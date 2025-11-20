// Observe elements for animation
document.querySelectorAll('.feature-card, .about-text, .contact h2').forEach(el => {
    observer.observe(el);
});

// Initialize Slick Slider
$('.slider').slick({
    dots: true,
    infinite: true,
    speed: 500,
    fade: true,
    cssEase: 'linear',
    autoplay: true,
    autoplaySpeed: 5000,
    arrows: true,
    responsive: [
        {
            breakpoint: 768,
            settings: {
                arrows: false
            }
        }
    ]
});

// Custom arrow styling
$('.slider').on('init', function() {
    $('.slick-prev').html('<i class="fas fa-chevron-left"></i>');
    $('.slick-next').html('<i class="fas fa-chevron-right"></i>');
});

// Initialize Owl Carousel for products
$(document).ready(function() {
    // Initialize all product carousels
    $('.products-carousel').owlCarousel({
        loop: true,
        margin: 5,
        nav: false,
        dots: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 3
            },
            992: {
                items: 4
            },
            1200: {
                items: 6
            }
        }
    });

    // Product tabs functionality
    $('.products__tab').on('click', function() {
        const targetTab = $(this).data('tab');

        // Remove active class from all tabs and contents
        $('.products__tab').removeClass('products__tab--active');
        $('.products__content').removeClass('products__content--active');

        // Add active class to clicked tab
        $(this).addClass('products__tab--active');

        // Show corresponding content
        $('#' + targetTab).addClass('products__content--active');

        // Refresh Owl Carousel for the new tab
        $('#' + targetTab + ' .products-carousel').trigger('refresh.owl.carousel');
    });
});

// Initialize Cute Stationery Carousel
$(document).ready(function() {
    console.log('Document ready - initializing carousel');

    // Initialize all carousels
    initializeCuteStationeryCarousels();
});

// Function to initialize all carousels
function initializeCuteStationeryCarousels() {
    $('.cute-stationery-carousel').each(function(index) {
        console.log('Initializing carousel', index + 1);
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
                    0: {
                        items: 1
                    },
                    576: {
                        items: 2
                    },
                    768: {
                        items: 3
                    },
                    992: {
                        items: 4
                    },
                    1200: {
                        items: 6
                    }
                }
            });
            console.log('Carousel', index + 1, 'initialized successfully');
        } catch (error) {
            console.error('Error initializing carousel', index + 1, ':', error);
        }
    });
}

// Cute Stationery Navigation Tabs
$(document).ready(function() {
    // Use event delegation to ensure clicks work
    $(document).on('click', '.cute-stationery__nav-item', function(e) {
        e.preventDefault();

        const category = $(this).data('category');
        console.log('Tab clicked:', category);

        // Remove active class from all nav items
        $('.cute-stationery__nav-item').removeClass('active');

        // Add active class to clicked nav item
        $(this).addClass('active');

        // Hide all tab contents
        $('.cute-stationery__tab-content').removeClass('active');

        // Show the selected tab content
        $('#' + category + '-content').addClass('active');

        // Refresh the carousel for the active tab
        setTimeout(function() {
            $('#' + category + '-content .cute-stationery-carousel').trigger('refresh.owl.carousel');
        }, 100);

        console.log('Switched to category:', category);
    });

    // Debug: Check if elements exist
    console.log('Cute Stationery nav items found:', $('.cute-stationery__nav-item').length);
    console.log('Active nav item:', $('.cute-stationery__nav-item.active').length);
    console.log('Tab contents found:', $('.cute-stationery__tab-content').length);
});

// Function to load products by category (placeholder for future implementation)
function loadProductsByCategory(category) {
    // This function can be implemented to load different products
    // based on the selected category using AJAX
    console.log('Loading products for category:', category);
}

