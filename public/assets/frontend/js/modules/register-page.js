/**
 * Register Page Module (Native JS)
 * Handles password toggles, strength meter, form focus, and validation feedback
 */
(function() {
    'use strict';

    function initRegisterPage() {
        initPasswordToggle();
        initPasswordStrength();
        initFormFocus();
        initInvalidFeedback();
    }

    function initPasswordToggle() {
        const toggles = [
            { btn: 'togglePassword', input: 'registerPassword', icon: 'passwordIcon' },
            { btn: 'toggleConfirmPassword', input: 'registerConfirmPassword', icon: 'confirmPasswordIcon' },
            { btn: 'toggleLoginPassword', input: 'loginPassword', icon: 'loginPasswordIcon' },
            { btn: 'toggleResetPassword', input: 'resetPassword', icon: 'resetPasswordIcon' },
            { btn: 'toggleResetConfirmPassword', input: 'resetConfirmPassword', icon: 'resetConfirmPasswordIcon' }
        ];

        toggles.forEach(({ btn, input, icon }) => {
            const toggleBtn = document.getElementById(btn);
            if (!toggleBtn) return;

            toggleBtn.addEventListener('click', function() {
                const passwordInput = document.getElementById(input);
                const passwordIcon = document.getElementById(icon);
                if (!passwordInput) return;

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    if (passwordIcon) {
                        passwordIcon.classList.remove('fa-eye');
                        passwordIcon.classList.add('fa-eye-slash');
                    }
                } else {
                    passwordInput.type = 'password';
                    if (passwordIcon) {
                        passwordIcon.classList.remove('fa-eye-slash');
                        passwordIcon.classList.add('fa-eye');
                    }
                }
            });
        });
    }

    function initPasswordStrength() {
        const registerPassword = document.getElementById('registerPassword');
        const registerConfirmPassword = document.getElementById('registerConfirmPassword');

        if (registerPassword) {
            registerPassword.addEventListener('input', function() {
                const password = this.value;
                const strengthBar = document.getElementById('passwordStrengthBar');
                const strengthContainer = document.getElementById('passwordStrength');
                const passwordHint = document.getElementById('passwordHint');

                // Clear server-side errors
                clearPasswordErrors(this);

                if (password.length === 0) {
                    if (strengthContainer) strengthContainer.style.display = 'none';
                    if (passwordHint) passwordHint.textContent = '';
                    return;
                }

                if (strengthContainer) strengthContainer.style.display = 'block';
                let strength = 0;
                let hint = [];

                if (password.length >= 8) strength += 1; else hint.push('At least 8 characters');
                if (/[a-z]/.test(password)) strength += 1; else hint.push('lowercase letter');
                if (/[A-Z]/.test(password)) strength += 1; else hint.push('uppercase letter');
                if (/[0-9]/.test(password)) strength += 1; else hint.push('number');
                if (/[^A-Za-z0-9]/.test(password)) strength += 1; else hint.push('special character');

                if (strengthBar) {
                    strengthBar.classList.remove('weak', 'medium', 'strong');
                    if (strength <= 2) {
                        strengthBar.classList.add('weak');
                        if (passwordHint) {
                            passwordHint.textContent = 'Weak password. Add: ' + hint.slice(0, 2).join(', ');
                            passwordHint.style.color = '#dc3545';
                        }
                    } else if (strength <= 3) {
                        strengthBar.classList.add('medium');
                        if (passwordHint) {
                            passwordHint.textContent = 'Medium password. Add: ' + hint.slice(0, 1).join(', ');
                            passwordHint.style.color = '#ffc107';
                        }
                    } else {
                        strengthBar.classList.add('strong');
                        if (passwordHint) {
                            passwordHint.textContent = 'Strong password!';
                            passwordHint.style.color = '#28a745';
                        }
                    }
                }
            });
        }

        if (registerConfirmPassword && registerPassword) {
            registerConfirmPassword.addEventListener('input', function() {
                const password = registerPassword.value;
                const confirmPassword = this.value;
                const matchMessage = document.getElementById('passwordMatch');

                clearPasswordErrors(this);

                if (confirmPassword.length === 0) {
                    if (matchMessage) matchMessage.textContent = '';
                    this.style.borderColor = '';
                    return;
                }

                if (password === confirmPassword) {
                    if (matchMessage) {
                        matchMessage.textContent = '✓ Passwords match';
                        matchMessage.style.color = '#28a745';
                    }
                    this.style.borderColor = '#28a745';
                } else {
                    if (matchMessage) {
                        matchMessage.textContent = '✗ Passwords do not match';
                        matchMessage.style.color = '#dc3545';
                    }
                    this.style.borderColor = '#dc3545';
                }
            });
        }
    }

    function clearPasswordErrors(input) {
        const formGroup = input.closest('.form-group');
        if (!formGroup) return;

        const invalidFeedback = formGroup.querySelector('.invalid-feedback');
        input.classList.remove('is-invalid');
        if (invalidFeedback) invalidFeedback.style.display = 'none';
        input.style.borderColor = '';
        input.style.backgroundColor = '';
    }

    function initFormFocus() {
        const focusStyles = {
            borderColor: 'var(--coral-red)',
            background: '#ffffff',
            boxShadow: '0 0 0 4px rgba(233, 92, 103, 0.1)',
            transform: 'translateY(-2px)'
        };

        const blurStyles = {
            borderColor: '#e9ecef',
            background: '#f8f9fa',
            boxShadow: 'none',
            transform: 'translateY(0)'
        };

        function clearFieldErrors(input) {
            if (input.classList.contains('is-invalid')) {
                const formGroup = input.closest('.form-group');
                if (formGroup) {
                    const invalidFeedback = formGroup.querySelector('.invalid-feedback');
                    input.classList.remove('is-invalid');
                    if (invalidFeedback) invalidFeedback.style.display = 'none';
                }
            }
        }

        document.addEventListener('focus', function(e) {
            const input = e.target;
            if (input && input.nodeType === 1 && (input.classList.contains('register-form-input') || input.classList.contains('login-form-input') || input.classList.contains('forgot-password-form-input'))) {
                Object.assign(input.style, focusStyles);
                clearFieldErrors(input);
            }
        }, true);

        document.addEventListener('blur', function(e) {
            const input = e.target;
            if (input && input.nodeType === 1 && (input.classList.contains('register-form-input') || input.classList.contains('login-form-input') || input.classList.contains('forgot-password-form-input'))) {
                if (!input.value) {
                    Object.assign(input.style, blurStyles);
                }
            }
        }, true);

        // User dropdown toggle
        const userDropdownTrigger = document.getElementById('userDropdownTrigger');
        const userDropdown = document.getElementById('userDropdown');

        if (userDropdownTrigger && userDropdown) {
            userDropdownTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                userDropdown.classList.toggle('open');
            });

            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target) && !userDropdownTrigger.contains(e.target)) {
                    userDropdown.classList.remove('open');
                }
            });
        }
    }

    function initInvalidFeedback() {
        function showInvalidFeedback() {
            document.querySelectorAll('.password-input-wrapper').forEach(wrapper => {
                const input = wrapper.querySelector('input.is-invalid');
                if (input) {
                    const formGroup = wrapper.closest('.form-group');
                    if (formGroup) {
                        const invalidFeedback = formGroup.querySelector('.invalid-feedback');
                        if (invalidFeedback) invalidFeedback.style.display = 'block';
                    }
                }
            });
        }

        showInvalidFeedback();

        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function() {
                setTimeout(showInvalidFeedback, 100);
            });

            if (window.FormSubmissionHandler) {
                window.FormSubmissionHandler.init('registerForm', {
                    loadingText: 'Creating Account...',
                    timeout: 10000
                });
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRegisterPage);
    } else {
        initRegisterPage();
    }
})();
