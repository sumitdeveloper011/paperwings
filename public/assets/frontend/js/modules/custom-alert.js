/**
 * Custom Alert & Confirmation Dialog Module
 * Reusable custom dialogs to replace browser alert() and confirm()
 */

(function() {
    'use strict';

    class CustomAlert {
        constructor() {
            this.init();
        }

        init() {
            this.createDialogHTML();
            this.bindEvents();
        }

        createDialogHTML() {
            if (document.getElementById('custom-alert-dialog')) {
                return;
            }

            const dialogHTML = `
                <div id="custom-alert-dialog" class="custom-alert-dialog">
                    <div class="custom-alert-dialog__overlay"></div>
                    <div class="custom-alert-dialog__content">
                        <div class="custom-alert-dialog__icon">
                            <i class="fas" id="custom-alert-icon"></i>
                        </div>
                        <h3 class="custom-alert-dialog__title" id="custom-alert-title"></h3>
                        <p class="custom-alert-dialog__message" id="custom-alert-message"></p>
                        <div class="custom-alert-dialog__actions" id="custom-alert-actions">
                            <button class="custom-alert-dialog__btn custom-alert-dialog__btn--primary" id="custom-alert-confirm">
                                OK
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', dialogHTML);
        }

        bindEvents() {
            const dialog = document.getElementById('custom-alert-dialog');
            const overlay = dialog?.querySelector('.custom-alert-dialog__overlay');
            const closeBtn = dialog?.querySelector('.custom-alert-dialog__btn--close');

            if (overlay) {
                overlay.addEventListener('click', () => {
                    this.close();
                });
            }

            document.addEventListener('keydown', (e) => {
                if (dialog?.classList.contains('active')) {
                    if (e.key === 'Escape') {
                        this.close();
                    }
                }
            });
        }

        show(options = {}) {
            const dialog = document.getElementById('custom-alert-dialog');
            if (!dialog) {
                this.createDialogHTML();
                this.bindEvents();
                return this.show(options);
            }

            const {
                title = 'Alert',
                message = '',
                type = 'info',
                confirmText = 'OK',
                cancelText = 'Cancel',
                showCancel = false,
                onConfirm = null,
                onCancel = null
            } = options;

            const iconElement = document.getElementById('custom-alert-icon');
            const titleElement = document.getElementById('custom-alert-title');
            const messageElement = document.getElementById('custom-alert-message');
            const actionsElement = document.getElementById('custom-alert-actions');
            const confirmBtn = document.getElementById('custom-alert-confirm');

            if (!iconElement || !titleElement || !messageElement || !actionsElement || !confirmBtn) {
                return;
            }

            const iconClasses = {
                'info': 'fa-info-circle',
                'success': 'fa-check-circle',
                'warning': 'fa-exclamation-triangle',
                'error': 'fa-times-circle',
                'question': 'fa-question-circle'
            };

            const iconClass = iconClasses[type] || iconClasses.info;
            iconElement.className = `fas ${iconClass}`;
            iconElement.parentElement.className = `custom-alert-dialog__icon custom-alert-dialog__icon--${type}`;

            titleElement.textContent = title;
            messageElement.textContent = message;

            actionsElement.innerHTML = '';

            if (showCancel) {
                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'custom-alert-dialog__btn custom-alert-dialog__btn--secondary';
                cancelBtn.textContent = cancelText;
                cancelBtn.addEventListener('click', () => {
                    this.close();
                    if (onCancel) {
                        onCancel();
                    }
                });
                actionsElement.appendChild(cancelBtn);
            }

            confirmBtn.textContent = confirmText;
            confirmBtn.className = `custom-alert-dialog__btn custom-alert-dialog__btn--primary`;
            confirmBtn.onclick = () => {
                this.close();
                if (onConfirm) {
                    onConfirm();
                }
            };

            actionsElement.appendChild(confirmBtn);

            document.body.style.overflow = 'hidden';
            dialog.classList.add('active');

            setTimeout(() => {
                confirmBtn.focus();
            }, 100);
        }

        close() {
            const dialog = document.getElementById('custom-alert-dialog');
            if (dialog) {
                dialog.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        alert(message, title = 'Alert', type = 'info') {
            return new Promise((resolve) => {
                this.show({
                    title,
                    message,
                    type,
                    onConfirm: () => resolve(true)
                });
            });
        }

        confirm(message, title = 'Confirm', type = 'question') {
            return new Promise((resolve) => {
                this.show({
                    title,
                    message,
                    type,
                    showCancel: true,
                    confirmText: 'Yes',
                    cancelText: 'No',
                    onConfirm: () => resolve(true),
                    onCancel: () => resolve(false)
                });
            });
        }
    }

    if (typeof window !== 'undefined') {
        if (!window.CustomAlertInstance) {
            window.CustomAlertInstance = new CustomAlert();
        }

        window.customAlert = function(message, title, type) {
            return window.CustomAlertInstance.alert(message, title, type);
        };

        window.customConfirm = function(message, title, type) {
            return window.CustomAlertInstance.confirm(message, title, type);
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                if (!window.CustomAlertInstance) {
                    window.CustomAlertInstance = new CustomAlert();
                }
            });
        }
    }
})();