// Subscription Form Functionality
$(document).ready(function() {
    $('.subscription-form').on('submit', function(e) {
        e.preventDefault();

        const email = $(this).find('.subscription-form__input').val();

        if (email && isValidEmail(email)) {
            // Show success message
            showSubscriptionMessage('Thank you for subscribing! Check your email for the 20% off code.', 'success');

            // Clear the form
            $(this).find('.subscription-form__input').val('');

            // Here you would typically send the data to your server
            console.log('Subscription submitted:', email);

        } else {
            // Show error message
            showSubscriptionMessage('Please enter a valid email address.', 'error');
        }
    });

    // Email validation function
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Function to show subscription messages
    function showSubscriptionMessage(message, type) {
        // Remove any existing messages
        $('.subscription-message').remove();

        // Create message element
        const messageClass = type === 'success' ? 'subscription-message--success' : 'subscription-message--error';
        const messageHtml = `
            <div class="subscription-message ${messageClass}">
                <span>${message}</span>
                <button class="subscription-message__close">&times;</button>
            </div>
        `;

        // Insert message after the form
        $('.subscription-form').after(messageHtml);

        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                $('.subscription-message').fadeOut();
            }, 5000);
        }

        // Close button functionality
        $('.subscription-message__close').on('click', function() {
            $(this).parent().fadeOut();
        });
    }
});

// Scroll to Top Functionality
$(document).ready(function() {
    const scrollToTopBtn = $('#scrollToTop');

    // Show/hide scroll to top button based on scroll position
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            scrollToTopBtn.addClass('show');
        } else {
            scrollToTopBtn.removeClass('show');
        }
    });

    // Smooth scroll to top when button is clicked
    scrollToTopBtn.on('click', function() {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
    });

    // Hide button when at top
    $(window).on('scroll', function() {
        if ($(this).scrollTop() === 0) {
            scrollToTopBtn.removeClass('show');
        }
    });
});


// Category Page Functionality
$(document).ready(function() {
    console.log('Category page JavaScript loaded!');

    // Price range slider functionality
    const priceRange = document.getElementById('priceRange');
    const priceMinDisplay = document.querySelector('.price-min');
    const priceMaxDisplay = document.querySelector('.price-max');

    console.log('Price range elements found:', { priceRange, priceMinDisplay, priceMaxDisplay });

    if (priceRange) {
        console.log('Price range slider initialized');

        // Set initial values
        let currentValue = parseInt(priceRange.value);
        priceMinDisplay.textContent = '$0';
        priceMaxDisplay.textContent = '$' + currentValue;

        // Set initial slider value to 100
        priceRange.value = 100;
        priceMaxDisplay.textContent = '$100';

        // Update price display when slider changes
        priceRange.addEventListener('input', function() {
            const value = parseInt(this.value);
            console.log('Price range changed to:', value);

            // Update max price display
            priceMaxDisplay.textContent = '$' + value;

            // Apply price filter automatically
            applyPriceFilter();
        });

        // Test if slider is working
        console.log('Price range value:', priceRange.value);

        // Add click test to see if slider is interactive
        priceRange.addEventListener('click', function() {
            console.log('Price range clicked!');
        });
    } else {
        console.error('Price range slider not found!');
    }

    // View toggle functionality
    $('.view-btn').click(function() {
        const view = $(this).data('view');

        // Remove active class from all buttons
        $('.view-btn').removeClass('active');
        // Add active class to clicked button
        $(this).addClass('active');

        // Update products grid class
        $('#productsGrid').removeClass('products-grid--list').addClass('products-grid--' + view);

        if (view === 'list') {
            $('#productsGrid').addClass('products-grid--list');
        }
    });

    // Category filter functionality
    // $('.sidebar-category__link').click(function(e) {
    //     e.preventDefault();

    //     const $categoryItem = $(this).closest('.sidebar-category');
    //     const $subcategories = $categoryItem.find('.sidebar-subcategories');

    //     // Toggle subcategories if they exist
    //     if ($subcategories.length > 0) {
    //         $categoryItem.toggleClass('expanded');
    //     }

    //     // Remove active class from all category links
    //     $('.sidebar-category__link').removeClass('active');
    //     // Add active class to clicked link
    //     $(this).addClass('active');

    //     // Here you would typically filter products based on category
    //     console.log('Category selected:', $(this).text().trim());
    // });

    // Sub-category filter functionality
    $('.sidebar-subcategory__link').click(function(e) {
        e.preventDefault();

        // Remove active class from all sub-category links
        $('.sidebar-subcategory__link').removeClass('active');
        // Add active class to clicked link
        $(this).addClass('active');

        // Here you would typically filter products based on sub-category
        console.log('Sub-category selected:', $(this).text().trim());
    });

    // Price filter functionality - works automatically when slider changes
    function applyPriceFilter() {
        const maxPrice = $('#priceRange').val();

        if (maxPrice) {
            // Here you would typically filter products based on price range
            console.log('Price filter applied:', { min: 0, max: maxPrice });
            // You can add your product filtering logic here
        }
    }

    // Brand filter functionality
    $('.brand-checkbox input').change(function() {
        const brand = $(this).parent().text().trim();
        const isChecked = $(this).is(':checked');

        // Here you would typically filter products based on brand selection
        console.log('Brand filter:', { brand, isChecked });
    });

    // Clear filters functionality
    $('.clear-filters-btn').click(function() {
        // Reset all checkboxes
        $('.brand-checkbox input').prop('checked', false);

        // Reset price range slider
        $('#priceRange').val(100);
        $('.price-min').text('$0');
        $('.price-max').text('$100');

        // Reset category selection to first one
        $('.sidebar-category__link').removeClass('active');
        $('.sidebar-category__link:first').addClass('active');

        // Here you would typically reset all product filters
        console.log('All filters cleared');
    });

    // Sort functionality
    $('.sort-select').change(function() {
        const sortBy = $(this).val();

        // Here you would typically sort products based on selection
        console.log('Sort by:', sortBy);
    });
});

