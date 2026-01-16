<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UtilityController extends Controller
{
    public function logClientError(Request $request)
    {
        try {
            $errorData = $request->json()->all() ?? $request->all();
            
            // Skip logging if error has no meaningful information
            $hasErrorInfo = isset($errorData['message']) || 
                           isset($errorData['error']) || 
                           isset($errorData['stack']) || 
                           isset($errorData['filename']) ||
                           isset($errorData['lineno']);
            
            if (!$hasErrorInfo) {
                return response()->json([
                    'success' => true,
                    'message' => 'Skipped - no error information'
                ], 200);
            }
            
            $logData = [
                'error' => $errorData['error'] ?? $errorData['message'] ?? $errorData,
                'url' => $errorData['url'] ?? $request->header('referer') ?? $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'user_id' => auth()->check() ? auth()->id() : null,
                'timestamp' => now()->toISOString(),
            ];

            if (isset($errorData['stack']) && $errorData['stack']) {
                $logData['stack'] = $errorData['stack'];
            }

            if (isset($errorData['line']) && $errorData['line']) {
                $logData['line'] = $errorData['line'];
            }

            if (isset($errorData['file']) && $errorData['file']) {
                $logData['file'] = $errorData['file'];
            }

            if (isset($errorData['filename']) && $errorData['filename']) {
                $logData['filename'] = $errorData['filename'];
            }

            if (isset($errorData['lineno']) && $errorData['lineno']) {
                $logData['lineno'] = $errorData['lineno'];
            }

            try {
                Log::channel('client_errors')->warning('Client-side error', $logData);
            } catch (\Exception $logException) {
                Log::error('Failed to write to client_errors channel, using default', [
                    'exception' => $logException->getMessage(),
                    'data' => $logData
                ]);
                Log::warning('Client-side error (fallback)', $logData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Error logged successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to log client error', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to log error'
            ], 200);
        }
    }
}
