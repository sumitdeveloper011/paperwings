@extends('layouts.frontend.main')
@section('content')
    @include('include.frontend.breadcrumb')
    <section class="cart-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-table-wrapper">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="cart-item">
                                    <td class="cart-item__product">
                                        <div class="cart-item__image">
                                            <img src="assets/images/product-1.jpg" alt="Premium Notebook">
                                        </div>
                                        <div class="cart-item__info">
                                            <h3 class="cart-item__name">Premium Notebook</h3>
                                            <span class="cart-item__sku">SKU: NB-001</span>
                                            <div class="cart-item__attributes">
                                                <span>Color: Black</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cart-item__price">
                                        <span class="price-current">$24.99</span>
                                        <span class="price-old">$34.99</span>
                                    </td>
                                    <td class="cart-item__quantity">
                                        <div class="quantity-selector">
                                            <button class="qty-btn" type="button">-</button>
                                            <input type="number" value="2" min="1" max="99" class="qty-input">
                                            <button class="qty-btn" type="button">+</button>
                                        </div>
                                    </td>
                                    <td class="cart-item__subtotal">
                                        <span class="subtotal-price">$49.98</span>
                                    </td>
                                    <td class="cart-item__remove">
                                        <button class="remove-btn" type="button" title="Remove item">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="cart-item">
                                    <td class="cart-item__product">
                                        <div class="cart-item__image">
                                            <img src="assets/images/product-2.jpg" alt="Professional Pen Set">
                                        </div>
                                        <div class="cart-item__info">
                                            <h3 class="cart-item__name">Professional Pen Set</h3>
                                            <span class="cart-item__sku">SKU: PEN-002</span>
                                            <div class="cart-item__attributes">
                                                <span>Color: Blue</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cart-item__price">
                                        <span class="price-current">$19.99</span>
                                    </td>
                                    <td class="cart-item__quantity">
                                        <div class="quantity-selector">
                                            <button class="qty-btn" type="button">-</button>
                                            <input type="number" value="1" min="1" max="99" class="qty-input">
                                            <button class="qty-btn" type="button">+</button>
                                        </div>
                                    </td>
                                    <td class="cart-item__subtotal">
                                        <span class="subtotal-price">$19.99</span>
                                    </td>
                                    <td class="cart-item__remove">
                                        <button class="remove-btn" type="button" title="Remove item">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="cart-item">
                                    <td class="cart-item__product">
                                        <div class="cart-item__image">
                                            <img src="assets/images/product-3.jpg" alt="Art Supply Kit">
                                        </div>
                                        <div class="cart-item__info">
                                            <h3 class="cart-item__name">Art Supply Kit</h3>
                                            <span class="cart-item__sku">SKU: ART-003</span>
                                            <div class="cart-item__attributes">
                                                <span>Size: Medium</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cart-item__price">
                                        <span class="price-current">$45.99</span>
                                        <span class="price-old">$59.99</span>
                                    </td>
                                    <td class="cart-item__quantity">
                                        <div class="quantity-selector">
                                            <button class="qty-btn" type="button">-</button>
                                            <input type="number" value="1" min="1" max="99" class="qty-input">
                                            <button class="qty-btn" type="button">+</button>
                                        </div>
                                    </td>
                                    <td class="cart-item__subtotal">
                                        <span class="subtotal-price">$45.99</span>
                                    </td>
                                    <td class="cart-item__remove">
                                        <button class="remove-btn" type="button" title="Remove item">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="cart-actions">
                        <a href="products.html" class="continue-shopping-btn">
                            <i class="fas fa-arrow-left"></i>
                            Continue Shopping
                        </a>
                        <button class="update-cart-btn" type="button">
                            Update Cart
                        </button>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h3 class="cart-summary__title">Cart Summary</h3>
                        
                        <div class="cart-summary__item">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value">$115.96</span>
                        </div>
                        
                        <div class="cart-summary__item">
                            <span class="summary-label">Shipping</span>
                            <span class="summary-value">$5.00</span>
                        </div>
                        
                        <div class="cart-summary__item cart-summary__item--total">
                            <span class="summary-label">Total</span>
                            <span class="summary-value">$120.96</span>
                        </div>

                        <div class="cart-summary__coupon">
                            <h4 class="coupon-title">Have a coupon code?</h4>
                            <div class="coupon-form">
                                <input type="text" class="coupon-input" placeholder="Enter coupon code">
                                <button class="coupon-btn" type="button">Apply</button>
                            </div>
                        </div>

                        <div class="cart-summary__checkout">
                            <a href="#" class="checkout-btn">
                                Proceed to Checkout
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection