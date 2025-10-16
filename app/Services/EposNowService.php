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
        $this->baseUrl = config('eposnow.api_base', 'https://api.eposnowhq.com/api/v4/');

        $this->apiKey = config('eposnow.api_key');
        $apiSecret = config('eposnow.api_secret');

        // $this->token = base64_encode($apiKey.':'.$apiSecret);
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Basic '.$this->apiKey,
            'Accept' => 'text/plain',
            // 'Content-Type' => 'application/json',
        ];
    }

    // public function getProducts()
    // {
    //     $url = "{$this->baseUrl}/Product?page=1";
    //     $response = Http::withHeaders($this->headers())->withoutVerifying()->get($url);

    //     if ($response->failed()) {
    //         Log::error('EposNow API failed', ['body' => $response->body()]);
    //         throw new \Exception('EposNow API Error: '.$response->body());
    //     }

    //     return $response->json();
    // }

    // public function getCategories()
    // {
    //     $url = "{$this->baseUrl}/Category?page=1";
    //     $response = Http::withHeaders($this->headers())->withoutVerifying()->get($url);

    //     if ($response->failed()) {
    //         Log::error('EposNow API failed', ['body' => $response->body()]);
    //         throw new \Exception('EposNow API Error: '.$response->body());
    //     }

    //     return $response->json();
    // }

    public function getProducts(int $page = 1)
    {
        $url = "{$this->baseUrl}/Product?page={$page}";

        return $this->makeRequest($url);
    }

    /**
     * ✅ Fetch all categories from EposNow API.
     */
    public function getCategories(int $page = 1)
    {
        $url = "{$this->baseUrl}/Category?page={$page}";

        return $this->makeRequest($url, ['page' => $page]);
    }

    public function uploadCategoryImage(int $categoryId, string $fileType, string $fileHash)
    {
        $url = "{$this->baseUrl}/CategoryImage";

        $payload = [
            [
                'categoryId' => $categoryId,
                'fileType' => $fileType,
                'fileHash' => $fileHash,
            ],
        ];

        $response = Http::withHeaders($this->headers())
            ->withoutVerifying()
            ->post($url, $payload);

        if ($response->failed()) {
            Log::error('EposNow CategoryImage upload failed', [
                'payload' => $payload,
                'body' => $response->body(),
            ]);
            throw new \Exception('EposNow API Error: '.$response->body());
        }

        return $response->json();
    }

    protected function makeRequest(string $url, array $params = [])
    {
        $response = Http::withHeaders($this->headers())
            ->withoutVerifying()
            ->get($url, $params);

        if ($response->failed()) {
            Log::error('EposNow API Request Failed', [
                'url' => $url,
                'params' => $params,
                'body' => $response->body(),
            ]);

            throw new \Exception('EposNow API Error: '.$response->body());
        }

        return $response->json();
    }
}
