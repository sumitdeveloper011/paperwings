<?php

namespace App\Services;

/**
 * PriceCalculationService
 * 
 * Centralized service for all price-related calculations including:
 * - Tax (GST) calculations
 * - Discount calculations
 * - Final price calculations
 * - Price formatting
 */
class PriceCalculationService
{
    /**
     * Get GST rate as percentage
     */
    public function getGstRate(): float
    {
        return config('tax.gst_rate', 15);
    }

    /**
     * Get GST rate as decimal (e.g., 0.15 for 15%)
     */
    public function getGstDecimal(): float
    {
        return config('tax.gst_decimal', 0.15);
    }

    /**
     * Get GST multiplier (e.g., 1.15 for 15% GST)
     */
    public function getGstMultiplier(): float
    {
        return config('tax.gst_multiplier', 1.15);
    }

    /**
     * Calculate price with GST included
     * 
     * @param float $priceWithoutTax Price without tax
     * @return float Price with GST included
     */
    public function addGst(float $priceWithoutTax): float
    {
        return round($priceWithoutTax * $this->getGstMultiplier(), 2);
    }

    /**
     * Calculate price without GST from price with GST
     * 
     * @param float $priceWithTax Price with GST included
     * @return float Price without GST
     */
    public function removeGst(float $priceWithTax): float
    {
        return round($priceWithTax / $this->getGstMultiplier(), 2);
    }

    /**
     * Calculate GST amount from price with GST
     * 
     * @param float $priceWithTax Price with GST included
     * @return float GST amount
     */
    public function calculateGstAmount(float $priceWithTax): float
    {
        return round($priceWithTax - $this->removeGst($priceWithTax), 2);
    }

    /**
     * Calculate discount amount
     * 
     * @param float $originalPrice Original price
     * @param string $discountType Type of discount: 'percentage', 'fixed', 'direct', or 'none'
     * @param float|null $discountValue Discount value (percentage or fixed amount)
     * @param float|null $directPrice Direct price (for 'direct' type)
     * @return float Discount amount
     */
    public function calculateDiscountAmount(
        float $originalPrice,
        string $discountType = 'none',
        ?float $discountValue = null,
        ?float $directPrice = null
    ): float {
        if ($discountType === 'none' || !$discountType) {
            return 0.0;
        }

        if ($discountType === 'percentage' && $discountValue) {
            return round($originalPrice * ($discountValue / 100), 2);
        }

        if ($discountType === 'fixed' && $discountValue) {
            return round(min($discountValue, $originalPrice), 2);
        }

        if ($discountType === 'direct' && $directPrice && $directPrice < $originalPrice) {
            return round($originalPrice - $directPrice, 2);
        }

        return 0.0;
    }

    /**
     * Calculate final price after discount
     * 
     * @param float $originalPrice Original price
     * @param string $discountType Type of discount: 'percentage', 'fixed', 'direct', or 'none'
     * @param float|null $discountValue Discount value (percentage or fixed amount)
     * @param float|null $directPrice Direct price (for 'direct' type)
     * @return float Final price after discount
     */
    public function calculateFinalPrice(
        float $originalPrice,
        string $discountType = 'none',
        ?float $discountValue = null,
        ?float $directPrice = null
    ): float {
        if ($discountType === 'none' || !$discountType) {
            return round($originalPrice, 2);
        }

        if ($discountType === 'percentage' && $discountValue) {
            return round(max(0, $originalPrice - ($originalPrice * ($discountValue / 100))), 2);
        }

        if ($discountType === 'fixed' && $discountValue) {
            return round(max(0, $originalPrice - $discountValue), 2);
        }

        if ($discountType === 'direct' && $directPrice && $directPrice < $originalPrice) {
            return round($directPrice, 2);
        }

        return round($originalPrice, 2);
    }

    /**
     * Calculate discount percentage from original and final price
     * 
     * @param float $originalPrice Original price
     * @param float $finalPrice Final price after discount
     * @return float Discount percentage
     */
    public function calculateDiscountPercentage(float $originalPrice, float $finalPrice): float
    {
        if ($originalPrice <= 0) {
            return 0.0;
        }

        $discountAmount = $originalPrice - $finalPrice;
        return round(($discountAmount / $originalPrice) * 100, 2);
    }

    /**
     * Format price for display
     * 
     * @param float $price Price to format
     * @param string $currency Currency symbol (default: '$')
     * @param int $decimals Number of decimal places (default: 2)
     * @return string Formatted price string
     */
    public function formatPrice(float $price, string $currency = '$', int $decimals = 2): string
    {
        return $currency . number_format($price, $decimals);
    }

    /**
     * Calculate subtotal from cart items
     * 
     * @param \Illuminate\Support\Collection $cartItems Collection of cart items
     * @return float Subtotal
     */
    public function calculateSubtotal($cartItems): float
    {
        return round($cartItems->sum(function($item) {
            return ($item->price ?? 0) * ($item->quantity ?? 0);
        }), 2);
    }

    /**
     * Calculate total with discount and shipping
     * 
     * @param float $subtotal Subtotal
     * @param float $discount Discount amount
     * @param float $shipping Shipping cost
     * @return float Total amount
     */
    public function calculateTotal(float $subtotal, float $discount = 0.0, float $shipping = 0.0): float
    {
        return round($subtotal - $discount + $shipping, 2);
    }
}
