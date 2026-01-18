<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Check if request is AJAX/JSON
     *
     * @param Request $request
     * @return bool
     */
    protected function isAjaxRequest(Request $request): bool
    {
        return $request->ajax() || $request->expectsJson() || $request->has('ajax');
    }

    /**
     * Return paginated JSON response with HTML
     *
     * @param LengthAwarePaginator $paginator
     * @param string $viewPath View path for rendering items
     * @param string $viewKey Key name for items in view (default: 'items')
     * @return JsonResponse
     */
    protected function paginatedJsonResponse(
        LengthAwarePaginator $paginator,
        string $viewPath,
        string $viewKey = 'items'
    ): JsonResponse {
        $html = view($viewPath, [$viewKey => $paginator])->render();
        
        $paginationHtml = '';
        if ($paginator->hasPages()) {
            $paginationHtml = '<div class="pagination-wrapper">' .
                view('components.pagination', ['paginator' => $paginator])->render() .
                '</div>';
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'pagination' => $paginationHtml,
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    /**
     * Return standardized success JSON response
     *
     * @param string $message Success message
     * @param mixed $data Optional data to include
     * @param int $status HTTP status code (default: 200)
     * @return JsonResponse
     */
    protected function jsonSuccess(string $message, $data = null, int $status = 200): JsonResponse
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
    protected function jsonError(string $message, ?string $errorCode = null, ?array $errors = null, int $status = 400): JsonResponse
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
    protected function jsonValidationError($errors, string $message = 'Validation failed'): JsonResponse
    {
        $errorArray = is_array($errors) ? $errors : $errors->errors();

        return $this->jsonError($message, 'VALIDATION_ERROR', $errorArray, 422);
    }
}
