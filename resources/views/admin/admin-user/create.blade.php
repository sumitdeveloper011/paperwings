@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Admin User
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card modern-card--compact">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Admin User Information</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.admin-users.store') }}" class="modern-form" id="admin-user-form">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="first_name" class="form-label-modern">First Name <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text"
                                               class="form-input-modern @error('first_name') is-invalid @enderror"
                                               id="first_name"
                                               name="first_name"
                                               value="{{ old('first_name') }}"
                                               placeholder="Enter first name"
                                               required>
                                    </div>
                                    @error('first_name')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="last_name" class="form-label-modern">Last Name <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text"
                                               class="form-input-modern @error('last_name') is-invalid @enderror"
                                               id="last_name"
                                               name="last_name"
                                               value="{{ old('last_name') }}"
                                               placeholder="Enter last name"
                                               required>
                                    </div>
                                    @error('last_name')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label for="email" class="form-label-modern">Email <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email"
                                       class="form-input-modern @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="Enter email address"
                                       required>
                            </div>
                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="password" class="form-label-modern">Password <span class="required">*</span></label>
                                    <div class="input-wrapper password-input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password"
                                               class="form-input-modern @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               placeholder="Create a strong password"
                                               required>
                                        <button type="button" id="togglePassword" class="password-toggle-btn">
                                            <i class="fas fa-eye" id="passwordIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                    <div id="passwordStrength" class="password-strength" style="display: none;">
                                        <div id="passwordStrengthBar" class="password-strength-bar"></div>
                                    </div>
                                    <small id="passwordHint" class="password-hint"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="password_confirmation" class="form-label-modern">Confirm Password <span class="required">*</span></label>
                                    <div class="input-wrapper password-input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password"
                                               class="form-input-modern @error('password_confirmation') is-invalid @enderror"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Re-enter your password"
                                               required>
                                        <button type="button" id="toggleConfirmPassword" class="password-toggle-btn">
                                            <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                                        </button>
                                    </div>
                                    <small id="passwordMatch" class="password-match"></small>
                                    @error('password_confirmation')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label for="phone" class="form-label-modern">Phone</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('phone') is-invalid @enderror"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       placeholder="Enter phone number">
                            </div>
                            @error('phone')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label class="form-label-modern">Roles <span class="required">*</span></label>
                            <div class="roles-wrapper" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 1rem;">
                                @foreach($roles as $role)
                                    <div class="form-check-modern mb-2">
                                        <input type="checkbox"
                                               class="form-check-input-modern"
                                               id="role_{{ $role->id }}"
                                               name="roles[]"
                                               value="{{ $role->id }}"
                                               {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label-modern" for="role_{{ $role->id }}">
                                            <strong>{{ $role->name }}</strong>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">Select one or more roles to assign to this admin user</small>
                            @error('roles')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="status" class="form-label-modern">Status <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-toggle-on input-icon"></i>
                                <select class="form-input-modern @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Create Admin User
                            </button>
                            <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.password-input-wrapper {
    position: relative;
}

.password-toggle-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    padding: 5px;
    font-size: 16px;
}

.password-toggle-btn:hover {
    color: #667eea;
}

.password-strength {
    margin-top: 8px;
    height: 4px;
    background-color: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
}

.password-strength-bar {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.password-strength-bar.weak {
    width: 33%;
    background-color: #dc3545;
}

.password-strength-bar.medium {
    width: 66%;
    background-color: #ffc107;
}

.password-strength-bar.strong {
    width: 100%;
    background-color: #28a745;
}

.password-hint {
    display: block;
    margin-top: 5px;
    font-size: 12px;
}

.password-match {
    display: block;
    margin-top: 5px;
    font-size: 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
    const passwordIcon = document.getElementById('passwordIcon');
    const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthContainer = document.getElementById('passwordStrength');
    const passwordHint = document.getElementById('passwordHint');
    const passwordMatch = document.getElementById('passwordMatch');

    // Toggle password visibility
    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            passwordIcon.classList.toggle('fa-eye');
            passwordIcon.classList.toggle('fa-eye-slash');
        });
    }

    // Toggle confirm password visibility
    if (toggleConfirmPasswordBtn) {
        toggleConfirmPasswordBtn.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            confirmPasswordIcon.classList.toggle('fa-eye');
            confirmPasswordIcon.classList.toggle('fa-eye-slash');
        });
    }

    // Password strength checker
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length === 0) {
                strengthContainer.style.display = 'none';
                passwordHint.textContent = '';
                return;
            }

            strengthContainer.style.display = 'block';
            let strength = 0;
            let hint = [];

            if (password.length >= 8) strength += 1; else hint.push('At least 8 characters');
            if (/[a-z]/.test(password)) strength += 1; else hint.push('lowercase letter');
            if (/[A-Z]/.test(password)) strength += 1; else hint.push('uppercase letter');
            if (/[0-9]/.test(password)) strength += 1; else hint.push('number');
            if (/[^A-Za-z0-9]/.test(password)) strength += 1; else hint.push('special character');

            strengthBar.className = 'password-strength-bar';
            if (strength <= 2) {
                strengthBar.classList.add('weak');
                passwordHint.textContent = 'Weak password. Add: ' + hint.slice(0, 2).join(', ');
                passwordHint.style.color = '#dc3545';
            } else if (strength <= 3) {
                strengthBar.classList.add('medium');
                passwordHint.textContent = 'Medium password. Add: ' + hint.slice(0, 1).join(', ');
                passwordHint.style.color = '#ffc107';
            } else {
                strengthBar.classList.add('strong');
                passwordHint.textContent = 'Strong password!';
                passwordHint.style.color = '#28a745';
            }
        });
    }

    // Password match checker
    if (confirmPasswordInput && passwordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;

            if (confirmPassword.length === 0) {
                passwordMatch.textContent = '';
                this.style.borderColor = '';
                return;
            }

            if (password === confirmPassword) {
                passwordMatch.textContent = '✓ Passwords match';
                passwordMatch.style.color = '#28a745';
                this.style.borderColor = '#28a745';
            } else {
                passwordMatch.textContent = '✗ Passwords do not match';
                passwordMatch.style.color = '#dc3545';
                this.style.borderColor = '#dc3545';
            }
        });
    }

    // Form validation
    const form = document.getElementById('admin-user-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const roles = document.querySelectorAll('input[name="roles[]"]:checked');
            if (roles.length === 0) {
                e.preventDefault();
                alert('Please select at least one role.');
                return false;
            }
        });
    }
});
</script>
@endsection
