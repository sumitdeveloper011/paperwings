<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

/**
 * JsonResponseHelper
 * 
 * Helper class for standardized JSON responses that can be used
 * anywhere in the application, including exception handlers.
 */
class JsonResponseHelper
{
    /**
     * Return standardized success JSON response
     *
     * @param string $message Success message
     * @param mixed $data Optional data to include
     * @param int $status HTTP status code (default: 200)
     * @return JsonResponse
     */
    public static function success(string $message, $data = null, int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Return standardized error JSON response
     *
     * @param string $message Error message
     * @param string|null $errorCode Optional error code for client-side handling
     * @param array|null $errors Optional validation errors
     * @param int $status HTTP status code (default: 400)
     * @return JsonResponse
     */
    public static function error(string $message, ?string $errorCode = null, ?array $errors = null, int $status = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errorCode !== null) {
            $response['error_code'] = $errorCode;
        }

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Return standardized validation error JSON response
     *
     * @param \Illuminate\Contracts\Validation\Validator|array $errors Validation errors
     * @param string $message Optional custom message
     * @return JsonResponse
     */
    public static function validationError($errors, string $message = 'Validation failed'): JsonResponse
    {
        $errorArray = is_array($errors) ? $errors : $errors->errors();

        return self::error($message, 'VALIDATION_ERROR', $errorArray, 422);
    }
}
