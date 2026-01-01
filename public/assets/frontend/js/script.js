// Main Application JavaScript - Optimized
(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        debounceDelay: 300,
        throttleDelay: 100,
        isDevelopment: false // Set to true for development
    };

    // Utility Functions
    const Utils = {
        // Debounce function
        debounce: function(func, wait) {
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

        // Throttle function
        throttle: function(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        // Log function (only in development)
        log: function(...args) {
            if (CONFIG.isDevelopment) {
                console.log(...args);
            }
        },

        // Error log (always show)
        error: function(...args) {
            console.error(...args);
        }
    };

    // Wait for jQuery and DOM to be ready
    function initScripts() {
        // Check if jQuery is available
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            Utils.log('jQuery is not loaded yet, retrying...');
            setTimeout(initScripts, 100);
            return;
        }

        // Initialize all modules
        Carousels.init();
        Animations.init();
        Subscription.init();
        ScrollToTop.init();
        CategoryPage.init();
        AddressForm.init();
        // CartSidebar and WishlistSidebar removed - handled by functions.js (Cart and Wishlist modules)
        RegisterPage.init();
        ProductDetails.init();
        Select2Init.init();
        HeaderSearch.init();
    }

    // Carousels Module
    const Carousels = {
        init: function() {
            this.initSlickSlider();
            this.initOwlCarousels();
            this.initCuteStationeryCarousels();
            this.initProductTabs();
            this.initCuteStationeryTabs();
        },

        initSlickSlider: function() {
            if (typeof jQuery.fn.slick === 'undefined') {
                Utils.log('Slick carousel is not loaded yet');
                return;
            }

            $('.slider').slick({
                dots: true,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear',
                autoplay: true,
                autoplaySpeed: 5000,
                arrows: false, // Removed next/previous buttons - using dot navigation only
                responsive: [{
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        dots: true // Keep dots on mobile too
                    }
                }]
            });
        },

        initOwlCarousels: function() {
            if (typeof $.fn.owlCarousel === 'undefined') return;

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
        },

        initCuteStationeryCarousels: function() {
            if (typeof $.fn.owlCarousel === 'undefined') return;

            $('.cute-stationery-carousel').each(function(index) {
                try {
                    $(this).owlCarousel({
                        loop: true,
                        margin: 30,
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
                    Utils.log('Carousel', index + 1, 'initialized successfully');
                } catch (error) {
                    Utils.error('Error initializing carousel', index + 1, ':', error);
                }
            });
        },

        initProductTabs: function() {
            // Use event delegation
            $(document).on('click', '.products__tab', function() {
                const targetTab = $(this).data('tab');
                $('.products__tab').removeClass('products__tab--active');
                $('.products__content').removeClass('products__content--active');
                $(this).addClass('products__tab--active');
                $('#' + targetTab).addClass('products__content--active');
                $('#' + targetTab + ' .products-carousel').trigger('refresh.owl.carousel');
            });
        },

        initCuteStationeryTabs: function() {
            // Use event delegation
            $(document).on('click', '.cute-stationery__nav-item', function(e) {
                e.preventDefault();
                const category = $(this).data('category');
                Utils.log('Tab clicked:', category);

                $('.cute-stationery__nav-item').removeClass('active');
                $(this).addClass('active');
                $('.cute-stationery__tab-content').removeClass('active');
                $('#' + category + '-content').addClass('active');

                setTimeout(function() {
                    $('#' + category + '-content .cute-stationery-carousel').trigger('refresh.owl.carousel');
                }, 100);
            });
        }
    };

    // Animations Module
    const Animations = {
        init: function() {
            if (typeof IntersectionObserver === 'undefined') return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.feature-card, .about-text, .contact h2').forEach(el => {
                observer.observe(el);
            });
        }
    };

    // Subscription Module
    const Subscription = {
        init: function() {
            const form = $('#subscriptionForm');
            if (!form.length) return;

            form.on('submit', (e) => {
                e.preventDefault();
                this.handleSubmit(form);
            });
        },

        handleSubmit: function(form) {
            const emailInput = form.find('#subscriptionEmail');
            const submitBtn = form.find('#subscriptionBtn');
            const btnText = submitBtn.find('.subscription-btn-text');
            const btnLoader = submitBtn.find('.subscription-btn-loader');
            const email = emailInput.val().trim();
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $('#subscriptionMessage').hide().removeClass('subscription-message--success subscription-message--error');

            if (!email || !this.isValidEmail(email)) {
                this.showMessage('Please enter a valid email address.', 'error');
                return;
            }

            submitBtn.prop('disabled', true);
            btnText.hide();
            btnLoader.show();

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: { email: email, _token: csrfToken },
                dataType: 'json',
                success: (response) => {
                    if (response.success) {
                        this.showMessage(response.message || 'Thank you for subscribing! You will receive our latest offers and updates.', 'success');
                        emailInput.val('');
                    } else {
                        this.showMessage(response.message || 'An error occurred. Please try again.', 'error');
                    }
                },
                error: (xhr) => {
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors)[0][0];
                    } else if (xhr.status === 409) {
                        errorMessage = 'This email is already subscribed to our newsletter.';
                    }
                    this.showMessage(errorMessage, 'error');
                },
                complete: () => {
                    submitBtn.prop('disabled', false);
                    btnText.show();
                    btnLoader.hide();
                }
            });
        },

        isValidEmail: function(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        showMessage: function(message, type) {
            const messageDiv = $('#subscriptionMessage');
            messageDiv.removeClass('subscription-message--success subscription-message--error')
                      .addClass('subscription-message--' + type)
                      .html('<span>' + message + '</span>')
                      .fadeIn();

            if (type === 'success') {
                setTimeout(() => messageDiv.fadeOut(), 5000);
            }
        }
    };

    // Scroll to Top Module
    const ScrollToTop = {
        init: function() {
            const scrollToTopBtn = $('#scrollToTop');
            if (!scrollToTopBtn.length) return;

            // Throttled scroll handler
            const handleScroll = Utils.throttle(() => {
                if ($(window).scrollTop() > 300) {
                    scrollToTopBtn.addClass('show');
                } else {
                    scrollToTopBtn.removeClass('show');
                }
            }, CONFIG.throttleDelay);

            $(window).on('scroll', handleScroll);

            scrollToTopBtn.on('click', () => {
                $('html, body').animate({ scrollTop: 0 }, 800);
            });
        }
    };

    // Category Page Module
    const CategoryPage = {
        init: function() {
            this.initPriceRange();
            this.initViewToggle();
            this.initFilters();
        },

        initPriceRange: function() {
            const priceRange = document.getElementById('priceRange');
            const priceMinDisplay = document.querySelector('.price-min');
            const priceMaxDisplay = document.querySelector('.price-max');

            if (!priceRange) return;

            priceRange.value = 100;
            if (priceMinDisplay) priceMinDisplay.textContent = '$0';
            if (priceMaxDisplay) priceMaxDisplay.textContent = '$100';

            priceRange.addEventListener('input', Utils.throttle(() => {
                const value = parseInt(priceRange.value);
                if (priceMaxDisplay) priceMaxDisplay.textContent = '$' + value;
                this.applyPriceFilter();
            }, CONFIG.throttleDelay));
        },

        initViewToggle: function() {
            $(document).on('click', '.view-btn', function() {
                const view = $(this).data('view');
                $('.view-btn').removeClass('active');
                $(this).addClass('active');
                $('#productsGrid').removeClass('products-grid--list').addClass('products-grid--' + view);
                if (view === 'list') {
                    $('#productsGrid').addClass('products-grid--list');
                }
            });
        },

        initFilters: function() {
            // Sub-category filter
            $(document).on('click', '.sidebar-subcategory__link', function(e) {
                e.preventDefault();
                $('.sidebar-subcategory__link').removeClass('active');
                $(this).addClass('active');
            });

            // Brand filter
            $(document).on('change', '.brand-checkbox input', function() {
                const brand = $(this).parent().text().trim();
                const isChecked = $(this).is(':checked');
                Utils.log('Brand filter:', { brand, isChecked });
            });

            // Clear filters
            $(document).on('click', '.clear-filters-btn', () => {
                $('.brand-checkbox input').prop('checked', false);
                $('#priceRange').val(100);
                $('.price-min').text('$0');
                $('.price-max').text('$100');
                $('.sidebar-category__link').removeClass('active');
                $('.sidebar-category__link:first').addClass('active');
            });

            // Sort functionality
            $(document).on('change', '.sort-select', function() {
                const sortBy = $(this).val();
                Utils.log('Sort by:', sortBy);
            });
        },

        applyPriceFilter: function() {
            const maxPrice = $('#priceRange').val();
            if (maxPrice) {
                Utils.log('Price filter applied:', { min: 0, max: maxPrice });
            }
        }
    };

    // Address Form Module
    const AddressForm = {
        init: function() {
            this.initAddAddress();
            this.initSameAsBilling();
            this.initOrderDetails();
        },

        initAddAddress: function() {
            $(document).on('click', '#addAddressBtn', function(e) {
                e.preventDefault();
                $('#addressFormContainer').slideDown(300);
                $('html, body').animate({
                    scrollTop: $('#addressFormContainer').offset().top - 100
                }, 300);
            });

            $(document).on('click', '#cancelAddressBtn', function(e) {
                e.preventDefault();
                $('#addressFormContainer').slideUp(300);
                $('#addAddressForm')[0].reset();
            });
        },

        initSameAsBilling: function() {
            $(document).on('change', '#sameAsBilling', function() {
                if ($(this).is(':checked')) {
                    $('#shippingFirstName').val($('#billingFirstName').val()).prop('readonly', true);
                    $('#shippingLastName').val($('#billingLastName').val()).prop('readonly', true);
                    $('#shippingEmail').val($('#billingEmail').val()).prop('readonly', true);
                    $('#shippingPhone').val($('#billingPhone').val()).prop('readonly', true);
                    $('#shippingAddress').val($('#billingAddress').val()).prop('readonly', true);
                    $('#shippingAddress2').val($('#billingAddress2').val()).prop('readonly', true);
                    $('#shippingCity').val($('#billingCity').val()).prop('readonly', true);
                    $('#shippingState').val($('#billingState').val()).prop('readonly', true);
                    $('#shippingZip').val($('#billingZip').val()).prop('readonly', true);
                    $('#shippingCountry').val($('#billingCountry').val()).prop('readonly', true);
                    $('#shippingDetails').slideUp(200);
                } else {
                    $('#shippingDetails input, #shippingDetails select').prop('readonly', false);
                    $('#shippingDetails').slideDown(200);
                }
            });
        },

        initOrderDetails: function() {
            $(document).on('click', '.view-order-details', function(e) {
                e.preventDefault();
                $('.account-nav__link').removeClass('account-nav__link--active');
                $('a[href="#my-orders"]').addClass('account-nav__link--active');
                $('.account-content').addClass('account-content--hidden');
                $('#order-details').removeClass('account-content--hidden');
                $('html, body').animate({
                    scrollTop: $('.account-section').offset().top - 100
                }, 300);
            });

            $(document).on('click', '.back-link', function(e) {
                e.preventDefault();
                $('.account-nav__link').removeClass('account-nav__link--active');
                $('a[href="#my-orders"]').addClass('account-nav__link--active');
                $('.account-content').addClass('account-content--hidden');
                $('#my-orders').removeClass('account-content--hidden');
                $('html, body').animate({
                    scrollTop: $('.account-section').offset().top - 100
                }, 300);
            });
        }
    };

    // Cart Sidebar and Wishlist Sidebar modules removed
    // These are now handled by functions.js (Cart and Wishlist modules) to avoid duplicate event handlers

    // Register Page Module
    const RegisterPage = {
        init: function() {
            this.initPasswordToggle();
            this.initPasswordStrength();
            this.initFormFocus();
        },

        initPasswordToggle: function() {
            // Register page password toggle
            $(document).on('click', '#togglePassword', function() {
                const passwordInput = $('#registerPassword');
                const passwordIcon = $('#passwordIcon');
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Register page confirm password toggle
            $(document).on('click', '#toggleConfirmPassword', function() {
                const confirmPasswordInput = $('#registerConfirmPassword');
                const confirmPasswordIcon = $('#confirmPasswordIcon');
                if (confirmPasswordInput.attr('type') === 'password') {
                    confirmPasswordInput.attr('type', 'text');
                    confirmPasswordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    confirmPasswordInput.attr('type', 'password');
                    confirmPasswordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Login page password toggle
            $(document).on('click', '#toggleLoginPassword', function() {
                const loginPasswordInput = $('#loginPassword');
                const loginPasswordIcon = $('#loginPasswordIcon');
                if (loginPasswordInput.attr('type') === 'password') {
                    loginPasswordInput.attr('type', 'text');
                    loginPasswordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    loginPasswordInput.attr('type', 'password');
                    loginPasswordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Reset password page password toggle
            $(document).on('click', '#toggleResetPassword', function() {
                const resetPasswordInput = $('#resetPassword');
                const resetPasswordIcon = $('#resetPasswordIcon');
                if (resetPasswordInput.attr('type') === 'password') {
                    resetPasswordInput.attr('type', 'text');
                    resetPasswordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    resetPasswordInput.attr('type', 'password');
                    resetPasswordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Reset password page confirm password toggle
            $(document).on('click', '#toggleResetConfirmPassword', function() {
                const resetConfirmPasswordInput = $('#resetConfirmPassword');
                const resetConfirmPasswordIcon = $('#resetConfirmPasswordIcon');
                if (resetConfirmPasswordInput.attr('type') === 'password') {
                    resetConfirmPasswordInput.attr('type', 'text');
                    resetConfirmPasswordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    resetConfirmPasswordInput.attr('type', 'password');
                    resetConfirmPasswordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
        },

        initPasswordStrength: function() {
            $('#registerPassword').on('input', function() {
                const password = $(this).val();
                const strengthBar = $('#passwordStrengthBar');
                const strengthContainer = $('#passwordStrength');
                const passwordHint = $('#passwordHint');

                if (password.length === 0) {
                    strengthContainer.css('display', 'none');
                    passwordHint.text('');
                    return;
                }

                strengthContainer.css('display', 'block');
                let strength = 0;
                let hint = [];

                if (password.length >= 8) strength += 1; else hint.push('At least 8 characters');
                if (/[a-z]/.test(password)) strength += 1; else hint.push('lowercase letter');
                if (/[A-Z]/.test(password)) strength += 1; else hint.push('uppercase letter');
                if (/[0-9]/.test(password)) strength += 1; else hint.push('number');
                if (/[^A-Za-z0-9]/.test(password)) strength += 1; else hint.push('special character');

                strengthBar.removeClass('weak medium strong');
                if (strength <= 2) {
                    strengthBar.addClass('weak');
                    passwordHint.text('Weak password. Add: ' + hint.slice(0, 2).join(', ')).css('color', '#dc3545');
                } else if (strength <= 3) {
                    strengthBar.addClass('medium');
                    passwordHint.text('Medium password. Add: ' + hint.slice(0, 1).join(', ')).css('color', '#ffc107');
                } else {
                    strengthBar.addClass('strong');
                    passwordHint.text('Strong password!').css('color', '#28a745');
                }
            });

            $('#registerConfirmPassword').on('input', function() {
                const password = $('#registerPassword').val();
                const confirmPassword = $(this).val();
                const matchMessage = $('#passwordMatch');

                if (confirmPassword.length === 0) {
                    matchMessage.text('');
                    $(this).css('border-color', '#e9ecef');
                    return;
                }

                if (password === confirmPassword) {
                    matchMessage.text('✓ Passwords match').css('color', '#28a745');
                    $(this).css('border-color', '#28a745');
                } else {
                    matchMessage.text('✗ Passwords do not match').css('color', '#dc3545');
                    $(this).css('border-color', '#dc3545');
                }
            });
        },

        initFormFocus: function() {
            const focusStyles = {
                'border-color': 'var(--coral-red)',
                'background': '#ffffff',
                'box-shadow': '0 0 0 4px rgba(233, 92, 103, 0.1)',
                'transform': 'translateY(-2px)'
            };

            const blurStyles = {
                'border-color': '#e9ecef',
                'background': '#f8f9fa',
                'box-shadow': 'none',
                'transform': 'translateY(0)'
            };

            $(document).on('focus', '.register-form-input, .login-form-input, .forgot-password-form-input', function() {
                $(this).css(focusStyles);
            });

            $(document).on('blur', '.register-form-input, .login-form-input, .forgot-password-form-input', function() {
                if (!$(this).val()) {
                    $(this).css(blurStyles);
                }
            });

            // Forgot Password Form - Only hide form if there's a success message from server
            // Don't prevent default submission, let server handle validation
            // The form will be hidden by server-side success message display

            // User Dropdown
            $(document).on('click', '#userDropdownTrigger', function(e) {
                e.preventDefault();
                $('#userDropdown').toggleClass('open');
            });

            $(document).on('click', function(e) {
                const userDropdown = $('#userDropdown');
                if (!userDropdown.is(e.target) && userDropdown.has(e.target).length === 0) {
                    userDropdown.removeClass('open');
                }
            });
        }
    };

    // Product Details Module
    const ProductDetails = {
        init: function() {
            this.initThumbnails();
            this.initQuantity();
        },

        initThumbnails: function() {
            $(document).on('click', '.thumbnail-item', function() {
                const imageUrl = $(this).data('image');
                const mainImage = $('#mainImage');
                if (imageUrl && mainImage.length) {
                    mainImage.attr('src', imageUrl);
                    $('.thumbnail-item').removeClass('active');
                    $(this).addClass('active');
                }
            });
        },

        initQuantity: function() {
            $(document).on('click', '#increaseQty', function(e) {
                e.preventDefault();
                const quantityInput = $('#quantity');
                let currentValue = parseInt(quantityInput.val()) || 1;
                const maxValue = parseInt(quantityInput.attr('max')) || 99;
                if (currentValue < maxValue) {
                    quantityInput.val(++currentValue);
                }
            });

            $(document).on('click', '#decreaseQty', function(e) {
                e.preventDefault();
                const quantityInput = $('#quantity');
                let currentValue = parseInt(quantityInput.val()) || 1;
                const minValue = parseInt(quantityInput.attr('min')) || 1;
                if (currentValue > minValue) {
                    quantityInput.val(--currentValue);
                }
            });

            $('#quantity').on('change', function() {
                let value = parseInt($(this).val()) || 1;
                const minValue = parseInt($(this).attr('min')) || 1;
                const maxValue = parseInt($(this).attr('max')) || 99;
                if (value < minValue) value = minValue;
                else if (value > maxValue) value = maxValue;
                $(this).val(value);
            });
        }
    };

    // Select2 Initialization
    const Select2Init = {
        init: function() {
            if (typeof $.fn.select2 === 'undefined') return;
            $('#editCountry, #editRegion, #editDistrict, #editCity').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    };

    // Header Search Module
    const HeaderSearch = {
        init: function() {
            const searchInput = document.getElementById('header-search-input');
            const searchBtn = document.getElementById('header-search-btn');
            const searchDropdown = document.getElementById('search-results-dropdown');
            const searchResultsList = document.getElementById('search-results-list');
            const searchLoading = document.getElementById('search-loading');
            const searchFooter = document.getElementById('search-results-footer');
            const viewAllResults = document.getElementById('view-all-results');

            if (!searchInput || !searchDropdown) return;

            let searchTimeout;
            let isSearching = false;

            const performSearch = Utils.debounce((query) => {
                if (isSearching || query.length < 2) {
                    if (query.length < 2) {
                        searchDropdown.style.display = 'none';
                    }
                    return;
                }

                searchLoading.style.display = 'block';
                searchResultsList.innerHTML = '';
                searchFooter.style.display = 'none';
                searchDropdown.style.display = 'block';
                isSearching = true;

                const url = new URL('/search/results/render', window.location.origin);
                url.searchParams.set('q', query);

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    searchLoading.style.display = 'none';
                    isSearching = false;

                    if (data.success && data.html && data.html.trim() !== '') {
                        searchResultsList.innerHTML = data.html;
                        searchFooter.style.display = 'block';
                        if (viewAllResults) {
                            viewAllResults.href = `/search?q=${encodeURIComponent(query)}`;
                        }
                    } else {
                        searchResultsList.innerHTML = '<div class="search-result-item" style="text-align: center; color: #6c757d;">No products found</div>';
                        searchFooter.style.display = 'none';
                    }
                })
                .catch(error => {
                    Utils.error('Search error:', error);
                    searchLoading.style.display = 'none';
                    isSearching = false;
                    searchResultsList.innerHTML = '<div class="search-result-item" style="text-align: center; color: #dc3545;">Error loading results</div>';
                });
            }, CONFIG.debounceDelay);

            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                clearTimeout(searchTimeout);
                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => performSearch(query), CONFIG.debounceDelay);
                } else {
                    searchDropdown.style.display = 'none';
                }
            });

            if (searchBtn) {
                searchBtn.addEventListener('click', function() {
                    const query = searchInput.value.trim();
                    if (query) {
                        window.location.href = `/search?q=${encodeURIComponent(query)}`;
                    }
                });
            }

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query) {
                        window.location.href = `/search?q=${encodeURIComponent(query)}`;
                    }
                }
            });

            document.addEventListener('click', function(e) {
                const searchContainer = document.getElementById('header-search');
                if (searchContainer && !searchContainer.contains(e.target)) {
                    searchDropdown.style.display = 'none';
                }
            });

            searchDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    };

    // Start initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScripts);
    } else {
        initScripts();
    }

})();
