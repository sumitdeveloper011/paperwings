<?php

namespace App\Http\Controllers\Admin\Review;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function updateStatus(Request $request, ProductReview $review): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:0,1,2'
        ]);

        $review->update(['status' => $request->status]);

        $statusText = match($request->status) {
            0 => 'pending',
            1 => 'approved',
            2 => 'rejected',
        };

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
