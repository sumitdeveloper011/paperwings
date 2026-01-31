/**
 * Main Application JavaScript
 * Loads and initializes all modules
 */
(function() {
    'use strict';

    // Initialize modules when DOM is ready
    function initScripts() {
        const Utils = window.ScriptUtils || { 
            log: () => {}, 
            throttle: (fn, delay) => {
                let lastCall = 0;
                return function(...args) {
                    const now = Date.now();
                    if (now - lastCall >= delay) {
                        lastCall = now;
                        fn.apply(this, args);
                    }
                };
            }
        };
        const CONFIG = Utils.getConfig ? Utils.getConfig() : { throttleDelay: 100 };

        // Initialize remaining modules (smaller ones kept here)
        Animations.init();
        Subscription.init();
        ScrollToTop.init(Utils, CONFIG);
        CategoryPage.init(Utils, CONFIG);
        
        // Initialize native JS modules
        ShopPage.init();
        AddressForm.init();
        ProductDetails.init();
        MegaMenu.init();
        MobileHeader.init();
        
        // Select2 removed - using native HTML selects (no jQuery dependency)
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

    // Subscription Module (Native JS)
    const Subscription = {
        init: function() {
            const form = document.getElementById('subscriptionForm');
            if (!form) return;

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSubmit(form);
            });
        },

        handleSubmit: function(form) {
            const emailInput = form.querySelector('#subscriptionEmail');
            const submitBtn = form.querySelector('#subscriptionBtn');
            const btnText = submitBtn?.querySelector('.subscription-btn-text');
            const btnLoader = submitBtn?.querySelector('.subscription-btn-loader');
            const email = emailInput?.value.trim() || '';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const messageDiv = document.getElementById('subscriptionMessage');
            if (messageDiv) {
                messageDiv.style.display = 'none';
                messageDiv.className = messageDiv.className.replace(/subscription-message--(success|error)/g, '').trim();
            }

            if (!email || !this.isValidEmail(email)) {
                this.showMessage('Please enter a valid email address.', 'error');
                return;
            }

            if (submitBtn) submitBtn.disabled = true;
            if (btnText) btnText.style.display = 'none';
            if (btnLoader) btnLoader.style.display = 'block';

            fetch(form.action || form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email, _token: csrfToken })
            })
            .then(response => response.json().then(data => ({ status: response.status, data })))
            .then(({ status, data }) => {
                if (status === 200 && data.success) {
                    if (window.Analytics) {
                        window.Analytics.trackNewsletterSubscribe('homepage');
                    }
                    this.showMessage(data.message || 'Thank you for subscribing! You will receive our latest offers and updates.', 'success');
                    if (emailInput) emailInput.value = '';
                } else {
                    let errorMessage = data.message || 'An error occurred. Please try again.';
                    if (status === 422 && data.errors) {
                        errorMessage = Object.values(data.errors)[0][0];
                    } else if (status === 409) {
                        errorMessage = 'This email is already subscribed to our newsletter.';
                    }
                    this.showMessage(errorMessage, 'error');
                }
            })
            .catch(error => {
                this.showMessage('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
                if (btnText) btnText.style.display = '';
                if (btnLoader) btnLoader.style.display = 'none';
            });
        },

        isValidEmail: function(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        showMessage: function(message, type) {
            // Use toast notification if available, otherwise fallback to inline message
            if (typeof showToast !== 'undefined') {
                showToast(message, type, 5000);
            } else {
                // Fallback to inline message
                const messageDiv = document.getElementById('subscriptionMessage');
                if (!messageDiv) return;

                messageDiv.className = messageDiv.className.replace(/subscription-message--(success|error)/g, '').trim();
                messageDiv.classList.add('subscription-message--' + type);
                messageDiv.innerHTML = '<span>' + message + '</span>';
                messageDiv.style.display = 'block';
                messageDiv.style.opacity = '0';
                setTimeout(() => {
                    messageDiv.style.transition = 'opacity 0.3s';
                    messageDiv.style.opacity = '1';
                }, 10);

                if (type === 'success') {
                    setTimeout(() => {
                        messageDiv.style.transition = 'opacity 0.3s';
                        messageDiv.style.opacity = '0';
                        setTimeout(() => {
                            messageDiv.style.display = 'none';
                        }, 300);
                    }, 5000);
                }
            }
        }
    };

    // Scroll to Top Module (Native JS)
    const ScrollToTop = {
        init: function(Utils, CONFIG) {
            const scrollToTopBtn = document.getElementById('scrollToTop');
            if (!scrollToTopBtn) return;

            const handleScroll = Utils.throttle(() => {
                if (window.scrollY > 300) {
                    scrollToTopBtn.classList.add('show');
                } else {
                    scrollToTopBtn.classList.remove('show');
                }
            }, CONFIG.throttleDelay);

            window.addEventListener('scroll', handleScroll);

            scrollToTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
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
            document.addEventListener('click', (e) => {
                const viewBtn = e.target.closest('.view-btn');
                if (!viewBtn) return;

                const view = viewBtn.getAttribute('data-view');
                if (!view) return;

                document.querySelectorAll('.view-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                viewBtn.classList.add('active');

                const productsGrid = document.getElementById('productsGrid');
                if (productsGrid) {
                    productsGrid.classList.remove('products-grid--list');
                    productsGrid.classList.add('products-grid--' + view);
                    if (view === 'list') {
                        productsGrid.classList.add('products-grid--list');
                    }
                }
            });
        },

        initFilters: function(Utils) {
            document.addEventListener('click', (e) => {
                const subcategoryLink = e.target.closest('.sidebar-subcategory__link');
                if (subcategoryLink) {
                    e.preventDefault();
                    document.querySelectorAll('.sidebar-subcategory__link').forEach(link => {
                        link.classList.remove('active');
                    });
                    subcategoryLink.classList.add('active');
                }

                const clearBtn = e.target.closest('.clear-filters-btn');
                if (clearBtn) {
                    document.querySelectorAll('.brand-checkbox input').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    const priceRange = document.getElementById('priceRange');
                    if (priceRange) priceRange.value = 100;
                    const priceMin = document.querySelector('.price-min');
                    if (priceMin) priceMin.textContent = '$0';
                    const priceMax = document.querySelector('.price-max');
                    if (priceMax) priceMax.textContent = '$100';
                    document.querySelectorAll('.sidebar-category__link').forEach(link => {
                        link.classList.remove('active');
                    });
                    const firstCategoryLink = document.querySelector('.sidebar-category__link');
                    if (firstCategoryLink) firstCategoryLink.classList.add('active');
                }
            });

            document.addEventListener('change', (e) => {
                const brandCheckbox = e.target.closest('.brand-checkbox input');
                if (brandCheckbox) {
                    const brand = brandCheckbox.closest('.brand-checkbox')?.textContent.trim() || '';
                    const isChecked = brandCheckbox.checked;
                    Utils.log('Brand filter:', { brand, isChecked });
                }

                const sortSelect = e.target.closest('.sort-select');
                if (sortSelect) {
                    const sortBy = sortSelect.value;
                    Utils.log('Sort by:', sortBy);
                }
            });
        },

        applyPriceFilter: function(Utils) {
            const priceRange = document.getElementById('priceRange');
            const maxPrice = priceRange?.value;
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
        }
    };

    // Address Form Module (Native JS)
    const AddressForm = {
        init: function() {
            this.initAddAddress();
            this.initSameAsBilling();
            this.initOrderDetails();
        },

        initAddAddress: function() {
            document.addEventListener('click', (e) => {
                if (e.target.closest('#addAddressBtn')) {
                    e.preventDefault();
                    const container = document.getElementById('addressFormContainer');
                    if (container) {
                        container.style.display = 'block';
                        container.style.maxHeight = container.scrollHeight + 'px';
                        container.style.opacity = '1';
                        window.scrollTo({
                            top: container.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                }

                if (e.target.closest('#cancelAddressBtn')) {
                    e.preventDefault();
                    const container = document.getElementById('addressFormContainer');
                    const form = document.getElementById('addAddressForm');
                    if (container) {
                        container.style.maxHeight = '0';
                        container.style.opacity = '0';
                        setTimeout(() => {
                            container.style.display = 'none';
                        }, 300);
                    }
                    if (form) form.reset();
                }
            });
        },

        initSameAsBilling: function() {
            document.addEventListener('change', (e) => {
                const checkbox = e.target.closest('#sameAsBilling');
                if (!checkbox) return;

                const shippingDetails = document.getElementById('shippingDetails');
                if (!shippingDetails) return;

                if (checkbox.checked) {
                    const fields = ['FirstName', 'LastName', 'Email', 'Phone', 'Address', 'Address2', 'City', 'State', 'Zip', 'Country'];
                    fields.forEach(field => {
                        const billingField = document.getElementById('billing' + field);
                        const shippingField = document.getElementById('shipping' + field);
                        if (billingField && shippingField) {
                            shippingField.value = billingField.value;
                            shippingField.readOnly = true;
                        }
                    });
                    shippingDetails.style.display = 'none';
                } else {
                    const inputs = shippingDetails.querySelectorAll('input, select');
                    inputs.forEach(input => input.readOnly = false);
                    shippingDetails.style.display = 'block';
                }
            });
        },

        initOrderDetails: function() {
            document.addEventListener('click', (e) => {
                if (e.target.closest('.view-order-details')) {
                    e.preventDefault();
                    document.querySelectorAll('.account-nav__link').forEach(link => {
                        link.classList.remove('account-nav__link--active');
                    });
                    const ordersLink = document.querySelector('a[href="#my-orders"]');
                    if (ordersLink) ordersLink.classList.add('account-nav__link--active');
                    document.querySelectorAll('.account-content').forEach(content => {
                        content.classList.add('account-content--hidden');
                    });
                    const orderDetails = document.getElementById('order-details');
                    if (orderDetails) {
                        orderDetails.classList.remove('account-content--hidden');
                        window.scrollTo({
                            top: document.querySelector('.account-section')?.offsetTop - 100 || 0,
                            behavior: 'smooth'
                        });
                    }
                }

                if (e.target.closest('.back-link')) {
                    e.preventDefault();
                    document.querySelectorAll('.account-nav__link').forEach(link => {
                        link.classList.remove('account-nav__link--active');
                    });
                    const ordersLink = document.querySelector('a[href="#my-orders"]');
                    if (ordersLink) ordersLink.classList.add('account-nav__link--active');
                    document.querySelectorAll('.account-content').forEach(content => {
                        content.classList.add('account-content--hidden');
                    });
                    const myOrders = document.getElementById('my-orders');
                    if (myOrders) {
                        myOrders.classList.remove('account-content--hidden');
                        window.scrollTo({
                            top: document.querySelector('.account-section')?.offsetTop - 100 || 0,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        }
    };


    // Product Details Module (Native JS)
    const ProductDetails = {
        init: function() {
            this.initThumbnails();
            // Quantity controls are handled by dedicated quantity.js module
            // Removed duplicate handler to prevent double increment/decrement
        },

        initThumbnails: function() {
            document.addEventListener('click', (e) => {
                const thumbnail = e.target.closest('.thumbnail-item');
                if (!thumbnail) return;

                const imageUrl = thumbnail.getAttribute('data-image');
                const mainImage = document.getElementById('mainImage');
                if (imageUrl && mainImage) {
                    mainImage.src = imageUrl;
                    document.querySelectorAll('.thumbnail-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    thumbnail.classList.add('active');
                }
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

    // Select2 Initialization - REMOVED
    // Native HTML selects are used instead (no jQuery dependency needed)

    // Mega Menu Module
    const MegaMenu = {
        init: function() {
            const megaMenuWrapper = document.querySelector('.mega-menu-wrapper');
            const megaMenuTrigger = document.getElementById('megaMenuTrigger');
            const megaMenu = document.getElementById('megaMenu');

            if (!megaMenuWrapper || !megaMenuTrigger || !megaMenu) {
                return;
            }

            // Toggle menu on click (works for both desktop and mobile)
            megaMenuTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // Mega menu button clicked
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

    // Mobile Header Module (Native JS)
    const MobileHeader = {
        init: function() {

            // Mobile Search Toggle
            const mobileSearchToggle = document.getElementById('mobileSearchToggle');
            const mobileSearchRow = document.getElementById('mobileSearchRow');
            const mobileSearchClose = document.getElementById('mobileSearchClose');
            const mobileSearchInput = document.getElementById('header-search-input-mobile');

            if (mobileSearchToggle && mobileSearchRow) {
                mobileSearchToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    mobileSearchRow.style.display = 'block';
                    mobileSearchRow.style.maxHeight = mobileSearchRow.scrollHeight + 'px';
                    mobileSearchRow.style.opacity = '1';
                    setTimeout(() => {
                        if (mobileSearchInput) mobileSearchInput.focus();
                    }, 350);
                });
            }

            if (mobileSearchClose && mobileSearchRow) {
                mobileSearchClose.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    mobileSearchRow.style.maxHeight = '0';
                    mobileSearchRow.style.opacity = '0';
                    setTimeout(() => {
                        mobileSearchRow.style.display = 'none';
                    }, 300);
                    if (mobileSearchInput) mobileSearchInput.value = '';
                });
            }

            // Close search when clicking outside
            document.addEventListener('click', function(e) {
                if (mobileSearchRow && mobileSearchRow.style.display !== 'none') {
                    const searchContainer = document.getElementById('header-search-mobile');
                    if (!searchContainer?.contains(e.target) && !mobileSearchToggle?.contains(e.target)) {
                        mobileSearchRow.style.maxHeight = '0';
                        mobileSearchRow.style.opacity = '0';
                        setTimeout(() => {
                            mobileSearchRow.style.display = 'none';
                        }, 300);
                        if (mobileSearchInput) mobileSearchInput.value = '';
                    }
                }
            });

            // Mobile User Login Dropdown
            const userLoginToggle = document.getElementById('userLoginToggleMobile');
            const userLoginDropdown = document.getElementById('userLoginDropdownMobile');

            if (userLoginToggle && userLoginDropdown) {
                userLoginToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Close other dropdowns
                    document.querySelectorAll('.header__user-dropdown').forEach(dropdown => {
                        if (dropdown !== userLoginDropdown) {
                            dropdown.classList.remove('open');
                        }
                    });

                    // Toggle this dropdown
                    userLoginDropdown.classList.toggle('open');
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (userLoginDropdown && !userLoginDropdown.contains(e.target)) {
                    userLoginDropdown.classList.remove('open');
                }
            });

            // Mobile User Dropdown (for logged in users)
            const userDropdownMobile = document.getElementById('userDropdownMobile');
            const userDropdownTriggerMobile = document.getElementById('userDropdownTriggerMobile');

            if (userDropdownTriggerMobile && userDropdownMobile) {
                userDropdownTriggerMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Close other dropdowns
                    document.querySelectorAll('.header__user-dropdown').forEach(dropdown => {
                        if (dropdown !== userDropdownMobile) {
                            dropdown.classList.remove('open');
                        }
                    });

                    // Toggle this dropdown
                    userDropdownMobile.classList.toggle('open');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (userDropdownMobile && !userDropdownMobile.contains(e.target) && !userDropdownTriggerMobile.contains(e.target)) {
                        userDropdownMobile.classList.remove('open');
                    }
                });
            }

            // Mobile Navigation Menu Toggle
            const navMobileMenuToggle = document.getElementById('navMobileMenuToggle');
            const navMobileMenu = document.getElementById('navMobileMenu');
            const navMobileMenuOverlay = document.getElementById('navMobileMenuOverlay');
            const navMobileMenuClose = document.getElementById('navMobileMenuClose');
            const body = document.body;

            function openMobileMenu() {
                if (navMobileMenu) navMobileMenu.classList.add('active');
                if (navMobileMenuOverlay) navMobileMenuOverlay.classList.add('active');
                if (body) body.style.overflow = 'hidden';
            }

            function closeMobileMenu() {
                if (navMobileMenu) navMobileMenu.classList.remove('active');
                if (navMobileMenuOverlay) navMobileMenuOverlay.classList.remove('active');
                if (body) body.style.overflow = '';
            }

            if (navMobileMenuToggle) {
                navMobileMenuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openMobileMenu();
                });
            }

            if (navMobileMenuClose) {
                navMobileMenuClose.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeMobileMenu();
                });
            }

            if (navMobileMenuOverlay) {
                navMobileMenuOverlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeMobileMenu();
                });
            }

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && navMobileMenu?.classList.contains('active')) {
                    closeMobileMenu();
                }
            });

            // Close menu when clicking on a link
            document.querySelectorAll('.nav__mobile-menu-link, .nav__mobile-menu-categories-link').forEach(link => {
                link.addEventListener('click', function() {
                    setTimeout(closeMobileMenu, 300);
                });
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
