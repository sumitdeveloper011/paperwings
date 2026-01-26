<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RateLimitTrackerService
{
    protected string $cachePrefix = 'eposnow_rate_limit';
    protected int $maxCallsPerMinute;
    protected int $safeThreshold;
    protected int $cooldownMinutes;

    public function __construct()
    {
        $this->maxCallsPerMinute = config('eposnow.rate_limit.max_calls_per_minute', 100);
        $this->safeThreshold = config('eposnow.rate_limit.safe_threshold', 80);
        $this->cooldownMinutes = config('eposnow.rate_limit.cooldown_minutes', 30);
    }

    /**
     * Track an API call and check if we're approaching rate limit
     *
     * @return array ['allowed' => bool, 'remaining' => int, 'delay' => float, 'message' => string]
     */
    public function trackCall(): array
    {
        $currentMinute = now()->format('Y-m-d H:i');
        $cacheKey = "{$this->cachePrefix}:{$currentMinute}";
        
        $calls = Cache::get($cacheKey, 0);
        $calls++;
        
        Cache::put($cacheKey, $calls, 120);
        
        $remaining = max(0, $this->maxCallsPerMinute - $calls);
        $percentage = ($calls / $this->maxCallsPerMinute) * 100;
        
        $allowed = $calls < $this->maxCallsPerMinute;
        $delay = $this->calculateDelay($percentage, $calls);
        
        $message = $allowed 
            ? "API calls: {$calls}/{$this->maxCallsPerMinute} ({$remaining} remaining)"
            : "Rate limit reached: {$calls}/{$this->maxCallsPerMinute} calls this minute";
        
        if (!$allowed) {
            Log::warning('EposNow Rate Limit Reached', [
                'calls' => $calls,
                'max' => $this->maxCallsPerMinute,
                'minute' => $currentMinute
            ]);
        }
        
        return [
            'allowed' => $allowed,
            'remaining' => $remaining,
            'delay' => $delay,
            'message' => $message,
            'calls' => $calls,
            'percentage' => $percentage
        ];
    }

    /**
     * Calculate adaptive delay based on current usage
     *
     * @param float $percentage Current usage percentage
     * @param int $calls Current number of calls
     * @return float Delay in seconds
     */
    protected function calculateDelay(float $percentage, int $calls): float
    {
        if ($percentage >= 90) {
            return 3.0;
        } elseif ($percentage >= 80) {
            return 2.5;
        } elseif ($percentage >= 70) {
            return 2.0;
        } elseif ($percentage >= 50) {
            return 1.5;
        } elseif ($percentage >= 30) {
            return 1.0;
        } else {
            return 0.5;
        }
    }

    /**
     * Check if we should pause due to rate limit
     *
     * @return bool
     */
    public function shouldPause(): bool
    {
        $currentMinute = now()->format('Y-m-d H:i');
        $cacheKey = "{$this->cachePrefix}:{$currentMinute}";
        $calls = Cache::get($cacheKey, 0);
        
        return $calls >= $this->safeThreshold;
    }

    /**
     * Get current rate limit status
     *
     * @return array
     */
    public function getStatus(): array
    {
        $currentMinute = now()->format('Y-m-d H:i');
        $cacheKey = "{$this->cachePrefix}:{$currentMinute}";
        $calls = Cache::get($cacheKey, 0);
        
        $remaining = max(0, $this->maxCallsPerMinute - $calls);
        $percentage = ($calls / $this->maxCallsPerMinute) * 100;
        
        return [
            'calls' => $calls,
            'max' => $this->maxCallsPerMinute,
            'remaining' => $remaining,
            'percentage' => $percentage,
            'is_safe' => $calls < $this->safeThreshold,
            'should_pause' => $calls >= $this->safeThreshold,
            'current_minute' => $currentMinute
        ];
    }

    /**
     * Check if we're in cooldown period (after hitting rate limit)
     *
     * @return array ['in_cooldown' => bool, 'cooldown_until' => string|null, 'minutes_remaining' => int|null]
     */
    public function checkCooldown(): array
    {
        $cooldownKey = "{$this->cachePrefix}:cooldown";
        $cooldownUntil = Cache::get($cooldownKey);
        
        if (!$cooldownUntil) {
            return [
                'in_cooldown' => false,
                'cooldown_until' => null,
                'minutes_remaining' => null
            ];
        }
        
        $cooldownTime = \Carbon\Carbon::parse($cooldownUntil);
        $now = now();
        
        if ($now->greaterThan($cooldownTime)) {
            Cache::forget($cooldownKey);
            return [
                'in_cooldown' => false,
                'cooldown_until' => null,
                'minutes_remaining' => null
            ];
        }
        
        $minutesRemaining = $now->diffInMinutes($cooldownTime, false);
        
        return [
            'in_cooldown' => true,
            'cooldown_until' => $cooldownUntil,
            'minutes_remaining' => max(0, $minutesRemaining)
        ];
    }

    /**
     * Set cooldown period after hitting rate limit
     *
     * @param int $minutes
     * @return void
     */
    public function setCooldown(int $minutes = null): void
    {
        $minutes = $minutes ?? $this->cooldownMinutes;
        $cooldownKey = "{$this->cachePrefix}:cooldown";
        $cooldownUntil = now()->addMinutes($minutes)->toDateTimeString();
        
        Cache::put($cooldownKey, $cooldownUntil, ($minutes + 5) * 60);
        
        Log::warning('EposNow Rate Limit Cooldown Set', [
            'cooldown_until' => $cooldownUntil,
            'minutes' => $minutes
        ]);
    }

    /**
     * Clear rate limit tracking (for testing/reset)
     *
     * @return void
     */
    public function clearTracking(): void
    {
        $currentMinute = now()->format('Y-m-d H:i');
        $cacheKey = "{$this->cachePrefix}:{$currentMinute}";
        Cache::forget($cacheKey);
        Cache::forget("{$this->cachePrefix}:cooldown");
    }

    /**
     * Get recommended delay before next API call
     *
     * @return float Delay in seconds
     */
    public function getRecommendedDelay(): float
    {
        $status = $this->getStatus();
        $cooldown = $this->checkCooldown();
        
        if ($cooldown['in_cooldown']) {
            return 60.0;
        }
        
        return $this->calculateDelay($status['percentage'], $status['calls']);
    }
}
