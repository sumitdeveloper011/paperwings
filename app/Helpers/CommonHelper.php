<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CommonHelper
{
    /**
     * Handle exception with environment check
     */
    public static function handleException(\Exception $e, $message = null, $context = []): array
    {
        $errorMessage = $message ?? $e->getMessage();
        
        // Log the error with context
        Log::error('Exception occurred: ' . $errorMessage, array_merge([
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], $context));

        // Return different responses based on environment
        if (app()->environment('production')) {
            return [
                'success' => false,
                'message' => 'An error occurred. Please try again later.',
                'error_code' => $e->getCode()
            ];
        } else {
            return [
                'success' => false,
                'message' => $errorMessage,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'error_code' => $e->getCode()
            ];
        }
    }

    /**
     * Try-catch wrapper for functions
     */
    public static function tryCatch(callable $callback, $errorMessage = null, $context = []): array
    {
        try {
            $result = $callback();
            return [
                'success' => true,
                'data' => $result
            ];
        } catch (\Exception $e) {
            return self::handleException($e, $errorMessage, $context);
        }
    }

    /**
     * Safe database operation with transaction
     */
    public static function safeDatabaseOperation(callable $callback, $errorMessage = null): array
    {
        return self::tryCatch(function () use ($callback) {
            return \DB::transaction($callback);
        }, $errorMessage, ['operation' => 'database']);
    }

    /**
     * Check if application is in production
     */
    public static function isProduction(): bool
    {
        return app()->environment('production');
    }

    /**
     * Check if application is in development
     */
    public static function isDevelopment(): bool
    {
        return app()->environment('local', 'development');
    }

    /**
     * Get environment-specific error message
     */
    public static function getErrorMessage(\Exception $e, $customMessage = null): string
    {
        if (self::isProduction()) {
            return $customMessage ?? 'An error occurred. Please try again later.';
        } else {
            return $customMessage ?? $e->getMessage();
        }
    }

    /**
     * Log error with environment context
     */
    public static function logError($message, $context = [], $level = 'error'): void
    {
        $context['environment'] = app()->environment();
        $context['timestamp'] = now()->toISOString();
        
        Log::{$level}($message, $context);
    }

    /**
     * Upload file with validation and error handling
     */
    public static function uploadFile($file, $path = 'uploads', $disk = 'public'): array
    {
        return self::tryCatch(function () use ($file, $path, $disk) {
            if (!$file || !$file->isValid()) {
                throw new \Exception('Invalid file');
            }

            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs($path, $fileName, $disk);

            return [
                'file_name' => $fileName,
                'file_path' => $filePath,
                'full_url' => Storage::disk($disk)->url($filePath),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ];
        }, 'File upload failed', ['file' => $file->getClientOriginalName()]);
    }

    /**
     * Delete file with error handling
     */
    public static function deleteFile($filePath, $disk = 'public'): array
    {
        return self::tryCatch(function () use ($filePath, $disk) {
            if (!Storage::disk($disk)->exists($filePath)) {
                throw new \Exception('File not found');
            }
            
            if (!Storage::disk($disk)->delete($filePath)) {
                throw new \Exception('Failed to delete file');
            }
            
            return true;
        }, 'File deletion failed', ['file_path' => $filePath]);
    }

    /**
     * Format currency
     */
    public static function formatCurrency($amount, $currency = 'USD'): string
    {
        return number_format($amount, 2) . ' ' . $currency;
    }

    /**
     * Sanitize input - Basic sanitization
     */
    public static function sanitizeInput($input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize email
     */
    public static function sanitizeEmail($email): string
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize URL
     */
    public static function sanitizeUrl($url): string
    {
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize phone number
     */
    public static function sanitizePhone($phone): string
    {
        return preg_replace('/[^0-9+\-\(\)\s]/', '', trim($phone));
    }

    /**
     * Sanitize name (letters, spaces, hyphens, apostrophes only)
     */
    public static function sanitizeName($name): string
    {
        return preg_replace('/[^a-zA-Z\s\-\']/', '', trim($name));
    }

    /**
     * Sanitize username (alphanumeric, underscores, hyphens only)
     */
    public static function sanitizeUsername($username): string
    {
        return preg_replace('/[^a-zA-Z0-9_-]/', '', trim($username));
    }

    /**
     * Sanitize array of inputs
     */
    public static function sanitizeArray($array): array
    {
        $sanitized = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } else {
                $sanitized[$key] = self::sanitizeInput($value);
            }
        }
        return $sanitized;
    }

    /**
     * Validate and sanitize request data
     */
    public static function validateAndSanitize(Request $request, array $rules, array $customMessages = []): array
    {
        $validator = Validator::make($request->all(), $rules, $customMessages);
        
        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ];
        }

        $sanitizedData = self::sanitizeArray($validator->validated());
        
        return [
            'success' => true,
            'data' => $sanitizedData
        ];
    }

    /**
     * Check for SQL injection attempts
     */
    public static function detectSqlInjection($input): bool
    {
        $sqlPatterns = [
            '/\b(union|select|insert|update|delete|drop|create|alter|exec|execute)\b/i',
            '/[\'";]/',
            '/--/',
            '/\/\*.*\*\//',
            '/xp_/i',
            '/sp_/i'
        ];

        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for XSS attempts
     */
    public static function detectXss($input): bool
    {
        $xssPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/<form/i',
            '/<input/i',
            '/<textarea/i',
            '/<select/i'
        ];

        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Secure password validation
     */
    public static function validatePassword($password): array
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Generate secure random token
     */
    public static function generateSecureToken($length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole($user, $role): bool
    {
        return $user && $user->hasRole($role);
    }

    /**
     * Check if user has any of the specified roles
     */
    public static function hasAnyRole($user, array $roles): bool
    {
        return $user && $user->hasAnyRole($roles);
    }

    /**
     * Check if user has all specified roles
     */
    public static function hasAllRoles($user, array $roles): bool
    {
        return $user && $user->hasAllRoles($roles);
    }

    /**
     * Log security event
     */
    public static function logSecurityEvent($event, $user = null, $context = []): void
    {
        $logData = array_merge([
            'event' => $event,
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->email : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ], $context);

        Log::channel('security')->info('Security Event: ' . $event, $logData);
    }

    /**
     * Send email with error handling
     */
    public static function sendEmail($to, $subject, $view, $data = []): array
    {
        return self::tryCatch(function () use ($to, $subject, $view, $data) {
            \Mail::send($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
            return true;
        }, 'Email sending failed', ['to' => $to, 'subject' => $subject]);
    }

    /**
     * Validate phone number
     */
    public static function isValidPhone($phone): bool
    {
        return preg_match('/^[\+]?[1-9][\d]{0,15}$/', $phone);
    }

    /**
     * Mask sensitive data
     */
    public static function maskData($data, $type = 'email'): string
    {
        switch ($type) {
            case 'email':
                $parts = explode('@', $data);
                if (count($parts) === 2) {
                    $username = $parts[0];
                    $domain = $parts[1];
                    $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
                    return $maskedUsername . '@' . $domain;
                }
                break;
            case 'phone':
                return substr($data, 0, 3) . str_repeat('*', strlen($data) - 5) . substr($data, -2);
            case 'card':
                return str_repeat('*', strlen($data) - 4) . substr($data, -4);
        }
        
        return $data;
    }
} 