<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CheckoutSessionService
{
    const SESSION_KEY = 'checkout_data';

    /**
     * Store shipping address data
     */
    public function storeShippingAddress(array $data): void
    {
        $checkoutData = $this->getCheckoutData();
        $checkoutData['shipping'] = $data;
        Session::put(self::SESSION_KEY, $checkoutData);
    }

    /**
     * Store billing address data
     */
    public function storeBillingAddress(array $data): void
    {
        $checkoutData = $this->getCheckoutData();
        $checkoutData['billing'] = $data;
        Session::put(self::SESSION_KEY, $checkoutData);
    }

    /**
     * Store order notes
     */
    public function storeOrderNotes(string $notes): void
    {
        $checkoutData = $this->getCheckoutData();
        $checkoutData['notes'] = $notes;
        Session::put(self::SESSION_KEY, $checkoutData);
    }

    /**
     * Store all checkout details at once
     */
    public function storeDetails(array $data): void
    {
        $checkoutData = [
            'shipping' => $data['shipping'] ?? [],
            'billing' => $data['billing'] ?? [],
            'notes' => $data['notes'] ?? '',
            'billing_different' => $data['billing_different'] ?? false,
        ];
        Session::put(self::SESSION_KEY, $checkoutData);
    }

    /**
     * Get all checkout data
     */
    public function getCheckoutData(): array
    {
        return Session::get(self::SESSION_KEY, [
            'shipping' => [],
            'billing' => [],
            'notes' => '',
            'billing_different' => false,
        ]);
    }

    /**
     * Get shipping address
     */
    public function getShippingAddress(): array
    {
        return $this->getCheckoutData()['shipping'] ?? [];
    }

    /**
     * Get billing address
     */
    public function getBillingAddress(): array
    {
        return $this->getCheckoutData()['billing'] ?? [];
    }

    /**
     * Get order notes
     */
    public function getOrderNotes(): string
    {
        return $this->getCheckoutData()['notes'] ?? '';
    }

    /**
     * Check if billing is different from shipping
     */
    public function isBillingDifferent(): bool
    {
        return $this->getCheckoutData()['billing_different'] ?? false;
    }

    /**
     * Store order totals for review/payment steps
     */
    public function storeTotals(array $totals): void
    {
        $checkoutData = $this->getCheckoutData();
        $checkoutData['totals'] = $totals;
        Session::put(self::SESSION_KEY, $checkoutData);
    }

    /**
     * Get order totals
     */
    public function getTotals(): array
    {
        return $this->getCheckoutData()['totals'] ?? [];
    }

    /**
     * Validate that required data exists
     */
    public function hasRequiredData(): bool
    {
        $data = $this->getCheckoutData();
        return !empty($data['shipping']);
    }

    /**
     * Clear all checkout data
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Check if checkout data exists
     */
    public function exists(): bool
    {
        return Session::has(self::SESSION_KEY);
    }
}
