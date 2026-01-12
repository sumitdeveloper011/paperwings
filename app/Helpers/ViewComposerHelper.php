<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class ViewComposerHelper
{
    /**
     * Safely execute view composer callback with error handling
     *
     * @param callable $callback Callback function that returns array
     * @param array $defaults Default values to return on error
     * @return array
     */
    public static function safeExecute(callable $callback, array $defaults = []): array
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error('View composer error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return $defaults;
        }
    }
}
