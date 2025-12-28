<?php

namespace App\Services;

use App\Models\ShippingPrice;
use App\Models\Region;
use Illuminate\Support\Facades\Cache;

class ShippingService
{
    // Calculate shipping price based on region and order amount
    public function calculateShipping(?int $regionId, float $orderAmount): float
    {
        if (!$regionId) {
            return 0.00;
        }

        $shippingPrice = ShippingPrice::where('region_id', $regionId)
            ->where('status', 1)
            ->first();

        if (!$shippingPrice) {
            return 0.00;
        }

        if ($shippingPrice->free_shipping_minimum && 
            $orderAmount >= $shippingPrice->free_shipping_minimum) {
            return 0.00;
        }

        return (float) $shippingPrice->shipping_price;
    }

    // Get shipping price for a region (for display purposes)
    public function getShippingInfo(?int $regionId): array
    {
        if (!$regionId) {
            return [
                'shipping_price' => 0.00,
                'free_shipping_minimum' => null,
                'is_free_shipping' => false,
            ];
        }

        $shippingPrice = ShippingPrice::where('region_id', $regionId)
            ->where('status', 1)
            ->first();

        if (!$shippingPrice) {
            return [
                'shipping_price' => 0.00,
                'free_shipping_minimum' => null,
                'is_free_shipping' => false,
            ];
        }

        return [
            'shipping_price' => (float) $shippingPrice->shipping_price,
            'free_shipping_minimum' => $shippingPrice->free_shipping_minimum ? (float) $shippingPrice->free_shipping_minimum : null,
            'is_free_shipping' => false,
        ];
    }

    // Calculate shipping for order amount and region
    public function calculateShippingWithInfo(?int $regionId, float $orderAmount): array
    {
        $info = $this->getShippingInfo($regionId);
        
        if ($info['free_shipping_minimum'] && $orderAmount >= $info['free_shipping_minimum']) {
            $info['is_free_shipping'] = true;
            $info['shipping_price'] = 0.00;
        }

        return $info;
    }
}

