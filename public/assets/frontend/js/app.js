/**
 * Main Application JavaScript
 * Consolidated event handlers and optimized performance
 */
(function() {
    'use strict';

    const App = {
        // Configuration
        config: {
            debounceDelay: 300,
            throttleDelay: 100,
        },

        // Initialize all modules
        init() {
            this.initCarousels();
            this.initSearch();
            this.initWishlist();
            this.initCart();
            this.initFilters();
            this.initScrollToTop();
            this.initSubscription();
        },

        // Debounce helper
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Throttle helper
        throttle(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        // Initialize carousels
        initCarousels() {
            if (typeof $ === 'undefined' || typeof $.fn.slick === 'undefined') {
                return;
            }

            // Slick Slider
            $('.slider').slick({
                dots: true,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear',
                autoplay: true,
                autoplaySpeed: 5000,
                arrows: true,
                responsive: [{
                    breakpoint: 768,
                    settings: {
                        arrows: false
                    }
                }]
            });

            // Custom arrow styling
            $('.slider').on('init', function() {
                $('.slick-prev').html('<i class="fas fa-chevron-left"></i>');
                $('.slick-next').html('<i class="fas fa-chevron-right"></i>');
            });

            // Owl Carousel for products
            if (typeof $.fn.owlCarousel !== 'undefined') {
                $('.products-carousel').owlCarousel({
                    loop: true,
                    margin: 5,
                    nav: false,
                    dots: true,
                    autoplay: true,
                    autoplayTimeout: 5000,
                    autoplayHoverPause: true,
                    responsive: {
                        0: { items: 1 },
                        576: { items: 2 },
                        768: { items: 3 },
                        992: { items: 4 },
                        1200: { items: 6 }
                    }
                });

                // Product tabs functionality
                $('.products__tab').on('click', function() {
                    const targetTab = $(this).data('tab');
                    $('.products__tab').removeClass('products__tab--active');
                    $('.products__content').removeClass('products__content--active');
                    $(this).addClass('products__tab--active');
                    $('#' + targetTab).addClass('products__content--active');
                    $('#' + targetTab + ' .products-carousel').trigger('refresh.owl.carousel');
                });
            }
        },

        // Initialize search with debouncing
        initSearch() {
            const searchInput = document.getElementById('search-input') || 
                               document.querySelector('input[name="q"]') ||
                               document.querySelector('.search-input');
            
            if (!searchInput) return;

            const performSearch = this.debounce((value) => {
                if (value.length < 2) {
                    // Hide autocomplete
                    const dropdown = document.querySelector('.search-autocomplete');
                    if (dropdown) dropdown.style.display = 'none';
                    return;
                }

                // Perform autocomplete search
                fetch(`/search/autocomplete?q=${encodeURIComponent(value)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update autocomplete dropdown
                    this.updateAutocomplete(data.products || []);
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
            }, this.config.debounceDelay);

            searchInput.addEventListener('input', (e) => {
                performSearch(e.target.value);
            });
        },

        // Update autocomplete dropdown
        updateAutocomplete(products) {
            const dropdown = document.querySelector('.search-autocomplete');
            if (!dropdown) return;

            if (products.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            dropdown.innerHTML = products.map(product => `
                <a href="${product.url}" class="search-autocomplete-item">
                    <img src="${product.image}" alt="${product.name}">
                    <div>
                        <div class="search-autocomplete-name">${product.name}</div>
                        <div class="search-autocomplete-price">$${product.price.toFixed(2)}</div>
                    </div>
                </a>
            `).join('');

            dropdown.style.display = 'block';
        },

        // Initialize wishlist (delegated event handling)
        initWishlist() {
            // Use event delegation for dynamic content
            document.addEventListener('click', (e) => {
                const wishlistBtn = e.target.closest('.wishlist-btn');
                if (wishlistBtn) {
                    e.preventDefault();
                    const productId = wishlistBtn.getAttribute('data-product-id');
                    if (productId && window.WishlistFunctions) {
                        window.WishlistFunctions.toggleWishlist(productId, wishlistBtn);
                    }
                }
            });
        },

        // Initialize cart (delegated event handling)
        initCart() {
            // Use event delegation for dynamic content
            document.addEventListener('click', (e) => {
                const cartBtn = e.target.closest('.add-to-cart-btn, [data-add-to-cart]');
                if (cartBtn) {
                    e.preventDefault();
                    const productId = cartBtn.getAttribute('data-product-id') || 
                                     cartBtn.getAttribute('data-add-to-cart');
                    if (productId && window.CartFunctions) {
                        window.CartFunctions.addToCart(productId, 1, cartBtn);
                    }
                }
            });
        },

        // Initialize filters
        initFilters() {
            // Price range filter
            const priceRange = document.querySelector('.price-range-input');
            if (priceRange) {
                priceRange.addEventListener('input', this.throttle((e) => {
                    const value = e.target.value;
                    const display = document.querySelector('.price-range-value');
                    if (display) {
                        display.textContent = `$${value}`;
                    }
                }, this.config.throttleDelay));
            }

            // Sort select
            const sortSelect = document.querySelector('.sort-select');
            if (sortSelect) {
                sortSelect.addEventListener('change', () => {
                    const form = sortSelect.closest('form');
                    if (form) form.submit();
                });
            }
        },

        // Initialize scroll to top
        initScrollToTop() {
            const scrollBtn = document.querySelector('.scroll-to-top');
            if (!scrollBtn) return;

            const toggleScrollBtn = this.throttle(() => {
                if (window.pageYOffset > 300) {
                    scrollBtn.classList.add('show');
                } else {
                    scrollBtn.classList.remove('show');
                }
            }, this.config.throttleDelay);

            window.addEventListener('scroll', toggleScrollBtn);

            scrollBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        },

        // Initialize subscription form
        initSubscription() {
            const subscriptionForm = document.getElementById('subscriptionForm');
            if (!subscriptionForm) return;

            subscriptionForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const formData = new FormData(subscriptionForm);
                const submitBtn = subscriptionForm.querySelector('button[type="submit"]');
                const originalText = submitBtn?.textContent;

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Subscribing...';
                }

                fetch('/subscription', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        subscriptionForm.reset();
                        this.showNotification('Successfully subscribed!', 'success');
                    } else {
                        this.showNotification(data.message || 'Subscription failed', 'error');
                    }
                })
                .catch(error => {
                    console.error('Subscription error:', error);
                    this.showNotification('An error occurred. Please try again.', 'error');
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText || 'Subscribe';
                    }
                });
            });
        },

        // Show notification
        showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification notification--${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => notification.classList.add('show'), 10);
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => App.init());
    } else {
        App.init();
    }

    // Export to global scope for compatibility
    window.App = App;
})();

