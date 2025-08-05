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
     * Sanitize input
     */
    public static function sanitizeInput($input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
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