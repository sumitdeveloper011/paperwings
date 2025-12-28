<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    // Display FAQ page
    public function index(Request $request): View
    {
        $title = 'Frequently Asked Questions';
        $selectedCategory = $request->get('category');

        // Get all active FAQs
        $query = Faq::active()->ordered();

        // Filter by category if selected
        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }

        $faqs = $query->get();

        // Get all unique categories for filter
        $categories = Faq::active()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->map(function($category) {
                return [
                    'value' => $category,
                    'label' => ucfirst(str_replace('_', ' ', $category))
                ];
            });

        return view('frontend.faq.index', compact('title', 'faqs', 'categories', 'selectedCategory'));
    }
}
