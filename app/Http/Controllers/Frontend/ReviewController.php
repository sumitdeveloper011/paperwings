<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreReviewRequest;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Store a new product review
    public function store(StoreReviewRequest $request, $productSlug): JsonResponse
    {
        try {
            $product = Product::where('slug', $productSlug)->firstOrFail();

            if (Auth::check()) {
                $existingReview = ProductReview::where('product_id', $product->id)
                    ->where('user_id', Auth::id())
                    ->first();
                
                if ($existingReview) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already reviewed this product.'
                    ], 400);
                }
            }

            $verifiedPurchase = false;
            if (Auth::check()) {
                $verifiedPurchase = Order::where('user_id', Auth::id())
                    ->whereHas('items', function($query) use ($product) {
                        $query->where('product_id', $product->id);
                    })
                    ->where('status', '!=', 'cancelled')
                    ->exists();
            }

            $review = ProductReview::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'name' => Auth::check() ? Auth::user()->name : $request->name,
                'email' => Auth::check() ? Auth::user()->email : $request->email,
                'rating' => $request->rating,
                'review' => $request->review,
                'status' => 0,
                'verified_purchase' => $verifiedPurchase,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully. It will be published after admin approval.',
                'review' => $review
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mark review as helpful
    public function helpful(Request $request, $reviewId): JsonResponse
    {
        try {
            $review = ProductReview::findOrFail($reviewId);
            $review->increment('helpful_count');

            return response()->json([
                'success' => true,
                'helpful_count' => $review->helpful_count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update helpful count.'
            ], 500);
        }
    }
}