// Account Page Tab Navigation
$(document).ready(function() {
    // Handle account navigation link clicks
    $('.account-nav__link').on('click', function(e) {
        e.preventDefault();

        const targetId = $(this).attr('href').substring(1); // Remove # from href
        console.log('Account tab clicked:', targetId);

        // Remove active class from all nav links
        $('.account-nav__link').removeClass('account-nav__link--active');

        // Add active class to clicked link
        $(this).addClass('account-nav__link--active');

        // Hide all account content sections
        $('.account-content').addClass('account-content--hidden');

        // Show the target content section
        $('#' + targetId).removeClass('account-content--hidden');

        // Scroll to top of content
        $('html, body').animate({
            scrollTop: $('.account-section').offset().top - 100
        }, 300);
    });

    // Handle add address button
    $('#addAddressBtn').on('click', function(e) {
        e.preventDefault();
        $('#addressFormContainer').slideDown(300);
        $('html, body').animate({
            scrollTop: $('#addressFormContainer').offset().top - 100
        }, 300);
    });

    // Handle cancel address form
    $('#cancelAddressBtn').on('click', function(e) {
        e.preventDefault();
        $('#addressFormContainer').slideUp(300);
        $('#addAddressForm')[0].reset();
    });

    // Handle same as billing checkbox
    $('#sameAsBilling').on('change', function() {
        if ($(this).is(':checked')) {
            // Copy billing address fields to shipping fields
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

            // Hide shipping details
            $('#shippingDetails').slideUp(200);
        } else {
            // Enable shipping fields
            $('#shippingDetails input, #shippingDetails select').prop('readonly', false);

            // Show shipping details
            $('#shippingDetails').slideDown(200);
        }
    });

    // Handle "View Details" button clicks in order history
    $('.view-order-details').on('click', function(e) {
        e.preventDefault();

        // Remove active class from all nav links
        $('.account-nav__link').removeClass('account-nav__link--active');

        // Add active class to "My Orders" link (since we're viewing an order from there)
        $('a[href="#my-orders"]').addClass('account-nav__link--active');

        // Hide all account content sections
        $('.account-content').addClass('account-content--hidden');

        // Show order details section
        $('#order-details').removeClass('account-content--hidden');

        // Scroll to top of content
        $('html, body').animate({
            scrollTop: $('.account-section').offset().top - 100
        }, 300);
    });

    // Handle "Back to Orders" link
    $('.back-link').on('click', function(e) {
        e.preventDefault();

        // Remove active class from all nav links
        $('.account-nav__link').removeClass('account-nav__link--active');

        // Add active class to "My Orders" link
        $('a[href="#my-orders"]').addClass('account-nav__link--active');

        // Hide all account content sections
        $('.account-content').addClass('account-content--hidden');

        // Show my orders section
        $('#my-orders').removeClass('account-content--hidden');

        // Scroll to top of content
        $('html, body').animate({
            scrollTop: $('.account-section').offset().top - 100
        }, 300);
    });
});

