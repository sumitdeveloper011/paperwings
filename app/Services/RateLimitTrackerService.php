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
        $this->cooldownMinutes = config('eposnow.rate_limit.cooldown_minutes', 10);
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
                'minute' => $currentMinute,
                'percentage' => round($percentage, 2),
                'timestamp' => now()->toDateTimeString()
            ]);
            
            // Track rate limit hit for monitoring
            $this->trackRateLimitHit();
        }
        
        // Log high usage warnings (70%+)
        if ($percentage >= 70 && $percentage < 100) {
            Log::info('EposNow Rate Limit: High usage detected', [
                'calls' => $calls,
                'max' => $this->maxCallsPerMinute,
                'percentage' => round($percentage, 2),
                'minute' => $currentMinute,
                'remaining' => $remaining
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
     * Implements progressive cooldown: 5 min → 10 min → 30 min for repeat offenders
     *
     * @param int $minutes Optional override, otherwise uses progressive cooldown
     * @return void
     */
    public function setCooldown(int $minutes = null): void
    {
        if ($minutes === null) {
            // Progressive cooldown: check how many times we've hit rate limit recently
            $cooldownCountKey = "{$this->cachePrefix}:cooldown_count";
            $cooldownCount = Cache::get($cooldownCountKey, 0);
            
            // Reset count if last cooldown was more than 2 hours ago
            $lastCooldownKey = "{$this->cachePrefix}:last_cooldown_time";
            $lastCooldownTime = Cache::get($lastCooldownKey);
            if ($lastCooldownTime) {
                $lastCooldown = \Carbon\Carbon::parse($lastCooldownTime);
                if (now()->diffInHours($lastCooldown) > 2) {
                    $cooldownCount = 0;
                }
            }
            
            // Progressive cooldown: 5 min → 10 min → 30 min
            if ($cooldownCount === 0) {
                $minutes = 5;
            } elseif ($cooldownCount === 1) {
                $minutes = 10;
            } else {
                $minutes = 30;
            }
            
            // Increment cooldown count
            $cooldownCount++;
            Cache::put($cooldownCountKey, $cooldownCount, 7200); // 2 hours
            Cache::put($lastCooldownKey, now()->toDateTimeString(), 7200);
        }
        
        $cooldownKey = "{$this->cachePrefix}:cooldown";
        $cooldownUntil = now()->addMinutes($minutes)->toDateTimeString();
        
        Cache::put($cooldownKey, $cooldownUntil, ($minutes + 5) * 60);
        
        Log::warning('EposNow Rate Limit Cooldown Set', [
            'cooldown_until' => $cooldownUntil,
            'minutes' => $minutes,
            'cooldown_count' => $cooldownCount ?? 0
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
     * Implements smart rate limiting: auto-pause at 80%, resume at 70%
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
        
        // Smart rate limiting: auto-pause at 80% threshold
        if ($status['should_pause']) {
            Log::info('EposNow Rate Limit: Auto-pausing at safe threshold', [
                'percentage' => round($status['percentage'], 2),
                'calls' => $status['calls'],
                'threshold' => $this->safeThreshold,
                'timestamp' => now()->toDateTimeString()
            ]);
            // Wait until usage drops below 70% (resume threshold)
            return 10.0; // Wait 10 seconds before checking again
        }
        
        return $this->calculateDelay($status['percentage'], $status['calls']);
    }

    /**
     * Track rate limit hit for monitoring and analytics
     *
     * @return void
     */
    protected function trackRateLimitHit(): void
    {
        $today = now()->format('Y-m-d');
        $hitCountKey = "{$this->cachePrefix}:hits:{$today}";
        $hits = Cache::get($hitCountKey, 0);
        $hits++;
        
        Cache::put($hitCountKey, $hits, 86400); // Store for 24 hours
        
        // Log daily hit count for monitoring
        if ($hits === 1 || $hits % 5 === 0) {
            Log::warning('EposNow Rate Limit: Daily hit tracking', [
                'date' => $today,
                'total_hits_today' => $hits,
                'timestamp' => now()->toDateTimeString()
            ]);
        }
    }

    /**
     * Get rate limit statistics for monitoring
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $today = now()->format('Y-m-d');
        $hitCountKey = "{$this->cachePrefix}:hits:{$today}";
        $hitsToday = Cache::get($hitCountKey, 0);
        
        $status = $this->getStatus();
        $cooldown = $this->checkCooldown();
        
        return [
            'current_status' => $status,
            'cooldown' => $cooldown,
            'hits_today' => $hitsToday,
            'date' => $today,
            'configuration' => [
                'max_calls_per_minute' => $this->maxCallsPerMinute,
                'safe_threshold' => $this->safeThreshold,
                'cooldown_minutes' => $this->cooldownMinutes
            ]
        ];
    }

    /**
     * Check if we should wait before making next API call (smart rate limiting)
     * Returns wait time in seconds if we should pause, 0 if we can proceed
     *
     * @return float Wait time in seconds (0 if no wait needed)
     */
    public function getWaitTimeIfNeeded(): float
    {
        $status = $this->getStatus();
        
        // Auto-pause at 80% threshold
        if ($status['should_pause']) {
            // Calculate wait time until we're below 70% (resume threshold)
            $resumeThreshold = 70;
            $currentPercentage = $status['percentage'];
            
            if ($currentPercentage >= $this->safeThreshold) {
                // Wait until next minute starts (rate limit resets per minute)
                $secondsUntilNextMinute = 60 - (int)now()->format('s');
                return max(5.0, $secondsUntilNextMinute); // Minimum 5 seconds wait
            }
        }
        
        return 0.0;
    }
}
