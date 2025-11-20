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
</div>