// Cart Sidebar Functionality
$(document).ready(function() {
    // Open cart sidebar when cart icon is clicked
    $('.cart-trigger').on('click', function(e) {
        e.preventDefault();
        $('.cart-sidebar-overlay').addClass('active');
        $('.cart-sidebar').addClass('active');
        $('body').css('overflow', 'hidden'); // Prevent body scroll
    });

    // Close cart sidebar when close button is clicked
    $('.cart-sidebar__close').on('click', function() {
        $('.cart-sidebar-overlay').removeClass('active');
        $('.cart-sidebar').removeClass('active');
        $('body').css('overflow', ''); // Restore body scroll
    });

    // Close cart sidebar when overlay is clicked
    $('.cart-sidebar-overlay').on('click', function() {
        $('.cart-sidebar-overlay').removeClass('active');
        $('.cart-sidebar').removeClass('active');
        $('body').css('overflow', ''); // Restore body scroll
    });

    // Close cart sidebar on ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('.cart-sidebar').hasClass('active')) {
            $('.cart-sidebar-overlay').removeClass('active');
            $('.cart-sidebar').removeClass('active');
            $('body').css('overflow', ''); // Restore body scroll
        }
    });

    // Remove item from cart sidebar
    $('.cart-sidebar-item__remove').on('click', function() {
        $(this).closest('.cart-sidebar-item').fadeOut(300, function() {
            $(this).remove();
            // Check if cart is empty
            if ($('.cart-sidebar-item').length === 0) {
                $('.cart-sidebar__items').hide();
                $('.cart-sidebar__empty').show();
            }
            // Update cart total (you would calculate this from remaining items)
        });
    });
});

