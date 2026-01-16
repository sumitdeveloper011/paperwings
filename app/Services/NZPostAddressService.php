<?php

namespace App\Services;

use App\Helpers\SettingHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NZPostAddressService
{
    private $apiKey;
    private $baseUrl = 'https://api.nzpost.co.nz/addresschecker/v1';

    public function __construct()
    {
        // Get API key from database settings with .env fallback
        $this->apiKey = SettingHelper::get('nzpost_api_key', config('services.nzpost.api_key'));
    }

    /**
     * Search addresses by query (autocomplete)
     * 
     * @param string $query Search query (e.g., "123 Queen Street")
     * @return array
     */
    public function searchAddresses(string $query): array
    {
        if (empty($this->apiKey)) {
            Log::warning('NZ Post API key not configured');
            return [];
        }

        if (empty(trim($query)) || strlen(trim($query)) < 3) {
            return [];
        }

        // Cache key based on query
        $cacheKey = 'nzpost_search_' . md5($query);
        
        // Check cache first (cache for 24 hours)
        return Cache::remember($cacheKey, 86400, function () use ($query) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])->get($this->baseUrl . '/addresses', [
                    'q' => $query,
                    'limit' => 10,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatAddressResults($data);
                }

                Log::warning('NZ Post API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error('NZ Post API error', [
                    'error' => $e->getMessage(),
                    'query' => $query
                ]);
                return [];
            }
        });
    }

    /**
     * Validate and get full address details by ID
     * 
     * @param string $addressId Address ID from search results
     * @return array|null
     */
    public function getAddressDetails(string $addressId): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $cacheKey = 'nzpost_address_' . md5($addressId);
        
        return Cache::remember($cacheKey, 86400, function () use ($addressId) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])->get($this->baseUrl . '/addresses/' . $addressId);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatAddressDetails($data);
                }

                return null;
            } catch (\Exception $e) {
                Log::error('NZ Post API error getting address details', [
                    'error' => $e->getMessage(),
                    'address_id' => $addressId
                ]);
                return null;
            }
        });
    }

    /**
     * Format search results for frontend
     * 
     * @param array $data API response data
     * @return array
     */
    private function formatAddressResults(array $data): array
    {
        $results = [];

        if (isset($data['addresses']) && is_array($data['addresses'])) {
            foreach ($data['addresses'] as $address) {
                $results[] = [
                    'id' => $address['id'] ?? null,
                    'display' => $this->formatAddressDisplay($address),
                    'street_address' => $address['address_line_1'] ?? '',
                    'suburb' => $address['suburb'] ?? '',
                    'city' => $address['city'] ?? '',
                    'region' => $address['region'] ?? '',
                    'postcode' => $address['postcode'] ?? '',
                ];
            }
        }

        return $results;
    }

    /**
     * Format address details for database storage
     * 
     * @param array $data API response data
     * @return array
     */
    private function formatAddressDetails(array $data): array
    {
        return [
            'street_address' => $data['address_line_1'] ?? '',
            'street_address_2' => $data['address_line_2'] ?? null,
            'suburb' => $data['suburb'] ?? null,
            'city' => $data['city'] ?? '',
            'region' => $data['region'] ?? '',
            'postcode' => $data['postcode'] ?? '',
            'country' => 'New Zealand',
        ];
    }

    /**
     * Format address for display in autocomplete
     * 
     * @param array $address Address data
     * @return string
     */
    private function formatAddressDisplay(array $address): string
    {
        $parts = [];
        
        if (!empty($address['address_line_1'])) {
            $parts[] = $address['address_line_1'];
        }
        if (!empty($address['suburb'])) {
            $parts[] = $address['suburb'];
        }
        if (!empty($address['city'])) {
            $parts[] = $address['city'];
        }
        if (!empty($address['postcode'])) {
            $parts[] = $address['postcode'];
        }

        return implode(', ', $parts);
    }
}
