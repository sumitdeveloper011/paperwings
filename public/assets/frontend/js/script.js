/**
 * Main Application JavaScript
 * Loads and initializes all modules
 */
(function() {
    'use strict';

    // Wait for jQuery and modules to be ready
    function initScripts() {
        if (typeof jQuery === 'undefined') {
            const Utils = window.ScriptUtils || { log: () => {} };
            Utils.log('jQuery is not loaded yet, retrying...');
            setTimeout(initScripts, 100);
            return;
        }

        const $ = jQuery;
        const Utils = window.ScriptUtils || { log: () => {}, throttle: (fn, delay) => fn };
        const CONFIG = Utils.getConfig ? Utils.getConfig() : { throttleDelay: 100 };

        // Initialize remaining modules (smaller ones kept here)
        Animations.init();
        Subscription.init();
        ScrollToTop.init(Utils, CONFIG);
        CategoryPage.init(Utils, CONFIG);
        ShopPage.init();
        AddressForm.init();
        RegisterPage.init();
        ProductDetails.init();
        Select2Init.init();
        MegaMenu.init();
    }

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
        init: function(Utils, CONFIG) {
            const scrollToTopBtn = $('#scrollToTop');
            if (!scrollToTopBtn.length) return;

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
        init: function(Utils, CONFIG) {
            this.initPriceRange(Utils, CONFIG);
            this.initViewToggle();
            this.initFilters(Utils);
            this.initCategorySearch();
            this.initLoadMoreCategories();
        },

        initPriceRange: function(Utils, CONFIG) {
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
                this.applyPriceFilter(Utils);
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

        initFilters: function(Utils) {
            $(document).on('click', '.sidebar-subcategory__link', function(e) {
                e.preventDefault();
                $('.sidebar-subcategory__link').removeClass('active');
                $(this).addClass('active');
            });

            $(document).on('change', '.brand-checkbox input', function() {
                const brand = $(this).parent().text().trim();
                const isChecked = $(this).is(':checked');
                Utils.log('Brand filter:', { brand, isChecked });
            });

            $(document).on('click', '.clear-filters-btn', () => {
                $('.brand-checkbox input').prop('checked', false);
                $('#priceRange').val(100);
                $('.price-min').text('$0');
                $('.price-max').text('$100');
                $('.sidebar-category__link').removeClass('active');
                $('.sidebar-category__link:first').addClass('active');
            });

            $(document).on('change', '.sort-select', function() {
                const sortBy = $(this).val();
                Utils.log('Sort by:', sortBy);
            });
        },

        applyPriceFilter: function(Utils) {
            const maxPrice = $('#priceRange').val();
            if (maxPrice) {
                Utils.log('Price filter applied:', { min: 0, max: maxPrice });
            }
        },

        initCategorySearch: function() {
            const categorySearch = document.getElementById('categorySearch');
            const categoriesList = document.getElementById('categoriesList');

            if (!categorySearch || !categoriesList) return;

            categorySearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const categoryItems = categoriesList.querySelectorAll('.sidebar-category');

                categoryItems.forEach(item => {
                    const categoryName = item.getAttribute('data-category-name');
                    if (categoryName && categoryName.includes(searchTerm)) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });

                // Show/hide empty message
                const visibleItems = categoriesList.querySelectorAll('.sidebar-category:not(.hidden)');
                const emptyMessage = categoriesList.querySelector('.sidebar-category--empty');

                if (visibleItems.length === 0 && searchTerm !== '') {
                    if (!emptyMessage || emptyMessage.classList.contains('hidden')) {
                        const noResults = document.createElement('li');
                        noResults.className = 'sidebar-category--empty';
                        noResults.innerHTML = '<span class="sidebar-category__empty-text">No categories found</span>';
                        if (emptyMessage) {
                            emptyMessage.replaceWith(noResults);
                        } else {
                            categoriesList.appendChild(noResults);
                        }
                    }
                } else if (emptyMessage && searchTerm === '') {
                    emptyMessage.remove();
                }
            });
        },

        initLoadMoreCategories: function() {
            const loadMoreBtn = document.getElementById('loadMoreCategories');
            const categoriesList = document.getElementById('categoriesListContainer');

            if (!loadMoreBtn || !categoriesList) return;

            const itemsPerPage = parseInt(loadMoreBtn.getAttribute('data-items-per-page')) || 10;
            const allItems = Array.from(categoriesList.querySelectorAll('.sidebar-category'));

            // Ensure items beyond the first page are hidden (in case HTML didn't add the class)
            allItems.forEach((item, index) => {
                if (index >= itemsPerPage) {
                    if (!item.classList.contains('category-item-hidden')) {
                        item.classList.add('category-item-hidden');
                    }
                } else {
                    // Ensure first 10 items are visible
                    item.classList.remove('category-item-hidden');
                    item.classList.remove('show-item');
                }
            });

            loadMoreBtn.addEventListener('click', function() {
                const hiddenItems = categoriesList.querySelectorAll('.category-item-hidden:not(.hidden)');

                if (hiddenItems.length === 0) {
                    // Show all items
                    allItems.forEach(item => {
                        if (!item.classList.contains('hidden')) {
                            item.classList.remove('category-item-hidden');
                            item.classList.add('show-item');
                        }
                    });
                    this.querySelector('.load-more-text').style.display = 'none';
                    this.querySelector('.load-all-text').style.display = 'inline';
                    this.classList.add('hide-button');
                } else {
                    // Show next batch
                    const itemsToShow = Math.min(itemsPerPage, hiddenItems.length);
                    for (let i = 0; i < itemsToShow; i++) {
                        hiddenItems[i].classList.remove('category-item-hidden');
                        hiddenItems[i].classList.add('show-item');
                    }

                    // Check if all items are now visible
                    const remainingHidden = categoriesList.querySelectorAll('.category-item-hidden:not(.hidden)');
                    if (remainingHidden.length === 0) {
                        this.querySelector('.load-more-text').style.display = 'none';
                        this.querySelector('.load-all-text').style.display = 'inline';
                        this.classList.add('hide-button');
                    }
                }
            });
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

    // Register Page Module
    const RegisterPage = {
        init: function() {
            this.initPasswordToggle();
            this.initPasswordStrength();
            this.initFormFocus();
            this.initInvalidFeedback();
        },

        initPasswordToggle: function() {
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
            // Function to clear server-side validation errors
            function clearPasswordErrors(input) {
                const formGroup = input.closest('.form-group');
                const invalidFeedback = formGroup.find('.invalid-feedback');

                // Remove is-invalid class
                input.removeClass('is-invalid');

                // Hide error message
                invalidFeedback.css('display', 'none');

                // Reset border color (remove inline styles that might have been set by validation)
                // The default styles will be applied from CSS
                input.css({
                    'border-color': '',
                    'background-color': ''
                });
            }

            $('#registerPassword').on('input', function() {
                const password = $(this).val();
                const strengthBar = $('#passwordStrengthBar');
                const strengthContainer = $('#passwordStrength');
                const passwordHint = $('#passwordHint');

                // Clear server-side errors when user starts typing
                clearPasswordErrors($(this));

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

                // Clear server-side errors when user starts typing
                clearPasswordErrors($(this));

                if (confirmPassword.length === 0) {
                    matchMessage.text('');
                    $(this).css('border-color', '');
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

            // Function to clear server-side validation errors
            function clearFieldErrors(input) {
                if (input.hasClass('is-invalid')) {
                    const formGroup = input.closest('.form-group');
                    const invalidFeedback = formGroup.find('.invalid-feedback');

                    // Remove is-invalid class
                    input.removeClass('is-invalid');

                    // Hide error message
                    invalidFeedback.css('display', 'none');
                }
            }

            $(document).on('focus', '.register-form-input, .login-form-input, .forgot-password-form-input', function() {
                $(this).css(focusStyles);

                // Clear errors when field is focused (for password fields, this happens on input too)
                if ($(this).hasClass('register-form-input--password')) {
                    // For password fields, errors are cleared on input, so we don't need to do it here
                    // But we can still clear on focus for immediate feedback
                    clearFieldErrors($(this));
                } else {
                    clearFieldErrors($(this));
                }
            });

            $(document).on('blur', '.register-form-input, .login-form-input, .forgot-password-form-input', function() {
                if (!$(this).val()) {
                    $(this).css(blurStyles);
                }
            });

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
        },

        initInvalidFeedback: function() {
            // Show invalid-feedback for password fields when input has is-invalid class
            // This handles cases where CSS :has() selector is not supported
            function showInvalidFeedback() {
                $('.password-input-wrapper').each(function() {
                    const wrapper = $(this);
                    const input = wrapper.find('input.is-invalid');

                    if (input.length > 0) {
                        // Find the invalid-feedback within the same form-group
                        const formGroup = wrapper.closest('.form-group');
                        const invalidFeedback = formGroup.find('.invalid-feedback');

                        if (invalidFeedback.length > 0) {
                            invalidFeedback.css('display', 'block');
                        }
                    }
                });
            }

            // Run on page load
            showInvalidFeedback();

            // Also check when form is submitted (in case errors are added dynamically)
            $('#registerForm').on('submit', function() {
                setTimeout(showInvalidFeedback, 100);
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

    // Shop Page Module
    const ShopPage = {
        init: function() {
            this.initCategorySearch();
            this.initTagSearch();
            this.initLoadMoreCategories();
            this.initLoadMoreTags();
        },

        initCategorySearch: function() {
            const categorySearch = document.getElementById('categorySearch');
            const categoriesList = document.getElementById('categoriesList');

            if (!categorySearch || !categoriesList) return;

            categorySearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const categoryItems = categoriesList.querySelectorAll('.sidebar-category');

                categoryItems.forEach(item => {
                    const categoryName = item.getAttribute('data-category-name');
                    if (categoryName && categoryName.includes(searchTerm)) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });

                // Show/hide empty message
                const visibleItems = categoriesList.querySelectorAll('.sidebar-category:not(.hidden)');
                const emptyMessage = categoriesList.querySelector('.sidebar-category--empty');

                if (visibleItems.length === 0 && searchTerm !== '') {
                    if (!emptyMessage || emptyMessage.classList.contains('hidden')) {
                        const noResults = document.createElement('li');
                        noResults.className = 'sidebar-category--empty';
                        noResults.innerHTML = '<span class="sidebar-category__empty-text">No categories found</span>';
                        if (emptyMessage) {
                            emptyMessage.replaceWith(noResults);
                        } else {
                            categoriesList.appendChild(noResults);
                        }
                    }
                } else if (emptyMessage && searchTerm === '') {
                    emptyMessage.remove();
                }
            });
        },

        initTagSearch: function() {
            const tagSearch = document.getElementById('tagSearch');
            const tagsList = document.getElementById('tagsList');

            if (!tagSearch || !tagsList) return;

            tagSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const tagItems = tagsList.querySelectorAll('.sidebar-category');

                tagItems.forEach(item => {
                    const tagName = item.getAttribute('data-category-name');
                    if (tagName && tagName.includes(searchTerm)) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });

                // Show/hide empty message
                const visibleItems = tagsList.querySelectorAll('.sidebar-category:not(.hidden)');
                const emptyMessage = tagsList.querySelector('.sidebar-category--empty');

                if (visibleItems.length === 0 && searchTerm !== '') {
                    if (!emptyMessage || emptyMessage.classList.contains('hidden')) {
                        const noResults = document.createElement('li');
                        noResults.className = 'sidebar-category--empty';
                        noResults.innerHTML = '<span class="sidebar-category__empty-text">No tags found</span>';
                        if (emptyMessage) {
                            emptyMessage.replaceWith(noResults);
                        } else {
                            tagsList.appendChild(noResults);
                        }
                    }
                } else if (emptyMessage && searchTerm === '') {
                    emptyMessage.remove();
                }
            });
        },

        initLoadMoreCategories: function() {
            const loadMoreBtn = document.getElementById('loadMoreCategories');
            const categoriesList = document.getElementById('categoriesListContainer');

            if (!loadMoreBtn || !categoriesList) return;

            const itemsPerPage = parseInt(loadMoreBtn.getAttribute('data-items-per-page')) || 10;
            const allItems = Array.from(categoriesList.querySelectorAll('.sidebar-category'));

            // Ensure items beyond the first page are hidden (in case HTML didn't add the class)
            allItems.forEach((item, index) => {
                if (index >= itemsPerPage) {
                    if (!item.classList.contains('category-item-hidden')) {
                        item.classList.add('category-item-hidden');
                    }
                } else {
                    // Ensure first 10 items are visible
                    item.classList.remove('category-item-hidden');
                    item.classList.remove('show-item');
                }
            });

            loadMoreBtn.addEventListener('click', function() {
                const hiddenItems = categoriesList.querySelectorAll('.category-item-hidden:not(.hidden)');

                if (hiddenItems.length === 0) {
                    // Show all items
                    allItems.forEach(item => {
                        if (!item.classList.contains('hidden')) {
                            item.classList.remove('category-item-hidden');
                            item.classList.add('show-item');
                        }
                    });
                    this.querySelector('.load-more-text').style.display = 'none';
                    this.querySelector('.load-all-text').style.display = 'inline';
                    this.classList.add('hide-button');
                } else {
                    // Show next batch
                    const itemsToShow = Math.min(itemsPerPage, hiddenItems.length);
                    for (let i = 0; i < itemsToShow; i++) {
                        hiddenItems[i].classList.remove('category-item-hidden');
                        hiddenItems[i].classList.add('show-item');
                    }

                    // Check if all items are now visible
                    const remainingHidden = categoriesList.querySelectorAll('.category-item-hidden:not(.hidden)');
                    if (remainingHidden.length === 0) {
                        this.querySelector('.load-more-text').style.display = 'none';
                        this.querySelector('.load-all-text').style.display = 'inline';
                        this.classList.add('hide-button');
                    }
                }
            });
        },

        initLoadMoreTags: function() {
            const loadMoreBtn = document.getElementById('loadMoreTags');
            const tagsList = document.getElementById('tagsListContainer');

            if (!loadMoreBtn || !tagsList) return;

            const itemsPerPage = parseInt(loadMoreBtn.getAttribute('data-items-per-page')) || 10;
            const allItems = Array.from(tagsList.querySelectorAll('.sidebar-category'));

            // Ensure items beyond the first page are hidden (in case HTML didn't add the class)
            allItems.forEach((item, index) => {
                if (index >= itemsPerPage) {
                    if (!item.classList.contains('category-item-hidden')) {
                        item.classList.add('category-item-hidden');
                    }
                } else {
                    // Ensure first 10 items are visible
                    item.classList.remove('category-item-hidden');
                    item.classList.remove('show-item');
                }
            });

            loadMoreBtn.addEventListener('click', function() {
                const hiddenItems = tagsList.querySelectorAll('.category-item-hidden:not(.hidden)');

                if (hiddenItems.length === 0) {
                    // Show all items
                    allItems.forEach(item => {
                        if (!item.classList.contains('hidden')) {
                            item.classList.remove('category-item-hidden');
                            item.classList.add('show-item');
                        }
                    });
                    this.querySelector('.load-more-text').style.display = 'none';
                    this.querySelector('.load-all-text').style.display = 'inline';
                    this.classList.add('hide-button');
                } else {
                    // Show next batch
                    const itemsToShow = Math.min(itemsPerPage, hiddenItems.length);
                    for (let i = 0; i < itemsToShow; i++) {
                        hiddenItems[i].classList.remove('category-item-hidden');
                        hiddenItems[i].classList.add('show-item');
                    }

                    // Check if all items are now visible
                    const remainingHidden = tagsList.querySelectorAll('.category-item-hidden:not(.hidden)');
                    if (remainingHidden.length === 0) {
                        this.querySelector('.load-more-text').style.display = 'none';
                        this.querySelector('.load-all-text').style.display = 'inline';
                        this.classList.add('hide-button');
                    }
                }
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

    // Mega Menu Module
    const MegaMenu = {
        init: function() {
            const megaMenuWrapper = document.querySelector('.mega-menu-wrapper');
            const megaMenuTrigger = document.getElementById('megaMenuTrigger');
            const megaMenu = document.getElementById('megaMenu');

            if (!megaMenuWrapper || !megaMenuTrigger || !megaMenu) {
                console.log('Mega Menu elements not found');
                return;
            }

            console.log('Mega Menu initialized');

            // Toggle menu on click (works for both desktop and mobile)
            megaMenuTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Mega menu button clicked');
                megaMenu.style.display = 'block';
                megaMenuWrapper.classList.toggle('active');
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!megaMenuWrapper.contains(e.target)) {
                    megaMenuWrapper.classList.remove('active');
                    megaMenu.style.display = 'none';
                }
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && megaMenuWrapper.classList.contains('active')) {
                    megaMenuWrapper.classList.remove('active');
                }
            });

            // Prevent menu from closing when clicking inside
            megaMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Also add hover support for desktop
            let hoverTimeout;

            megaMenuWrapper.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
                megaMenu.style.display = 'block';
                this.classList.add('active');
            });

            megaMenuWrapper.addEventListener('mouseleave', function(e) {
                // Check if mouse is moving to the menu
                const relatedTarget = e.relatedTarget;
                if (relatedTarget && (megaMenu.contains(relatedTarget) || megaMenu === relatedTarget)) {
                    return; // Don't close if moving to menu
                }

                // Add small delay before closing
                hoverTimeout = setTimeout(() => {
                    if (!this.classList.contains('force-open')) {
                        this.classList.remove('active');
                        megaMenu.style.display = 'none';
                    }
                }, 200); // 200ms delay
            });

            // Keep menu open when hovering over it
            megaMenu.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
                megaMenuWrapper.classList.add('active');
                megaMenu.style.display = 'block';
            });

            megaMenu.addEventListener('mouseleave', function(e) {
                const relatedTarget = e.relatedTarget;
                if (relatedTarget && (megaMenuWrapper.contains(relatedTarget) || megaMenuWrapper === relatedTarget)) {
                    return; // Don't close if moving back to button
                }

                hoverTimeout = setTimeout(() => {
                    if (!megaMenuWrapper.classList.contains('force-open')) {
                        megaMenuWrapper.classList.remove('active');
                        megaMenu.style.display = 'none';
                    }
                }, 200);
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
