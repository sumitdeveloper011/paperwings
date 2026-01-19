/**
 * Google Analytics Module
 * Handles all GA4 event tracking
 */
(function() {
    'use strict';

    const Analytics = {
        gaId: null,
        enabled: false,
        dataLayer: null,

        init: function() {
            const metaGaId = document.querySelector('meta[name="ga-id"]');
            const metaGaEnabled = document.querySelector('meta[name="ga-enabled"]');
            
            if (metaGaId) {
                this.gaId = metaGaId.getAttribute('content');
                this.enabled = !!this.gaId && metaGaEnabled?.getAttribute('content') === '1';
            }

            this.dataLayer = window.dataLayer || [];
            window.dataLayer = this.dataLayer;

            if (typeof gtag === 'undefined') {
                window.gtag = function() {
                    this.dataLayer.push(arguments);
                }.bind(this);
            }

            if (window.CookieConsent && window.CookieConsent.hasConsent('analytics')) {
                this.loadGoogleAnalytics();
            }
        },

        loadGoogleAnalytics: function() {
            if (!this.enabled || !this.gaId) {
                return;
            }

            if (typeof gtag === 'undefined') {
                const script = document.createElement('script');
                script.async = true;
                script.src = `https://www.googletagmanager.com/gtag/js?id=${this.gaId}`;
                script.onload = () => {
                    if (typeof gtag !== 'undefined') {
                        gtag('js', new Date());
                        gtag('config', this.gaId, {
                            'send_page_view': true
                        });
                    }
                };
                document.head.appendChild(script);
            } else {
                gtag('js', new Date());
                gtag('config', this.gaId, {
                    'send_page_view': true
                });
            }
        },

        isEnabled: function() {
            return this.enabled && typeof gtag !== 'undefined';
        },

        trackEvent: function(eventName, parameters = {}) {
            if (!this.isEnabled()) {
                return;
            }

            try {
                gtag('event', eventName, parameters);
            } catch (error) {
                // Analytics tracking error (silent fail in production)
            }
        },

        trackViewItem: function(product) {
            this.trackEvent('view_item', {
                currency: 'NZD',
                value: product.price || 0,
                items: [{
                    item_id: product.id,
                    item_name: product.name,
                    item_category: product.category || 'Uncategorized',
                    item_brand: product.brand || '',
                    price: product.price || 0,
                    quantity: 1
                }]
            });
        },

        /**
         * Auto-track product view if ProductAnalyticsData is available
         */
        autoTrackProductView: function() {
            if (window.ProductAnalyticsData && this.isEnabled()) {
                this.trackViewItem(window.ProductAnalyticsData);
            }
        },

        trackAddToCart: function(product, quantity = 1) {
            this.trackEvent('add_to_cart', {
                currency: 'NZD',
                value: (product.price || 0) * quantity,
                items: [{
                    item_id: product.id,
                    item_name: product.name,
                    item_category: product.category || 'Uncategorized',
                    item_brand: product.brand || '',
                    price: product.price || 0,
                    quantity: quantity
                }]
            });
        },

        trackRemoveFromCart: function(product, quantity = 1) {
            this.trackEvent('remove_from_cart', {
                currency: 'NZD',
                value: (product.price || 0) * quantity,
                items: [{
                    item_id: product.id,
                    item_name: product.name,
                    item_category: product.category || 'Uncategorized',
                    item_brand: product.brand || '',
                    price: product.price || 0,
                    quantity: quantity
                }]
            });
        },

        trackBeginCheckout: function(cartData) {
            this.trackEvent('begin_checkout', {
                currency: 'NZD',
                value: cartData.total || 0,
                items: cartData.items || []
            });
        },

        trackViewItemList: function(items, listName = 'Product List') {
            this.trackEvent('view_item_list', {
                item_list_name: listName,
                items: items
            });
        },

        trackSearch: function(searchTerm) {
            this.trackEvent('search', {
                search_term: searchTerm
            });
        },

        trackAddToWishlist: function(product) {
            this.trackEvent('add_to_wishlist', {
                currency: 'NZD',
                value: product.price || 0,
                items: [{
                    item_id: product.id,
                    item_name: product.name,
                    item_category: product.category || 'Uncategorized',
                    item_brand: product.brand || '',
                    price: product.price || 0
                }]
            });
        },

        trackRemoveFromWishlist: function(product) {
            this.trackEvent('remove_from_wishlist', {
                currency: 'NZD',
                value: product.price || 0,
                items: [{
                    item_id: product.id,
                    item_name: product.name,
                    item_category: product.category || 'Uncategorized',
                    item_brand: product.brand || '',
                    price: product.price || 0
                }]
            });
        },

        trackNewsletterSubscribe: function(method = 'homepage') {
            this.trackEvent('newsletter_subscribe', {
                method: method
            });
        },

        trackContactFormSubmit: function() {
            this.trackEvent('contact_form_submit', {
                form_type: 'contact'
            });
        },

        trackCheckoutStep: function(step, stepNumber) {
            this.trackEvent('checkout_progress', {
                checkout_step: stepNumber,
                checkout_step_option: step
            });
        },

        trackCouponApplied: function(couponCode, value) {
            this.trackEvent('apply_promotion', {
                promotion_id: couponCode,
                promotion_name: couponCode,
                value: value,
                currency: 'NZD'
            });
        },

        setUserProperties: function(userId, properties = {}) {
            if (!this.isEnabled() || !userId) {
                return;
            }

            gtag('set', 'user_properties', {
                user_id: userId,
                ...properties
            });
        },

        setUserType: function(userType) {
            if (!this.isEnabled()) {
                return;
            }

            gtag('set', 'user_properties', {
                user_type: userType
            });
        },

        setCustomDimension: function(dimensionName, value) {
            if (!this.isEnabled()) {
                return;
            }

            gtag('set', { [dimensionName]: value });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            Analytics.init();
            Analytics.autoTrackProductView();
        });
    } else {
        Analytics.init();
        Analytics.autoTrackProductView();
    }

    window.Analytics = Analytics;
})();