// Wishlist Sidebar Functionality
$(document).ready(function() {
    // Open wishlist sidebar when wishlist icon is clicked
    $(document).on('click', '.wishlist-trigger', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Open sidebar
        $('#wishlist-sidebar-overlay, .wishlist-sidebar-overlay').addClass('active');
        $('#wishlist-sidebar, .wishlist-sidebar').addClass('active');
        $('body').css('overflow', 'hidden'); // Prevent body scroll

        // Load wishlist content if function exists
        if (window.WishlistFunctions && typeof window.WishlistFunctions.loadWishlistSidebar === 'function') {
            window.WishlistFunctions.loadWishlistSidebar();
        }
    });

    // Close wishlist sidebar when close button is clicked
    $('.wishlist-sidebar__close').on('click', function() {
        $('.wishlist-sidebar-overlay').removeClass('active');
        $('.wishlist-sidebar').removeClass('active');
        $('body').css('overflow', ''); // Restore body scroll
    });

    // Close wishlist sidebar when overlay is clicked
    $('.wishlist-sidebar-overlay').on('click', function() {
        $('.wishlist-sidebar-overlay').removeClass('active');
        $('.wishlist-sidebar').removeClass('active');
        $('body').css('overflow', ''); // Restore body scroll
    });

    // Close wishlist sidebar on ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('.wishlist-sidebar').hasClass('active')) {
            $('.wishlist-sidebar-overlay').removeClass('active');
            $('.wishlist-sidebar').removeClass('active');
            $('body').css('overflow', ''); // Restore body scroll
        }
    });

    // Remove item from wishlist sidebar
    $('.wishlist-sidebar-item__remove').on('click', function() {
        $(this).closest('.wishlist-sidebar-item').fadeOut(300, function() {
            $(this).remove();
            // Check if wishlist is empty
            if ($('.wishlist-sidebar-item').length === 0) {
                $('.wishlist-sidebar__items').hide();
                $('.wishlist-sidebar__empty').show();
            }
        });
    });

    // Move selected items to cart
    $('.move-to-cart-btn').on('click', function() {
        const selectedItems = $('.wishlist-item-checkbox:checked').closest('.wishlist-sidebar-item');

        if (selectedItems.length === 0) {
            alert('Please select at least one item to move to cart.');
            return;
        }

        // Move selected items to cart (you would implement actual cart logic here)
        selectedItems.each(function() {
            const itemName = $(this).find('.wishlist-sidebar-item__name').text();
            const itemPrice = $(this).find('.wishlist-sidebar-item__price').text();

            // Here you would add the item to cart
            console.log('Moving to cart:', itemName, itemPrice);
        });

        // Remove selected items from wishlist
        selectedItems.fadeOut(300, function() {
            $(this).remove();

            // Check if wishlist is empty
            if ($('.wishlist-sidebar-item').length === 0) {
                $('.wishlist-sidebar__items').hide();
                $('.wishlist-sidebar__empty').show();
            }
        });

        // Show success message
        alert('Selected items have been moved to cart!');

        // Optionally close the wishlist sidebar
        // $('.wishlist-sidebar-overlay').removeClass('active');
        // $('.wishlist-sidebar').removeClass('active');
        // $('body').css('overflow', '');
    });
});

// ============================================
// Register Page Functionality
// ============================================

