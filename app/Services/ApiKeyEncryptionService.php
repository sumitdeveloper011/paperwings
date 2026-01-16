<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class ApiKeyEncryptionService
{
    /**
     * List of keys that should be encrypted in database
     */
    private const ENCRYPTED_KEYS = [
        'stripe_secret',
        'stripe_webhook_secret',
        'google_client_secret',
        'facebook_client_secret',
        'eposnow_api_secret',
        'instagram_app_secret',
        'instagram_access_token',
        'nzpost_api_key',
        'google_map_api_key',
    ];

    /**
     * Encrypt a sensitive API key before storing
     *
     * @param string $key Setting key name
     * @param string|null $value Setting value
     * @return string|null Encrypted value or null
     */
    public static function encrypt(string $key, ?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (!self::shouldEncrypt($key)) {
            return $value;
        }

        try {
            return Crypt::encryptString($value);
        } catch (\Exception $e) {
            Log::error('Failed to encrypt API key', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $value;
        }
    }

    /**
     * Decrypt a sensitive API key when retrieving
     *
     * @param string $key Setting key name
     * @param string|null $value Encrypted value
     * @return string|null Decrypted value or null
     */
    public static function decrypt(string $key, ?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (!self::shouldEncrypt($key)) {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Key might be unencrypted (old data from before encryption was implemented)
            // Try to use as-is
            Log::warning('Failed to decrypt API key, using as-is (might be unencrypted)', [
                'key' => $key
            ]);
            return $value;
        } catch (\Exception $e) {
            Log::error('Failed to decrypt API key', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check if a key should be encrypted
     *
     * @param string $key Setting key name
     * @return bool
     */
    public static function shouldEncrypt(string $key): bool
    {
        return in_array($key, self::ENCRYPTED_KEYS);
    }

    /**
     * Get list of encrypted keys
     *
     * @return array
     */
    public static function getEncryptedKeys(): array
    {
        return self::ENCRYPTED_KEYS;
    }
}
