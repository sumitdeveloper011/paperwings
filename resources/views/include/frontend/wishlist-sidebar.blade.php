<!-- Wishlist Sidebar -->
<div class="wishlist-sidebar-overlay" id="wishlist-sidebar-overlay"></div>
<div class="wishlist-sidebar" id="wishlist-sidebar">
    <div class="wishlist-sidebar__header">
        <h3 class="wishlist-sidebar__title">My Wishlist <span id="wishlist-count" class="wishlist-count" style="display: none;">0</span></h3>
        <button class="wishlist-sidebar__close" id="wishlist-sidebar-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="wishlist-sidebar__body">
        <div class="wishlist-sidebar__items" id="wishlist-sidebar-items" style="display: none;"></div>
        <div class="wishlist-sidebar__empty" id="wishlist-sidebar-empty">
            <div class="wishlist-sidebar__empty-icon">
                <i class="far fa-heart"></i>
            </div>
            <p class="wishlist-sidebar__empty-text">Your wishlist is empty</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>
    <div class="wishlist-sidebar__footer" id="wishlist-sidebar-footer" style="display: none;">
        <div class="wishlist-sidebar__actions">
            <label class="wishlist-select-all-label">
                <input type="checkbox" id="wishlist-select-all" class="wishlist-select-all-checkbox">
                <span>Select All</span>
            </label>
            <div class="wishlist-bulk-actions">
                <button class="btn btn-primary btn-sm move-to-cart-btn" id="wishlist-add-selected-to-cart" disabled>
                    <i class="fas fa-shopping-cart"></i> <span class="btn-text">Add to Cart</span>
                </button>
                <button class="btn btn-outline-danger btn-sm" id="wishlist-remove-selected" disabled>
                    <i class="fas fa-trash"></i> <span class="btn-text">Remove</span>
                </button>
            </div>
        </div>
    </div>
</div>