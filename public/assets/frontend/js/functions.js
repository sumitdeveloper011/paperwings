/**
 * Wishlist Functionality
 * Handles add to wishlist, remove from wishlist, and wishlist sidebar
 */

(function() {
    'use strict';

    // CSRF Token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    /**
     * Add product to wishlist
     */
    function addToWishlist(productId, button) {
        // Show loading state
        if (button) {
            const icon = button.querySelector('i');
            if (icon) {
                icon.classList.remove('fas', 'fa-heart');
                icon.classList.add('fas', 'fa-spinner', 'fa-spin');
                button.disabled = true;
            }
        }

        fetch('/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.requires_login) {
                // User not logged in, redirect to login with intended URL
                const currentUrl = window.location.href;
                window.location.href = data.redirect_url + '?intended=' + encodeURIComponent(currentUrl);
                return;
            }

            if (data.success) {
                // Update button state
                if (button) {
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-spinner', 'fa-spin');
                        icon.classList.add('fa-heart');
                        button.classList.add('in-wishlist');
                        button.disabled = false;
                    }
                }

                // Update wishlist count in header and sidebar
                if (data.wishlist_count !== undefined) {
                    updateWishlistCount(data.wishlist_count);
                }

                // Show success message
                showNotification('Product added to wishlist!', 'success');

                // Load and show wishlist sidebar
                loadWishlistSidebar();
                toggleWishlistSidebar();
            } else {
                // Show error message
                showNotification(data.message || 'Failed to add product to wishlist.', 'error');

                // Reset button state
                if (button) {
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-spinner', 'fa-spin');
                        icon.classList.add('fa-heart');
                        button.disabled = false;
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');

            // Reset button state
            if (button) {
                const icon = button.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add('fa-heart');
                    button.disabled = false;
                }
            }
        });
    }

    /**
     * Remove product from wishlist
     */
    function removeFromWishlist(productId, button) {
        // Show loading state
        if (button) {
            const icon = button.querySelector('i');
            if (icon) {
                icon.classList.add('fa-spinner', 'fa-spin');
                button.disabled = true;
            }
        }

        fetch('/wishlist/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update button state
                if (button) {
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-spinner', 'fa-spin');
                        icon.classList.add('fa-heart');
                        button.classList.remove('in-wishlist');
                        button.disabled = false;
                    }
                }

                // Update wishlist count in header and sidebar
                if (data.wishlist_count !== undefined) {
                    updateWishlistCount(data.wishlist_count);
                }

                // Show success message
                showNotification('Product removed from wishlist!', 'success');

                // Reload wishlist sidebar
                loadWishlistSidebar();
            } else {
                showNotification(data.message || 'Failed to remove product from wishlist.', 'error');

                // Reset button state
                if (button) {
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-spinner', 'fa-spin');
                        icon.classList.add('fa-heart');
                        button.disabled = false;
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');

            // Reset button state
            if (button) {
                const icon = button.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add('fa-heart');
                    button.disabled = false;
                }
            }
        });
    }

    /**
     * Toggle wishlist (add or remove)
     */
    function toggleWishlist(productId, button) {
        if (button && button.classList.contains('in-wishlist')) {
            removeFromWishlist(productId, button);
        } else {
            addToWishlist(productId, button);
        }
    }

    /**
     * Load wishlist sidebar content
     */
    function loadWishlistSidebar() {
        fetch('/wishlist/list', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateWishlistSidebar(data.items);
                updateWishlistCount(data.count);
            }
        })
        .catch(error => {
            console.error('Error loading wishlist:', error);
        });
    }

    /**
     * Update wishlist sidebar HTML
     */
    function updateWishlistSidebar(items) {
        const sidebarItems = document.getElementById('wishlist-sidebar-items');
        const sidebarEmpty = document.getElementById('wishlist-sidebar-empty');

        if (!sidebarItems || !sidebarEmpty) return;

        if (items.length === 0) {
            sidebarItems.style.display = 'none';
            sidebarEmpty.style.display = 'flex';
            return;
        }

        sidebarEmpty.style.display = 'none';
        sidebarItems.style.display = 'flex';

        let html = '';
        items.forEach(item => {
            const price = item.discount_price || item.product_price;
            const oldPrice = item.discount_price ? item.product_price : null;

            html += `
                <div class="wishlist-sidebar-item" data-product-id="${item.product_id}">
                    <div class="wishlist-sidebar-item__checkbox">
                        <label class="checkbox-label">
                            <input type="checkbox" class="wishlist-item-checkbox" data-product-id="${item.product_id}">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="wishlist-sidebar-item__image">
                        <a href="${item.product_url}">
                            <img src="${item.product_image}" alt="${item.product_name}">
                        </a>
                    </div>
                    <div class="wishlist-sidebar-item__info">
                        <h4 class="wishlist-sidebar-item__name">
                            <a href="${item.product_url}">${item.product_name}</a>
                        </h4>
                        <div class="wishlist-sidebar-item__price-row">
                            ${oldPrice ? `<span class="wishlist-sidebar-item__price" style="text-decoration: line-through; color: #6c757d; font-size: 0.85rem; margin-right: 0.5rem;">$${oldPrice}</span>` : ''}
                            <span class="wishlist-sidebar-item__price">$${price}</span>
                        </div>
                    </div>
                    <button class="wishlist-sidebar-item__remove" data-product-id="${item.product_id}" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });

        sidebarItems.innerHTML = html;

        // Attach remove event listeners
        document.querySelectorAll('.wishlist-sidebar-item__remove').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const item = this.closest('.wishlist-sidebar-item');
                removeFromWishlist(productId, this);
                // Remove item from DOM
                if (item) {
                    $(item).fadeOut(300, function() {
                        $(this).remove();
                        // Check if wishlist is empty
                        const remainingItems = document.querySelectorAll('.wishlist-sidebar-item');
                        if (remainingItems.length === 0) {
                            updateWishlistSidebar([]);
                        }
                    });
                }
            });
        });
    }

    /**
     * Update wishlist count badge
     */
    function updateWishlistCount(count) {
        // Update sidebar count badge
        const countBadge = document.getElementById('wishlist-count');
        if (countBadge) {
            countBadge.textContent = count;
            countBadge.style.display = count > 0 ? 'absolute' : 'none';
        }

        // Update header badge
        const headerBadge = document.getElementById('wishlist-header-badge');
        if (headerBadge) {
            headerBadge.textContent = count;
            // Show badge if count > 0, hide if 0
            if (count > 0) {
                headerBadge.style.display = 'absolute';
            } else {
                headerBadge.style.display = 'none';
            }
        }
    }

    /**
     * Check wishlist status for products on page load
     */
    function checkWishlistStatus() {
        const wishlistButtons = document.querySelectorAll('.wishlist-btn[data-product-id]');
        if (wishlistButtons.length === 0) return;

        const productIds = Array.from(wishlistButtons).map(btn => btn.getAttribute('data-product-id'));

        fetch('/wishlist/check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_ids: productIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.status) {
                wishlistButtons.forEach(button => {
                    const productId = button.getAttribute('data-product-id');
                    if (data.status[productId]) {
                        button.classList.add('in-wishlist');
                        const icon = button.querySelector('i');
                        if (icon) {
                            icon.classList.add('fas', 'fa-heart');
                        }
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error checking wishlist status:', error);
        });
    }

    /**
     * Show notification message
     */
    function showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `wishlist-notification wishlist-notification--${type}`;
        notification.textContent = message;

        // Add to body
        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    /**
     * Toggle wishlist sidebar
     */
    function toggleWishlistSidebar() {
        const sidebar = document.getElementById('wishlist-sidebar');
        const overlay = document.getElementById('wishlist-sidebar-overlay');
        if (!sidebar) return;

        const isOpening = !sidebar.classList.contains('active');
        sidebar.classList.toggle('active');
        if (overlay) {
            overlay.classList.toggle('active');
        }

        // Prevent body scroll when sidebar is open
        if (sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
            // Load wishlist content when opening
            if (isOpening) {
                loadWishlistSidebar();
            }
        } else {
            document.body.style.overflow = '';
        }
    }

    /**
     * Close wishlist sidebar
     */
    function closeWishlistSidebar() {
        const sidebar = document.getElementById('wishlist-sidebar');
        const overlay = document.getElementById('wishlist-sidebar-overlay');
        if (sidebar) {
            sidebar.classList.remove('active');
        }
        if (overlay) {
            overlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Attach click handlers to wishlist buttons
        document.addEventListener('click', function(e) {
            const wishlistBtn = e.target.closest('.wishlist-btn');
            if (wishlistBtn) {
                e.preventDefault();
                const productId = wishlistBtn.getAttribute('data-product-id');
                if (productId) {
                    toggleWishlist(productId, wishlistBtn);
                }
            }

            // Open wishlist sidebar when wishlist trigger is clicked
            const wishlistTrigger = e.target.closest('#wishlist-trigger') || e.target.closest('.wishlist-trigger');
            if (wishlistTrigger) {
                e.preventDefault();
                toggleWishlistSidebar();
            }

            // Close sidebar when close button is clicked
            if (e.target.closest('#wishlist-sidebar-close') || e.target.closest('.wishlist-sidebar__close')) {
                e.preventDefault();
                closeWishlistSidebar();
            }

            // Close sidebar when overlay is clicked
            if (e.target.id === 'wishlist-sidebar-overlay' || e.target.classList.contains('wishlist-sidebar-overlay')) {
                closeWishlistSidebar();
            }
        });

        // Close sidebar on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeWishlistSidebar();
            }
        });

        // Check wishlist status on page load
        checkWishlistStatus();

        // Load wishlist count on page load
        fetch('/wishlist/count', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateWishlistCount(data.count);
            }
        })
        .catch(error => {
            console.error('Error loading wishlist count:', error);
        });
    });

    // Export functions to global scope
    window.WishlistFunctions = {
        addToWishlist,
        removeFromWishlist,
        toggleWishlist,
        loadWishlistSidebar,
        toggleWishlistSidebar,
        closeWishlistSidebar
    };

})();

