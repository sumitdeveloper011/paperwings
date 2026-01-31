/**
 * Account Menu Drawer Module
 * Handles mobile account menu drawer toggle functionality
 */
(function() {
    'use strict';

    const AccountDrawer = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            const accountMenuBtn = document.getElementById('accountMenuBtn');
            const accountDrawer = document.getElementById('accountMenuDrawer');
            const accountCloseBtn = document.getElementById('accountMenuDrawerClose');
            const accountOverlay = document.getElementById('accountMenuDrawerOverlay');

            if (accountMenuBtn && accountDrawer) {
                accountMenuBtn.addEventListener('click', () => {
                    this.openDrawer(accountDrawer);
                });
            }

            if (accountCloseBtn) {
                accountCloseBtn.addEventListener('click', () => {
                    this.closeDrawer(accountDrawer);
                });
            }

            if (accountOverlay) {
                accountOverlay.addEventListener('click', () => {
                    this.closeDrawer(accountDrawer);
                });
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && accountDrawer && accountDrawer.classList.contains('active')) {
                    this.closeDrawer(accountDrawer);
                }
            });

            const accountNavLinks = document.querySelectorAll('.account-menu-drawer .account-nav__link');
            accountNavLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (accountDrawer) {
                        this.closeDrawer(accountDrawer);
                    }
                });
            });
        },

        openDrawer: function(drawer) {
            if (!drawer) return;
            
            drawer.classList.add('active');
            document.body.style.overflow = 'hidden';
        },

        closeDrawer: function(drawer) {
            if (!drawer) return;
            
            drawer.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => AccountDrawer.init());
    } else {
        AccountDrawer.init();
    }

    window.AccountDrawer = AccountDrawer;
})();
