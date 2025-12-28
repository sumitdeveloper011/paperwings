# Test Cases Documentation

## Overview
This directory contains test cases for the Paper Wings e-commerce application.

## Test Files Created

### 1. StripePaymentTest.php
Tests for Stripe payment functionality:
- ✅ Payment Intent creation
- ✅ Payment Intent validation
- ✅ Order creation after payment
- ✅ Webhook handling
- ✅ Error handling

### 2. CartTest.php
Tests for shopping cart functionality:
- ✅ Add product to cart
- ✅ Update cart item quantity
- ✅ Remove item from cart
- ✅ View cart
- ✅ Cart count
- ✅ Authentication required

### 3. WishlistTest.php
Tests for wishlist functionality:
- ✅ Add product to wishlist
- ✅ Remove product from wishlist
- ✅ View wishlist
- ✅ Wishlist count
- ✅ Check wishlist status
- ✅ Authentication required

### 4. CheckoutTest.php
Tests for checkout process:
- ✅ Access checkout page
- ✅ Apply coupon code
- ✅ Remove coupon
- ✅ Calculate shipping
- ✅ Order success page
- ✅ Payment verification

### 5. ProductTest.php
Tests for product functionality:
- ✅ View product detail page
- ✅ View products by category
- ✅ View shop page
- ✅ Product filters

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test File
```bash
php artisan test tests/Feature/CartTest.php
```

### Run Specific Test Method
```bash
php artisan test --filter test_user_can_add_product_to_cart
```

### Run with Coverage
```bash
php artisan test --coverage
```

## Test Database

Tests use SQLite in-memory database (configured in `phpunit.xml`):
- No need to set up separate test database
- Tests run in isolation
- Database is reset after each test

## Important Notes

1. **Stripe Tests**: Some Stripe tests may fail in actual run because they require Stripe API keys. Use Stripe test mode keys for testing.

2. **Factories Required**: Make sure you have factories for:
   - User
   - Product
   - Category
   - Coupon
   - Region
   - Order
   - CartItem
   - Wishlist

3. **Authentication**: Most tests require authenticated users. Use `$this->actingAs($user)` for authentication.

## Test Structure

Each test follows this pattern:
```php
public function test_feature_name()
{
    // Arrange: Set up test data
    $user = User::factory()->create();
    
    // Act: Perform the action
    $response = $this->postJson(route('cart.add'), [...]);
    
    // Assert: Verify the result
    $response->assertStatus(200);
    $this->assertDatabaseHas('cart_items', [...]);
}
```

## Adding New Tests

When adding new tests:
1. Create test file in `tests/Feature/` directory
2. Extend `Tests\TestCase`
3. Use `RefreshDatabase` trait
4. Follow naming convention: `test_feature_name()`
5. Use descriptive test names

