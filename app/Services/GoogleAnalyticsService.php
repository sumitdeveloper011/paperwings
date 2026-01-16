<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GoogleAnalyticsService
{
    protected ?string $gaId;
    protected bool $enabled;
    protected bool $ecommerceEnabled;

    public function __construct()
    {
        $settings = \App\Helpers\SettingHelper::all();
        $this->gaId = $settings['google_analytics_id'] ?? null;
        $this->enabled = isset($settings['google_analytics_enabled']) && $settings['google_analytics_enabled'] == '1';
        $this->ecommerceEnabled = isset($settings['google_analytics_ecommerce']) && $settings['google_analytics_ecommerce'] == '1';
    }

    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->gaId);
    }

    public function isEcommerceEnabled(): bool
    {
        return $this->isEnabled() && $this->ecommerceEnabled;
    }

    public function getGaId(): ?string
    {
        return $this->gaId;
    }

    public function trackEvent(string $eventName, array $parameters = []): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $this->sendToMeasurementProtocol('event', $eventName, $parameters);
        } catch (\Exception $e) {
            Log::warning('Google Analytics event tracking failed', [
                'event' => $eventName,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function trackEcommerce(string $eventName, array $data): void
    {
        if (!$this->isEcommerceEnabled()) {
            return;
        }

        try {
            $this->sendToMeasurementProtocol('event', $eventName, $data);
        } catch (\Exception $e) {
            Log::warning('Google Analytics e-commerce tracking failed', [
                'event' => $eventName,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function setUserProperties(?int $userId, array $properties): void
    {
        if (!$this->isEnabled() || !$userId) {
            return;
        }

        try {
            $this->sendToMeasurementProtocol('user_properties', null, array_merge([
                'user_id' => (string) $userId
            ], $properties));
        } catch (\Exception $e) {
            Log::warning('Google Analytics user properties failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function sendToMeasurementProtocol(string $type, ?string $eventName, array $data): void
    {
        if (empty($this->gaId)) {
            return;
        }

        $endpoint = "https://www.google-analytics.com/mp/collect";
        $apiSecret = config('services.google_analytics.api_secret');

        if (!$apiSecret) {
            return;
        }

        $payload = [
            'client_id' => $this->generateClientId(),
            'user_id' => $data['user_id'] ?? null,
        ];

        if ($type === 'event') {
            $payload['events'] = [[
                'name' => $eventName,
                'params' => $data
            ]];
        }

        Http::post("{$endpoint}?measurement_id={$this->gaId}&api_secret={$apiSecret}", $payload);
    }

    protected function generateClientId(): string
    {
        return request()->ip() . '.' . time();
    }

    public function getEventDataForProduct($product): array
    {
        return [
            'item_id' => (string) $product->id,
            'item_name' => $product->name ?? 'Product',
            'item_category' => $product->category->name ?? 'Uncategorized',
            'item_brand' => $product->brand->name ?? '',
            'price' => (float) ($product->price ?? 0),
            'currency' => 'NZD',
        ];
    }

    public function getEventDataForOrder($order): array
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'item_id' => (string) $item->product_id,
                'item_name' => $item->product_name ?? 'Product',
                'item_category' => $item->product->category->name ?? 'Uncategorized',
                'item_brand' => $item->product->brand->name ?? '',
                'price' => (float) ($item->price ?? 0),
                'quantity' => (int) ($item->quantity ?? 1),
            ];
        }

        return [
            'transaction_id' => $order->order_number,
            'value' => (float) $order->total,
            'currency' => 'NZD',
            'tax' => (float) ($order->tax ?? 0),
            'shipping' => (float) ($order->shipping_price ?? $order->shipping ?? 0),
            'coupon' => $order->coupon_code ?? '',
            'items' => $items,
        ];
    }
}
