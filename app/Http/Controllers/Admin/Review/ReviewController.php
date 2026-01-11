<?php

namespace App\Http\Controllers\Admin\Review;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $productId = $request->get('product_id');

        $query = ProductReview::with(['product', 'user']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('review', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.review.index', compact('reviews', 'search', 'status', 'productId'));
    }

    public function show(ProductReview $review): View
    {
        $review->load(['product', 'user']);
        return view('admin.review.show', compact('review'));
    }

    public function updateStatus(Request $request, ProductReview $review): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:0,1,2'
        ]);

        // Cast status to integer
        $status = (int) $validated['status'];
        $review->update(['status' => $status]);

        $statusText = match($status) {
            0 => 'pending',
            1 => 'approved',
            2 => 'rejected',
        };

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Review {$statusText} successfully."
            ]);
        }

        return redirect()->route('admin.reviews.index')
            ->with('success', "Review {$statusText} successfully.");
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $review->delete();
        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