$(document).ready(function() {
    // Password Visibility Toggle for Password Field
    $('#togglePassword').on('click', function() {
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

    // Password Visibility Toggle for Confirm Password Field
    $('#toggleConfirmPassword').on('click', function() {
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

    // Password Strength Indicator
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

        // Length check
        if (password.length >= 8) {
            strength += 1;
        } else {
            hint.push('At least 8 characters');
        }

        // Lowercase check
        if (/[a-z]/.test(password)) {
            strength += 1;
        } else {
            hint.push('lowercase letter');
        }

        // Uppercase check
        if (/[A-Z]/.test(password)) {
            strength += 1;
        } else {
            hint.push('uppercase letter');
        }

        // Number check
        if (/[0-9]/.test(password)) {
            strength += 1;
        } else {
            hint.push('number');
        }

        // Special character check
        if (/[^A-Za-z0-9]/.test(password)) {
            strength += 1;
        } else {
            hint.push('special character');
        }

        // Update strength bar
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

    // Password Match Validation
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

    // Register Form Input Focus Effects
    $('.register-form-input').on('focus', function() {
        $(this).css({
            'border-color': 'var(--coral-red)',
            'background': '#ffffff',
            'box-shadow': '0 0 0 4px rgba(233, 92, 103, 0.1)',
            'transform': 'translateY(-2px)'
        });
    });

    $('.register-form-input').on('blur', function() {
        if (!$(this).val()) {
            $(this).css({
                'border-color': '#e9ecef',
                'background': '#f8f9fa',
                'box-shadow': 'none',
                'transform': 'translateY(0)'
            });
        }
    });

    // ============================================
    // Login Page Functionality
    // ============================================

    // Login Form Input Focus Effects
    $('.login-form-input').on('focus', function() {
        $(this).css({
            'border-color': 'var(--coral-red)',
            'background': '#ffffff',
            'box-shadow': '0 0 0 4px rgba(233, 92, 103, 0.1)',
            'transform': 'translateY(-2px)'
        });
    });

    $('.login-form-input').on('blur', function() {
        if (!$(this).val()) {
            $(this).css({
                'border-color': '#e9ecef',
                'background': '#f8f9fa',
                'box-shadow': 'none',
                'transform': 'translateY(0)'
            });
        }
    });

    // ============================================
    // Forgot Password Page Functionality
    // ============================================

    // Forgot Password Form Input Focus Effects
    $('.forgot-password-form-input').on('focus', function() {
        $(this).css({
            'border-color': 'var(--coral-red)',
            'background': '#ffffff',
            'box-shadow': '0 0 0 4px rgba(233, 92, 103, 0.1)',
            'transform': 'translateY(-2px)'
        });
    });

    $('.forgot-password-form-input').on('blur', function() {
        if (!$(this).val()) {
            $(this).css({
                'border-color': '#e9ecef',
                'background': '#f8f9fa',
                'box-shadow': 'none',
                'transform': 'translateY(0)'
            });
        }
    });

    // Forgot Password Form Handler
    $('#forgotPasswordForm').on('submit', function(e) {
        e.preventDefault();
        const email = $('#forgotEmail').val();

        // Simulate form submission
        const successMessage = $('#forgotPasswordSuccess');
        successMessage.css('display', 'block');
        $(this).css('display', 'none');

        // In a real application, you would send an AJAX request here
        // Example: $.ajax({ url: '/api/forgot-password', method: 'POST', data: { email } })
    });

    // ============================================
    // User Dropdown Functionality
    // ============================================

    // Toggle dropdown menu
    $('#userDropdownTrigger').on('click', function(e) {
        e.preventDefault();
        const userDropdown = $('#userDropdown');
        userDropdown.toggleClass('open');
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        const userDropdown = $('#userDropdown');
        if (!userDropdown.is(e.target) && userDropdown.has(e.target).length === 0) {
            userDropdown.removeClass('open');
        }
    });

});

// ============================================
// Product Details Page Functionality
// ============================================

$(document).ready(function() {
    // Product Image Thumbnail Switching
    $('.thumbnail-item').on('click', function() {
        const imageUrl = $(this).data('image');
        const mainImage = $('#mainImage');

        if (imageUrl && mainImage.length) {
            // Update main image source
            mainImage.attr('src', imageUrl);

            // Remove active class from all thumbnails
            $('.thumbnail-item').removeClass('active');

            // Add active class to clicked thumbnail
            $(this).addClass('active');
        }
    });

    // Quantity Increase/Decrease Buttons
    $('#increaseQty').on('click', function(e) {
        e.preventDefault();
        const quantityInput = $('#quantity');
        let currentValue = parseInt(quantityInput.val()) || 1;
        const maxValue = parseInt(quantityInput.attr('max')) || 99;

        if (currentValue < maxValue) {
            currentValue++;
            quantityInput.val(currentValue);
        }
    });

    $('#decreaseQty').on('click', function(e) {
        e.preventDefault();
        const quantityInput = $('#quantity');
        let currentValue = parseInt(quantityInput.val()) || 1;
        const minValue = parseInt(quantityInput.attr('min')) || 1;

        if (currentValue > minValue) {
            currentValue--;
            quantityInput.val(currentValue);
        }
    });

    // Prevent quantity input from going below min or above max
    $('#quantity').on('change', function() {
        let value = parseInt($(this).val()) || 1;
        const minValue = parseInt($(this).attr('min')) || 1;
        const maxValue = parseInt($(this).attr('max')) || 99;

        if (value < minValue) {
            value = minValue;
        } else if (value > maxValue) {
            value = maxValue;
        }

        $(this).val(value);
    });
});
