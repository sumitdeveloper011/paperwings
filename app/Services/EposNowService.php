<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EposNowService
{
    protected string $baseUrl;

    protected string $token;

    protected string $apiKey;

    protected RateLimitTrackerService $rateLimitTracker;

    public function __construct(RateLimitTrackerService $rateLimitTracker = null)
    {
        // Read from .env config file
        $this->baseUrl = rtrim(
            config('eposnow.api_base', 'https://api.eposnowhq.com/api/v4/'),
            '/'
        );
        $this->apiKey = config('eposnow.api_key');

        // If base URL doesn't end with /api/v4/, add it
        if (!str_ends_with($this->baseUrl, '/api/v4')) {
            $this->baseUrl = rtrim($this->baseUrl, '/') . '/api/v4';
        }

        $this->rateLimitTracker = $rateLimitTracker ?? app(RateLimitTrackerService::class);
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Basic '.$this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    // Get all products from EposNow API
    public function getAllProducts(): array
    {
        $page = 1;
        $allProducts = [];
        $maxPages = 1000; // Safety limit to prevent infinite loops
        $emptyPageCount = 0;
        $maxEmptyPages = 2; // Stop after 2 consecutive empty pages
        $previousPageIds = []; // Track IDs from previous page to detect duplicates
        $duplicatePageCount = 0;
        $maxDuplicatePages = 2; // Stop after 2 consecutive duplicate pages

        while ($page <= $maxPages) {
            try {
                $products = $this->makeRequest(
                    "{$this->baseUrl}/Product",
                    ['page' => $page]
                );

                // Check if response is valid array
                if (!is_array($products)) {
                    Log::warning('EposNow Products API returned non-array response', [
                        'page' => $page,
                        'response_type' => gettype($products),
                        'response' => $products
                    ]);
                    break;
                }

                // If empty page, increment empty counter
                if (empty($products) || count($products) === 0) {
                    $emptyPageCount++;
                    if (app()->environment('local') || app()->environment('development')) {
                        Log::info('EPOSNOW Products Import: Empty page detected', [
                            'page' => $page,
                            'empty_page_count' => $emptyPageCount,
                            'total_products_so_far' => count($allProducts)
                        ]);
                    }

                    if ($emptyPageCount >= $maxEmptyPages) {
                        if (app()->environment('local') || app()->environment('development')) {
                            Log::info('EPOSNOW Products Import: Reached end of pagination (empty pages)', [
                                'pages_fetched' => $page - 1,
                                'total_products' => count($allProducts)
                            ]);
                        }
                        break;
                    }
                    $page++;
                    continue;
                }

                // Reset empty counter if we got data
                $emptyPageCount = 0;

                // Check for duplicate data
                $duplicateCheck = $this->checkForDuplicatePage(
                    $products,
                    $previousPageIds,
                    $duplicatePageCount,
                    $maxDuplicatePages,
                    $page,
                    'Products',
                    count($allProducts)
                );

                if ($duplicateCheck === 'stop') {
                    break;
                } elseif ($duplicateCheck === 'skip') {
                    $page++;
                    continue;
                }

                // Reset duplicate counter if we got new data
                $duplicatePageCount = 0;
                $previousPageIds = $duplicateCheck;

                // Merge products
                $allProducts = array_merge($allProducts, $products);

                // Log progress for debugging (every page for first 10, then every 5) - only in local environment
                if (app()->environment('local') || app()->environment('development') && ($page <= 10 || $page % 5 == 0)) {
                    Log::info('EPOSNOW Products Import Progress', [
                        'page' => $page,
                        'products_on_page' => count($products),
                        'total_products' => count($allProducts),
                        'sample_ids' => is_array($duplicateCheck) ? array_slice($duplicateCheck, 0, 3) : [] // Log first 3 IDs
                    ]);
                }

                $page++;
                usleep(500000); // 0.5 second delay between requests

            } catch (\Exception $e) {
                // If rate limit error and we have data, return partial data
                if ((stripos($e->getMessage(), 'rate limit') !== false ||
                    stripos($e->getMessage(), 'maximum API limit') !== false) &&
                    count($allProducts) > 0) {
                    Log::warning('EposNow Products API Rate Limit: Returning partial data', [
                        'products_fetched' => count($allProducts),
                        'last_page' => $page - 1,
                        'error' => $e->getMessage()
                    ]);
                    return $allProducts;
                }

                // If rate limit error but no data, throw it up so job can handle it
                if (stripos($e->getMessage(), 'rate limit') !== false ||
                    stripos($e->getMessage(), 'maximum API limit') !== false) {
                    throw $e;
                }

                Log::error('EposNow Products API Error', [
                    'page' => $page,
                    'error' => $e->getMessage(),
                    'total_products_so_far' => count($allProducts)
                ]);

                // If we have some data, return it instead of failing completely
                if (count($allProducts) > 0) {
                    Log::warning('EposNow Products API Error but returning partial data', [
                        'products_fetched' => count($allProducts),
                        'last_page' => $page - 1
                    ]);
                    return $allProducts;
                }

                break;
            }
        }

        if ($page > $maxPages) {
            Log::warning('EPOSNOW Products Import: Reached maximum page limit', [
                'max_pages' => $maxPages,
                'total_products' => count($allProducts)
            ]);
        }

        // Remove duplicates based on Id before returning
        $uniqueProducts = [];
        $seenIds = [];

        foreach ($allProducts as $product) {
            $productId = isset($product['Id']) ? (string)$product['Id'] : null;
            if ($productId && !isset($seenIds[$productId])) {
                $seenIds[$productId] = true;
                $uniqueProducts[] = $product;
            }
        }

        if (app()->environment('local') && app()->environment('development')) {
            Log::info('EPOSNOW Products Import: Completed fetching', [
                'total_pages' => $page - 1,
                'total_products_fetched' => count($allProducts),
                'unique_products' => count($uniqueProducts),
                'duplicates_removed' => count($allProducts) - count($uniqueProducts)
            ]);
        }

        return $uniqueProducts;
    }

    // Fetch all categories from EposNow API
    public function getCategories(int $page = 1): array
    {
        $url = "{$this->baseUrl}/Category?page={$page}";

        return $this->makeRequest($url);
    }

    // Fetch all categories from EposNow API (all pages with pagination)
    public function getAllCategories(): array
    {
        $page = 1;
        $allCategories = [];
        $maxPages = 1000; // Safety limit to prevent infinite loops
        $emptyPageCount = 0;
        $maxEmptyPages = 2; // Stop after 2 consecutive empty pages
        $previousPageIds = []; // Track IDs from previous page to detect duplicates
        $duplicatePageCount = 0;
        $maxDuplicatePages = 2; // Stop after 2 consecutive duplicate pages

        while ($page <= $maxPages) {
            try {
                $categories = $this->getCategories($page);

                // Check if response is valid array
                if (!is_array($categories)) {
                    Log::warning('EposNow Categories API returned non-array response', [
                        'page' => $page,
                        'response_type' => gettype($categories),
                        'response' => $categories
                    ]);
                    break;
                }

                // If empty page, increment empty counter
                if (empty($categories) || count($categories) === 0) {
                    $emptyPageCount++;
                    Log::info('EPOSNOW Categories Import: Empty page detected', [
                        'page' => $page,
                        'empty_page_count' => $emptyPageCount,
                        'total_categories_so_far' => count($allCategories)
                    ]);

                    if ($emptyPageCount >= $maxEmptyPages) {
                        Log::info('EPOSNOW Categories Import: Reached end of pagination (empty pages)', [
                            'pages_fetched' => $page - 1,
                            'total_categories' => count($allCategories)
                        ]);
                        break;
                    }
                    $page++;
                    continue;
                }

                // Reset empty counter if we got data
                $emptyPageCount = 0;

                // Check for duplicate data
                $duplicateCheck = $this->checkForDuplicatePage(
                    $categories,
                    $previousPageIds,
                    $duplicatePageCount,
                    $maxDuplicatePages,
                    $page,
                    'Categories',
                    count($allCategories)
                );

                if ($duplicateCheck === 'stop') {
                    break;
                } elseif ($duplicateCheck === 'skip') {
                    $page++;
                    continue;
                }

                // Reset duplicate counter if we got new data
                $duplicatePageCount = 0;
                $previousPageIds = $duplicateCheck;

                // Merge categories
                $allCategories = array_merge($allCategories, $categories);

                // Log progress for debugging (every page for first 10, then every 5) - only in local environment
                if (app()->environment('local') && app()->environment('development') && ($page <= 10 || $page % 5 == 0)) {
                    Log::info('EPOSNOW Categories Import Progress', [
                        'page' => $page,
                        'categories_on_page' => count($categories),
                        'total_categories' => count($allCategories),
                        'sample_ids' => is_array($duplicateCheck) ? array_slice($duplicateCheck, 0, 3) : [] // Log first 3 IDs
                    ]);
                }

                $page++;
                usleep(500000); // 0.5 second delay between requests
            } catch (\Exception $e) {
                // If rate limit error and we have data, return partial data
                if ((stripos($e->getMessage(), 'rate limit') !== false ||
                    stripos($e->getMessage(), 'maximum API limit') !== false) &&
                    count($allCategories) > 0) {
                    Log::warning('EposNow Categories API Rate Limit: Returning partial data', [
                        'categories_fetched' => count($allCategories),
                        'last_page' => $page - 1,
                        'error' => $e->getMessage()
                    ]);
                    return $allCategories;
                }

                // If rate limit error but no data, throw it up so job can handle it
                if (stripos($e->getMessage(), 'rate limit') !== false ||
                    stripos($e->getMessage(), 'maximum API limit') !== false) {
                    throw $e;
                }

                Log::error('EposNow Categories API Error', [
                    'page' => $page,
                    'error' => $e->getMessage(),
                    'total_categories_so_far' => count($allCategories)
                ]);

                // If we have some data, return it instead of failing completely
                if (count($allCategories) > 0) {
                    Log::warning('EposNow Categories API Error but returning partial data', [
                        'categories_fetched' => count($allCategories),
                        'last_page' => $page - 1
                    ]);
                    return $allCategories;
                }

                throw $e;
            }
        }

        if ($page > $maxPages) {
            Log::warning('EPOSNOW Categories Import: Reached maximum page limit', [
                'max_pages' => $maxPages,
                'total_categories' => count($allCategories)
            ]);
        }

        // Remove duplicates based on Id before returning
        $uniqueCategories = [];
        $seenIds = [];

        foreach ($allCategories as $cat) {
            $catId = isset($cat['Id']) ? (string)$cat['Id'] : null;
            if ($catId && !isset($seenIds[$catId])) {
                $seenIds[$catId] = true;
                $uniqueCategories[] = $cat;
            }
        }

        if (app()->environment('local') && app()->environment('development')) {
            Log::info('EPOSNOW Categories Import: Completed fetching', [
                'total_pages' => $page - 1,
                'total_categories_fetched' => count($allCategories),
                'unique_categories' => count($uniqueCategories),
                'duplicates_removed' => count($allCategories) - count($uniqueCategories)
            ]);
        }

        return $uniqueCategories;
    }

    // Get product images from EposNow API
    public function getProductImages(string $productId): array
    {
        try {
            $url = "{$this->baseUrl}/ProductImage/{$productId}";
            $response = $this->makeRequest($url);

            if (empty($response)) {
                return [];
            }

            if (is_array($response) && isset($response[0])) {
                foreach ($response as $productData) {
                    if (isset($productData['ProductId']) && (string)$productData['ProductId'] === (string)$productId) {
                        if (!empty($productData['ImageUrls']) && is_array($productData['ImageUrls'])) {
                            return $productData['ImageUrls'];
                        }
                    }
                }
                return [];
            }

            if (isset($response['ImageUrls']) && is_array($response['ImageUrls'])) {
                return $response['ImageUrls'];
            }

            if (is_array($response) && isset($response[0]['ImageUrl'])) {
                return $response;
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get product stock from EposNow API
     *
     * @param int $productId EposNow product ID
     * @return int|null Total current stock (sum of all batches) or null if not found/error
     */
    public function getProductStock(int $productId): ?int
    {
        try {
            $url = "{$this->baseUrl}/ProductStock/Product/{$productId}";
            $response = $this->makeRequest($url);

            if (empty($response) || !is_array($response)) {
                return null;
            }

            $totalStock = 0;

            foreach ($response as $stockData) {
                if (!isset($stockData['productStockBatches']) || !is_array($stockData['productStockBatches'])) {
                    continue;
                }

                foreach ($stockData['productStockBatches'] as $batch) {
                    if (isset($batch['currentStock']) && is_numeric($batch['currentStock'])) {
                        $totalStock += (int) $batch['currentStock'];
                    }
                }
            }

            return $totalStock > 0 ? $totalStock : null;
        } catch (\Exception $e) {
            Log::warning('EposNow Stock API Error', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    // Download image from URL and save to storage
    public function downloadAndSaveImage(string $imageUrl, string $productId, bool $isMainImage = false): ?string
    {
        try {
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            $extension = $extension ?: 'jpg';

            $filename = $productId . '_' . ($isMainImage ? 'main' : 'image') . '_' . time() . '.' . $extension;
            $savePath = "products/{$filename}";
            $localFullPath = storage_path('app/public/' . $savePath);

            $directory = dirname($localFullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            $success = false;
            $errorMessage = '';

            try {
                $response = Http::timeout(60)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
                        'Accept-Language' => 'en-US,en;q=0.9',
                        'Referer' => 'https://www.eposnow.com/',
                    ])
                    ->withoutVerifying()
                    ->sink($localFullPath)
                    ->get($imageUrl);

                if ($response->successful() && file_exists($localFullPath) && filesize($localFullPath) > 0) {
                    $success = true;
                } else {
                    $errorMessage = "HTTP Status: {$response->status()}, Body: " . substr($response->body(), 0, 200);
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
            }

            // Method 2: If first method failed, try with cURL (better for S3)
            if (!$success && file_exists($localFullPath)) {
                @unlink($localFullPath);
            }

            if (!$success && function_exists('curl_init')) {
                try {
                    $ch = curl_init($imageUrl);
                    $fp = fopen($localFullPath, 'wb');

                    curl_setopt_array($ch, [
                        CURLOPT_FILE => $fp,
                        CURLOPT_HEADER => false,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_TIMEOUT => 60,
                        CURLOPT_CONNECTTIMEOUT => 30,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        CURLOPT_HTTPHEADER => [
                            'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
                            'Accept-Language: en-US,en;q=0.9',
                        ],
                    ]);

                    $curlSuccess = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curlError = curl_error($ch);

                    curl_close($ch);
                    fclose($fp);

                    if ($curlSuccess && $httpCode == 200 && file_exists($localFullPath) && filesize($localFullPath) > 0) {
                        $success = true;
                    } else {
                        if (file_exists($localFullPath)) {
                            @unlink($localFullPath);
                        }
                        $errorMessage .= " | cURL HTTP {$httpCode}: {$curlError}";
                    }
                } catch (\Exception $e) {
                    $errorMessage .= " | cURL error: " . $e->getMessage();
                }
            }

            // Method 3: Fallback to file_get_contents
            if (!$success && file_exists($localFullPath)) {
                @unlink($localFullPath);
            }

            if (!$success) {
                try {
                    $context = stream_context_create([
                        'http' => [
                            'method' => 'GET',
                            'header' => [
                                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                                'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
                            ],
                            'timeout' => 60,
                            'follow_location' => true,
                            'ignore_errors' => true,
                        ],
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ]);

                    $imageData = @file_get_contents($imageUrl, false, $context);

                    if ($imageData !== false && strlen($imageData) > 0) {
                        file_put_contents($localFullPath, $imageData);
                        if (file_exists($localFullPath) && filesize($localFullPath) > 0) {
                            $success = true;
                        }
                    }
                } catch (\Exception $e) {
                    $errorMessage .= " | file_get_contents error: " . $e->getMessage();
                }
            }

            if ($success && file_exists($localFullPath) && filesize($localFullPath) > 0) {
                return $savePath;
            }

            // Log error for debugging
            if (!empty($errorMessage)) {
                Log::warning('Image download failed', [
                    'url' => $imageUrl,
                    'product_id' => $productId,
                    'error' => $errorMessage,
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Image download exception', [
                'url' => $imageUrl,
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    // Make HTTP request to EposNow API with retry logic and rate limit tracking
    protected function makeRequest(string $url, array $params = [], int $retries = 3, int $delay = 2): array
    {
        $attempt = 0;

        while ($attempt < $retries) {
            try {
                $cooldown = $this->rateLimitTracker->checkCooldown();
                if ($cooldown['in_cooldown']) {
                    throw new \Exception('EposNow API Rate Limit: In cooldown period. Please wait ' . $cooldown['minutes_remaining'] . ' more minutes.');
                }

                $rateLimitStatus = $this->rateLimitTracker->trackCall();
                
                if (!$rateLimitStatus['allowed']) {
                    $this->rateLimitTracker->setCooldown();
                    throw new \Exception('EposNow API Rate Limit: Maximum calls per minute reached. Please wait and try again later.');
                }

                if ($rateLimitStatus['delay'] > 0) {
                    usleep((int)($rateLimitStatus['delay'] * 1000000));
                }

                $response = Http::withHeaders($this->headers())
                    ->withoutVerifying()
                    ->timeout(60)
                    ->get($url, $params);

                if ($response->status() === 403) {
                    $errorBody = $response->body();
                    $this->rateLimitTracker->setCooldown(30);
                    
                    Log::error('EposNow API Rate Limit Hit (HTTP 403)', [
                        'url' => $url,
                        'params' => $params,
                        'body' => $errorBody,
                        'cooldown_minutes' => 30
                    ]);
                    
                    throw new \Exception('EposNow API Rate Limit (HTTP 403): Maximum daily limit reached. Cooldown period: 30 minutes.');
                }

                if ($response->status() === 429) {
                    $this->rateLimitTracker->setCooldown();
                    $attempt++;
                    $waitTime = $delay * pow(2, $attempt - 1);

                    Log::warning('EposNow API Rate Limit Hit (HTTP 429)', [
                        'url' => $url,
                        'attempt' => $attempt,
                        'wait_time' => $waitTime,
                        'retry_after' => $response->header('Retry-After', $waitTime)
                    ]);

                    if ($attempt < $retries) {
                        $retryAfter = (int) $response->header('Retry-After', $waitTime);
                        sleep(min($retryAfter, 60));
                        continue;
                    } else {
                        throw new \Exception('EposNow API Rate Limit: Maximum retries reached. Please try again later.');
                    }
                }

                if ($response->failed()) {
                    $errorBody = $response->body();
                    $errorMessage = 'EposNow API Error';

                    if ($response->status() === 403 ||
                        stripos($errorBody, 'maximum API limit') !== false ||
                        stripos($errorBody, 'rate limit') !== false ||
                        stripos($errorBody, 'too many requests') !== false) {
                        $this->rateLimitTracker->setCooldown(30);
                        $errorMessage = 'EposNow API Rate Limit (HTTP 403): You have reached your maximum API limit. Please wait 30 minutes and try again.';
                    } else {
                        $errorMessage .= ': ' . $errorBody;
                    }

                    Log::error('EposNow API Request Failed', [
                        'url' => $url,
                        'params' => $params,
                        'status' => $response->status(),
                        'body' => $errorBody,
                    ]);

                    throw new \Exception($errorMessage);
                }

                return $response->json();

            } catch (\Exception $e) {
                if (stripos($e->getMessage(), 'rate limit') !== false ||
                    stripos($e->getMessage(), 'maximum API limit') !== false ||
                    stripos($e->getMessage(), 'cooldown period') !== false) {
                    $this->rateLimitTracker->setCooldown();
                    $attempt++;
                    if ($attempt < $retries) {
                        $waitTime = $delay * pow(2, $attempt - 1);
                        if (app()->environment('local') && app()->environment('development')) {
                            Log::info('Retrying after rate limit error', [
                                'attempt' => $attempt,
                                'wait_time' => $waitTime
                            ]);
                        }
                        sleep(min($waitTime, 60));
                        continue;
                    }
                }

                throw $e;
            }
        }

        throw new \Exception('EposNow API Error: Maximum retries reached');
    }

    /**
     * Check if API limit is available by making a test request
     */
    public function checkApiLimit(): array
    {
        try {
            // Make a single test request to check API status
            $response = Http::withHeaders($this->headers())
                ->withoutVerifying()
                ->timeout(10)
                ->get("{$this->baseUrl}/Category?page=1");

            if ($response->status() === 403) {
                $body = $response->body();
                if (stripos($body, 'maximum API limit') !== false ||
                    stripos($body, 'rate limit') !== false ||
                    stripos($body, 'too many requests') !== false) {
                    return [
                        'available' => false,
                        'message' => 'API rate limit has been reached. Please wait before trying again.',
                        'status' => 403
                    ];
                }
            }

            if ($response->failed()) {
                return [
                    'available' => false,
                    'message' => 'API request failed. Please check your API credentials.',
                    'status' => $response->status()
                ];
            }

            return [
                'available' => true,
                'message' => 'API is available',
                'status' => $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'available' => false,
                'message' => 'Error checking API: ' . $e->getMessage(),
                'status' => 0
            ];
        }
    }

    /**
     * Check for duplicate page data
     *
     * @param array $items Current page items
     * @param array $previousPageIds IDs from previous page
     * @param int $duplicatePageCount Current duplicate page count (passed by reference)
     * @param int $maxDuplicatePages Maximum allowed duplicate pages
     * @param int $page Current page number
     * @param string $type Import type ('Products' or 'Categories')
     * @param int $totalItems Total items collected so far
     * @return array|string Returns current page IDs array, 'stop', or 'skip'
     */
    private function checkForDuplicatePage(
        array $items,
        array $previousPageIds,
        int &$duplicatePageCount,
        int $maxDuplicatePages,
        int $page,
        string $type,
        int $totalItems
    ) {
        // Extract IDs from current page
        $currentPageIds = [];
        foreach ($items as $item) {
            if (isset($item['Id'])) {
                $currentPageIds[] = (string)$item['Id'];
            }
        }

        // Check if current page has same IDs as previous page (duplicate data)
        if (!empty($previousPageIds) && !empty($currentPageIds)) {
            $isDuplicate = count(array_intersect($previousPageIds, $currentPageIds)) === count($currentPageIds)
                && count($currentPageIds) === count($previousPageIds);

            if ($isDuplicate) {
                $duplicatePageCount++;
                Log::warning("EPOSNOW {$type} Import: Duplicate page detected", [
                    'page' => $page,
                    'duplicate_page_count' => $duplicatePageCount,
                    'item_ids' => array_slice($currentPageIds, 0, 5) // Log first 5 IDs
                ]);

                if ($duplicatePageCount >= $maxDuplicatePages) {
                    if (app()->environment('local') || app()->environment('development')) {
                        Log::info("EPOSNOW {$type} Import: Stopping due to duplicate pages", [
                            'pages_fetched' => $page - 1,
                            'total_items' => $totalItems,
                            'last_page' => $page - 1
                        ]);
                    }
                    return 'stop';
                }
                return 'skip';
            }
        }

        return $currentPageIds;
    }
}
