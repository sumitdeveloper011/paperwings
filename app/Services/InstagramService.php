<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class InstagramService
{
    private $appId;
    private $appSecret;
    private $accessToken;
    private $userId;
    private $baseUrl = 'https://graph.instagram.com';

    public function __construct()
    {
        $this->appId = Setting::get('instagram_app_id');
        $this->appSecret = Setting::get('instagram_app_secret');
        $this->accessToken = Setting::get('instagram_access_token');
        $this->userId = Setting::get('instagram_user_id');
    }

    // Check if Instagram API is configured
    public function isConfigured(): bool
    {
        return !empty($this->appId) && 
               !empty($this->appSecret) && 
               !empty($this->accessToken) && 
               !empty($this->userId);
    }

    // Get user profile information
    public function getUserProfile(): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/me", [
                'fields' => 'id,username,account_type,media_count',
                'access_token' => $this->accessToken
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Instagram API: Failed to fetch user profile', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Instagram API: Exception fetching user profile', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    // Get recent media posts from Instagram
    public function getRecentMedia(int $limit = 6): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        $cacheKey = "instagram_media_{$this->userId}_{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            try {
                $response = Http::timeout(10)->get("{$this->baseUrl}/{$this->userId}/media", [
                    'fields' => 'id,media_type,media_url,thumbnail_url,permalink,caption,timestamp,like_count,comments_count',
                    'limit' => min($limit, 25),
                    'access_token' => $this->accessToken
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['data']) && is_array($data['data'])) {
                        return $this->formatMediaData($data['data']);
                    }
                }

                Log::warning('Instagram API: Failed to fetch media', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error('Instagram API: Exception fetching media', [
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        });
    }

    // Format media data for frontend display
    private function formatMediaData(array $media): array
    {
        $formatted = [];

        foreach ($media as $item) {
            $imageUrl = $item['media_url'] ?? $item['thumbnail_url'] ?? null;
            
            if (!$imageUrl) {
                continue;
            }

            $formatted[] = [
                'id' => $item['id'] ?? null,
                'type' => $item['media_type'] ?? 'IMAGE',
                'image_url' => $imageUrl,
                'permalink' => $item['permalink'] ?? '#',
                'caption' => $this->truncateCaption($item['caption'] ?? ''),
                'timestamp' => $item['timestamp'] ?? null,
                'likes' => $item['like_count'] ?? 0,
                'comments' => $item['comments_count'] ?? 0,
            ];
        }

        return $formatted;
    }

    // Truncate caption to a reasonable length
    private function truncateCaption(?string $caption, int $maxLength = 100): string
    {
        if (empty($caption)) {
            return '';
        }

        if (strlen($caption) <= $maxLength) {
            return $caption;
        }

        return substr($caption, 0, $maxLength) . '...';
    }

    // Refresh the access token
    public function refreshAccessToken(): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/refresh_access_token", [
                'grant_type' => 'ig_refresh_token',
                'access_token' => $this->accessToken
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['access_token'])) {
                    // Update the access token in settings
                    Setting::set('instagram_access_token', $data['access_token']);
                    $this->accessToken = $data['access_token'];
                    
                    Cache::forget("instagram_media_{$this->userId}_*");
                    
                    Log::info('Instagram API: Access token refreshed successfully');
                    return true;
                }
            }

            Log::warning('Instagram API: Failed to refresh access token', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Instagram API: Exception refreshing access token', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // Test the API connection
    public function testConnection(): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'data' => null
        ];

        if (!$this->isConfigured()) {
            $result['message'] = 'Instagram API is not configured. Please add your credentials in settings.';
            return $result;
        }

        try {
            $profile = $this->getUserProfile();
            
            if ($profile) {
                $result['success'] = true;
                $result['message'] = 'Connection successful!';
                $result['data'] = [
                    'username' => $profile['username'] ?? 'N/A',
                    'account_type' => $profile['account_type'] ?? 'N/A',
                    'media_count' => $profile['media_count'] ?? 0,
                ];
            } else {
                $result['message'] = 'Failed to connect. Please check your credentials.';
            }
        } catch (\Exception $e) {
            $result['message'] = 'Error: ' . $e->getMessage();
        }

        return $result;
    }

    // Clear Instagram cache
    public function clearCache(): void
    {
        if ($this->userId) {
            for ($i = 1; $i <= 25; $i++) {
                Cache::forget("instagram_media_{$this->userId}_{$i}");
            }
        }
    }
}

