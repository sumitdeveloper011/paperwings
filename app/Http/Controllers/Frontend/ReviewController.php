<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreReviewRequest;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // Store a new product review
    public function store(StoreReviewRequest $request, $productSlug): JsonResponse
    {
        try {
            $product = Product::where('slug', $productSlug)
                ->active()
                ->firstOrFail();

            if (Auth::check()) {
                $existingReview = ProductReview::where('product_id', $product->id)
                    ->where('user_id', Auth::id())
                    ->first();
                
                if ($existingReview) {
                    return $this->jsonError('You have already reviewed this product.', 'REVIEW_ALREADY_EXISTS', null, 400);
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

            // Create admin notification
            try {
                $this->notificationService->createReviewNotification($review);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to create review notification: ' . $e->getMessage());
            }

            return $this->jsonSuccess('Review submitted successfully. It will be published after admin approval.', [
                'review' => $review
            ]);

        } catch (\Exception $e) {
            return $this->jsonError('Failed to submit review. Please try again.', 'REVIEW_SUBMIT_ERROR', null, 500);
        }
    }

    // Mark review as helpful
    public function helpful(Request $request, $reviewId): JsonResponse
    {
        try {
            $review = ProductReview::findOrFail($reviewId);
            $review->increment('helpful_count');

            return $this->jsonSuccess('Helpful count updated.', [
                'helpful_count' => $review->helpful_count
            ]);

        } catch (\Exception $e) {
            return $this->jsonError('Failed to update helpful count.', 'REVIEW_HELPFUL_ERROR', null, 500);
        }
    }
}
