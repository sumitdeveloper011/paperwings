<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PriceCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriceCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PriceCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PriceCalculationService();
    }

    // Test: Get GST rate
    public function test_get_gst_rate_returns_config_value(): void
    {
        $rate = $this->service->getGstRate();
        $this->assertIsFloat($rate);
        $this->assertGreaterThan(0, $rate);
    }

    // Test: Get GST decimal
    public function test_get_gst_decimal_returns_config_value(): void
    {
        $decimal = $this->service->getGstDecimal();
        $this->assertIsFloat($decimal);
        $this->assertGreaterThan(0, $decimal);
        $this->assertLessThan(1, $decimal);
    }

    // Test: Get GST multiplier
    public function test_get_gst_multiplier_returns_config_value(): void
    {
        $multiplier = $this->service->getGstMultiplier();
        $this->assertIsFloat($multiplier);
        $this->assertGreaterThan(1, $multiplier);
    }

    // Test: Add GST to price
    public function test_add_gst_calculates_correctly(): void
    {
        $priceWithoutTax = 100.00;
        $priceWithTax = $this->service->addGst($priceWithoutTax);
        
        $this->assertIsFloat($priceWithTax);
        $this->assertGreaterThan($priceWithoutTax, $priceWithTax);
        $this->assertEquals(115.00, $priceWithTax);
    }

    // Test: Remove GST from price
    public function test_remove_gst_calculates_correctly(): void
    {
        $priceWithTax = 115.00;
        $priceWithoutTax = $this->service->removeGst($priceWithTax);
        
        $this->assertIsFloat($priceWithoutTax);
        $this->assertLessThan($priceWithTax, $priceWithoutTax);
        $this->assertEquals(100.00, $priceWithoutTax);
    }

    // Test: Calculate GST amount
    public function test_calculate_gst_amount_calculates_correctly(): void
    {
        $priceWithTax = 115.00;
        $gstAmount = $this->service->calculateGstAmount($priceWithTax);
        
        $this->assertIsFloat($gstAmount);
        $this->assertEquals(15.00, $gstAmount);
    }

    // Test: Calculate discount amount - percentage
    public function test_calculate_discount_amount_percentage(): void
    {
        $originalPrice = 100.00;
        $discountAmount = $this->service->calculateDiscountAmount($originalPrice, 'percentage', 10);
        
        $this->assertEquals(10.00, $discountAmount);
    }

    // Test: Calculate discount amount - fixed
    public function test_calculate_discount_amount_fixed(): void
    {
        $originalPrice = 100.00;
        $discountAmount = $this->service->calculateDiscountAmount($originalPrice, 'fixed', 20);
        
        $this->assertEquals(20.00, $discountAmount);
    }

    // Test: Calculate discount amount - fixed (cannot exceed original price)
    public function test_calculate_discount_amount_fixed_cannot_exceed_price(): void
    {
        $originalPrice = 100.00;
        $discountAmount = $this->service->calculateDiscountAmount($originalPrice, 'fixed', 150);
        
        $this->assertEquals(100.00, $discountAmount);
    }

    // Test: Calculate discount amount - direct price
    public function test_calculate_discount_amount_direct(): void
    {
        $originalPrice = 100.00;
        $directPrice = 80.00;
        $discountAmount = $this->service->calculateDiscountAmount($originalPrice, 'direct', null, $directPrice);
        
        $this->assertEquals(20.00, $discountAmount);
    }

    // Test: Calculate discount amount - none
    public function test_calculate_discount_amount_none(): void
    {
        $originalPrice = 100.00;
        $discountAmount = $this->service->calculateDiscountAmount($originalPrice, 'none');
        
        $this->assertEquals(0.00, $discountAmount);
    }

    // Test: Calculate final price - percentage discount
    public function test_calculate_final_price_percentage(): void
    {
        $originalPrice = 100.00;
        $finalPrice = $this->service->calculateFinalPrice($originalPrice, 'percentage', 10);
        
        $this->assertEquals(90.00, $finalPrice);
    }

    // Test: Calculate final price - fixed discount
    public function test_calculate_final_price_fixed(): void
    {
        $originalPrice = 100.00;
        $finalPrice = $this->service->calculateFinalPrice($originalPrice, 'fixed', 20);
        
        $this->assertEquals(80.00, $finalPrice);
    }

    // Test: Calculate final price - fixed discount (cannot go negative)
    public function test_calculate_final_price_fixed_cannot_go_negative(): void
    {
        $originalPrice = 100.00;
        $finalPrice = $this->service->calculateFinalPrice($originalPrice, 'fixed', 150);
        
        $this->assertEquals(0.00, $finalPrice);
    }

    // Test: Calculate final price - direct price
    public function test_calculate_final_price_direct(): void
    {
        $originalPrice = 100.00;
        $directPrice = 80.00;
        $finalPrice = $this->service->calculateFinalPrice($originalPrice, 'direct', null, $directPrice);
        
        $this->assertEquals(80.00, $finalPrice);
    }

    // Test: Calculate final price - none
    public function test_calculate_final_price_none(): void
    {
        $originalPrice = 100.00;
        $finalPrice = $this->service->calculateFinalPrice($originalPrice, 'none');
        
        $this->assertEquals(100.00, $finalPrice);
    }

    // Test: Calculate discount percentage
    public function test_calculate_discount_percentage(): void
    {
        $originalPrice = 100.00;
        $finalPrice = 80.00;
        $discountPercentage = $this->service->calculateDiscountPercentage($originalPrice, $finalPrice);
        
        $this->assertEquals(20.00, $discountPercentage);
    }

    // Test: Calculate discount percentage - zero original price
    public function test_calculate_discount_percentage_zero_original(): void
    {
        $originalPrice = 0.00;
        $finalPrice = 80.00;
        $discountPercentage = $this->service->calculateDiscountPercentage($originalPrice, $finalPrice);
        
        $this->assertEquals(0.00, $discountPercentage);
    }

    // Test: Format price
    public function test_format_price_defaults(): void
    {
        $price = 123.45;
        $formatted = $this->service->formatPrice($price);
        
        $this->assertStringStartsWith('$', $formatted);
        $this->assertStringContainsString('123.45', $formatted);
    }

    // Test: Format price with custom currency
    public function test_format_price_custom_currency(): void
    {
        $price = 123.45;
        $formatted = $this->service->formatPrice($price, '€');
        
        $this->assertStringStartsWith('€', $formatted);
    }

    // Test: Format price with custom decimals
    public function test_format_price_custom_decimals(): void
    {
        $price = 123.456;
        $formatted = $this->service->formatPrice($price, '$', 2);
        
        $this->assertStringContainsString('123.46', $formatted);
    }

    // Test: Calculate subtotal from cart items
    public function test_calculate_subtotal_from_cart_items(): void
    {
        $cartItems = collect([
            (object) ['price' => 10.00, 'quantity' => 2],
            (object) ['price' => 20.00, 'quantity' => 1],
        ]);
        
        $subtotal = $this->service->calculateSubtotal($cartItems);
        
        $this->assertEquals(40.00, $subtotal);
    }

    // Test: Calculate total with discount and shipping
    public function test_calculate_total_with_discount_and_shipping(): void
    {
        $subtotal = 100.00;
        $discount = 10.00;
        $shipping = 5.00;
        
        $total = $this->service->calculateTotal($subtotal, $discount, $shipping);
        
        $this->assertEquals(95.00, $total);
    }

    // Test: Calculate total without discount
    public function test_calculate_total_without_discount(): void
    {
        $subtotal = 100.00;
        $shipping = 5.00;
        
        $total = $this->service->calculateTotal($subtotal, 0, $shipping);
        
        $this->assertEquals(105.00, $total);
    }

    // Test: Calculate total without shipping
    public function test_calculate_total_without_shipping(): void
    {
        $subtotal = 100.00;
        $discount = 10.00;
        
        $total = $this->service->calculateTotal($subtotal, $discount, 0);
        
        $this->assertEquals(90.00, $total);
    }
}
