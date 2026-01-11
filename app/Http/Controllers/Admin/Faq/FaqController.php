<?php

namespace App\Http\Controllers\Admin\Faq;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Http\Requests\Admin\Faq\StoreFaqRequest;
use App\Http\Requests\Admin\Faq\UpdateFaqRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');
        $category = $request->get('category');

        $query = Faq::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        $faqs = $query->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Faq::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            // Only show pagination if there are FAQs and multiple pages
            if ($faqs->total() > 0 && $faqs->hasPages()) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $faqs
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.faq.partials.table', compact('faqs'))->render(),
                'pagination' => $paginationHtml
            ]);
        }

        return view('admin.faq.index', compact('faqs', 'search', 'category', 'categories'));
    }

    public function create(): View
    {
        return view('admin.faq.create');
    }

    public function store(StoreFaqRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ created successfully!');
    }

    public function show(Faq $faq): View
    {
        return view('admin.faq.show', compact('faq'));
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faq.edit', compact('faq'));
    }

    public function update(UpdateFaqRequest $request, Faq $faq): RedirectResponse
    {
        $validated = $request->validated();

        $faq->update($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ updated successfully!');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ deleted successfully!');
    }

    public function updateStatus(Request $request, Faq $faq): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:0,1'
        ]);

        // Cast status to integer
        $status = (int) $validated['status'];
        $faq->update(['status' => $status]);

        $statusText = $status == 1 ? 'activated' : 'deactivated';

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "FAQ {$statusText} successfully!"
            ]);
        }

        return redirect()->back()
            ->with('success', "FAQ {$statusText} successfully!");
    }
}
