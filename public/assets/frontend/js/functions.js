/**
 * Main Functions Entry Point
 * Loads and initializes wishlist and cart modules
 * Exports functions to global scope for backward compatibility
 */
(function() {
    'use strict';

    // Wait for modules to load
    function initFunctions() {
        // Wait for modules to be available
        if (!window.AppUtils || !window.WishlistModule || !window.CartModule) {
            setTimeout(initFunctions, 50);
            return;
        }

        // Export to global scope for backward compatibility
        window.WishlistFunctions = {
            addToWishlist: (productId, button) => window.WishlistModule.addToWishlist(productId, button),
            removeFromWishlist: (productId, button, silent) => window.WishlistModule.removeFromWishlist(productId, button, silent),
            toggleWishlist: (productId, button) => window.WishlistModule.toggleWishlist(productId, button),
            loadWishlistSidebar: () => window.WishlistModule.loadSidebar(),
            checkWishlistStatus: () => window.WishlistModule.checkWishlistStatus(),
            loadWishlistCount: () => window.WishlistModule.loadWishlistCount(),
            toggleWishlistSidebar: () => window.WishlistModule.toggleSidebar(),
            closeWishlistSidebar: () => window.WishlistModule.closeSidebar()
        };

        window.CartFunctions = {
            addToCart: (productId, quantity, button) => window.CartModule.addToCart(productId, quantity, button),
            updateCartItem: (cartItemId, quantity) => window.CartModule.updateCartItem(cartItemId, quantity),
            removeFromCart: (cartItemId, button) => window.CartModule.removeFromCart(cartItemId, button),
            loadCartSidebar: () => window.CartModule.loadSidebar(),
            toggleCartSidebar: () => window.CartModule.toggleSidebar(),
            closeCartSidebar: () => window.CartModule.closeSidebar(),
            updateCartCount: (count) => window.CartModule.updateCount(count),
            checkCartStatus: () => window.CartModule.checkCartStatus(),
            loadCartCount: () => window.CartModule.loadCartCount()
        };
    }

    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFunctions);
    } else {
        initFunctions();
    }
})();
