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
}
