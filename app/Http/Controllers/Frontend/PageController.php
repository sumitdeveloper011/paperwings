<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display a page by slug (supports both direct slug and /page/{slug} format)
     * This method handles both new direct URLs and old /page/{slug} URLs
     */
    public function showBySlug(?string $slug = null): View
    {
        try {
            // If slug is not provided, extract it from the request path
            if (!$slug) {
                $slug = ltrim(request()->path(), '/');
            }

            $page = Page::where('slug', $slug)->first();

            if (!$page) {
                return view('frontend.errors.404', [
                    'title' => '404 - Page Not Found',
                    'message' => 'The page you are looking for does not exist.'
                ]);
            }

            $title = $page->title;

            return view('frontend.page.show', compact('title', 'page'));
        } catch (\Exception $e) {
            return view('frontend.errors.404', [
                'title' => '404 - Error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Legacy method for backward compatibility
     * @deprecated Use showBySlug instead
     */
    public function show(string $slug): View
    {
        return $this->showBySlug($slug);
    }
}

