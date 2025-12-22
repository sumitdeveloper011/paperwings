<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Store a new subscription
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address must not exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('email'),
            ], 422);
        }

        try {
            $email = strtolower(trim($request->email));

            // Check if email already exists
            $existingSubscription = Subscription::where('email', $email)->first();

            if ($existingSubscription) {
                if ($existingSubscription->status == 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This email is already subscribed to our newsletter.',
                    ], 409);
                } else {
                    // Reactivate the subscription
                    $existingSubscription->update([
                        'status' => 1,
                        'subscribed_at' => now(),
                        'unsubscribed_at' => null,
                    ]);

                    Log::info('Subscription reactivated', [
                        'email' => $email,
                        'ip' => $request->ip(),
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Thank you for subscribing! You will receive our latest offers and updates.',
                    ]);
                }
            }

            // Create new subscription
            $subscription = Subscription::create([
                'email' => $email,
                'status' => 1,
                'subscribed_at' => now(),
            ]);

            Log::info('New subscription created', [
                'email' => $email,
                'subscription_id' => $subscription->id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing! You will receive our latest offers and updates.',
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription error', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again later.',
            ], 500);
        }
    }

    /**
     * Unsubscribe an email
     */
    public function unsubscribe(Request $request, string $uuid): JsonResponse
    {
        try {
            $subscription = Subscription::where('uuid', $uuid)->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found.',
                ], 404);
            }

            $subscription->update([
                'status' => 0,
                'unsubscribed_at' => now(),
            ]);

            Log::info('Subscription unsubscribed', [
                'email' => $subscription->email,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'You have been successfully unsubscribed from our newsletter.',
            ]);

        } catch (\Exception $e) {
            Log::error('Unsubscribe error', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again later.',
            ], 500);
        }
    }
}

