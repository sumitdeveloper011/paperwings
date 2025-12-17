<!-- Cart Sidebar -->
<div class="cart-sidebar-overlay" id="cart-sidebar-overlay"></div>
    <div class="cart-sidebar" id="cart-sidebar">
        <div class="cart-sidebar__header">
            <h3 class="cart-sidebar__title">Shopping Cart</h3>
            <button class="cart-sidebar__close" id="cart-sidebar-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-sidebar__body">
            <div class="cart-sidebar__items" id="cart-sidebar-items" style="display: none;">
                <!-- Cart items will be loaded dynamically -->
            </div>

            <div class="cart-sidebar__empty" id="cart-sidebar-empty" style="display: flex;">
                <div class="cart-sidebar__empty-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <p class="cart-sidebar__empty-text">Your cart is empty</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
        <div class="cart-sidebar__footer" id="cart-sidebar-summary" style="display: none;">
            <div class="cart-sidebar__summary">
                <div class="cart-sidebar__summary-row cart-sidebar__summary-row--total">
                    <span class="cart-sidebar__summary-label">Total</span>
                    <span class="cart-sidebar__summary-value">$0.00</span>
                </div>
            </div>
            <div class="cart-sidebar__actions">
                <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-block">View Cart</a>
                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block">Checkout</a>
            </div>
        </div>
    </div>
