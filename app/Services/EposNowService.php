<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EposNowService
{
    protected string $baseUrl;

    protected string $token;

    protected string $apiKey;

    public function __construct()
    {
        // Read from database settings, fallback to config/env
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        
        $this->baseUrl = rtrim(
            $settings['eposnow_api_base'] ?? config('eposnow.api_base', 'https://api.eposnowhq.com/api/v4/'),
            '/'
        );
        $this->apiKey = $settings['eposnow_api_key'] ?? config('eposnow.api_key');
        
        // If base URL doesn't end with /api/v4/, add it
        if (!str_ends_with($this->baseUrl, '/api/v4')) {
            $this->baseUrl = rtrim($this->baseUrl, '/') . '/api/v4';
        }
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

        while (true) {
            try {
                $products = $this->makeRequest(
                    "{$this->baseUrl}/Product",
                    ['page' => $page]
                );

                if (empty($products)) {
                    break;
                }

                $allProducts = array_merge($allProducts, $products);
                $page++;

                usleep(500000);

            } catch (\Exception $e) {
                // If rate limit error, throw it up so job can handle it
                if (stripos($e->getMessage(), 'rate limit') !== false ||
                    stripos($e->getMessage(), 'maximum API limit') !== false) {
                    throw $e;
                }

                Log::error('Error fetching products page', [
                    'page' => $page,
                    'error' => $e->getMessage()
                ]);
                break;
            }
        }

        return $allProducts;
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

        while (true) {
            try {
                $categories = $this->getCategories($page);

                if (empty($categories) || !is_array($categories)) {
                    break;
                }

                $allCategories = array_merge($allCategories, $categories);
                $page++;

                // Log progress for debugging
                if ($page % 5 == 0) {
                    Log::info('EPOSNOW Categories Import Progress', [
                        'pages_fetched' => $page - 1,
                        'total_categories' => count($allCategories)
                    ]);
                }

                usleep(500000);
            } catch (\Exception $e) {
                // If rate limit error, throw it up so job can handle it
                if (stripos($e->getMessage(), 'rate limit') !== false ||
                    stripos($e->getMessage(), 'maximum API limit') !== false) {
                    throw $e;
                }

                Log::error('EposNow Categories API Error', [
                    'page' => $page,
                    'error' => $e->getMessage()
                ]);
                break;
            }
        }

        return $allCategories;
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

    // Make HTTP request to EposNow API with retry logic
    protected function makeRequest(string $url, array $params = [], int $retries = 3, int $delay = 2): array
    {
        $attempt = 0;

        while ($attempt < $retries) {
            try {
                $response = Http::withHeaders($this->headers())
                    ->withoutVerifying()
                    ->timeout(60)
                    ->get($url, $params);

                if ($response->status() === 429) {
                    $attempt++;
                    $waitTime = $delay * pow(2, $attempt - 1);

                    Log::warning('EposNow API Rate Limit Hit', [
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

                    if (stripos($errorBody, 'maximum API limit') !== false ||
                        stripos($errorBody, 'rate limit') !== false ||
                        stripos($errorBody, 'too many requests') !== false) {
                        $errorMessage = 'EposNow API Rate Limit: You have reached your maximum API limit. Please wait and try again later.';
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
                    stripos($e->getMessage(), 'maximum API limit') !== false) {
                    $attempt++;
                    if ($attempt < $retries) {
                        $waitTime = $delay * pow(2, $attempt - 1);
                        Log::info('Retrying after rate limit error', [
                            'attempt' => $attempt,
                            'wait_time' => $waitTime
                        ]);
                        sleep(min($waitTime, 60));
                        continue;
                    }
                }

                throw $e;
            }
        }

        throw new \Exception('EposNow API Error: Maximum retries reached');
    }
}
