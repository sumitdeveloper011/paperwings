@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-user-circle"></i>
                    My Profile
                </h1>
                <p class="page-header__subtitle">Manage your profile information and account settings</p>
            </div>
        </div>
    </div>

    <!-- Profile Tabs -->
    <div class="profile-container">
        <!-- Tab Navigation -->
        <div class="profile-tabs">
            <button class="profile-tab active" data-tab="profile">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </button>
            <button class="profile-tab" data-tab="password">
                <i class="fas fa-lock"></i>
                <span>Change Password</span>
            </button>
            <button class="profile-tab" data-tab="security">
                <i class="fas fa-shield-alt"></i>
                <span>Security</span>
            </button>
        </div>

        <!-- Tab Content -->
        <div class="profile-content">
            <!-- Profile Tab -->
            <div class="tab-pane active" id="profile-tab">
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-user"></i>
                            Personal Information
                        </h3>
                        <p class="modern-card__subtitle">Update your personal details and profile information</p>
                    </div>
                    <div class="modern-card__body">
                        <form method="POST" action="{{ route('admin.profile.update') }}" class="modern-form" id="profileForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label for="name" class="form-label-modern">
                                            Full Name <span class="required">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <i class="fas fa-user input-icon"></i>
                                            <input type="text"
                                                   class="form-input-modern @error('name') is-invalid @enderror"
                                                   id="name"
                                                   name="name"
                                                   value="{{ old('name', Auth::user()->name ?? '') }}"
                                                   placeholder="Enter your full name"
                                                   required>
                                        </div>
                                        @error('name')
                                            <div class="form-error">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label for="email" class="form-label-modern">
                                            Email Address <span class="required">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <i class="fas fa-envelope input-icon"></i>
                                            <input type="email"
                                                   class="form-input-modern @error('email') is-invalid @enderror"
                                                   id="email"
                                                   name="email"
                                                   value="{{ old('email', Auth::user()->email ?? '') }}"
                                                   placeholder="Enter your email address"
                                                   required>
                                        </div>
                                        @error('email')
                                            <div class="form-error">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label for="phone" class="form-label-modern">
                                            Phone Number
                                        </label>
                                        <div class="input-wrapper">
                                            <i class="fas fa-phone input-icon"></i>
                                            <input type="tel"
                                                   class="form-input-modern @error('phone') is-invalid @enderror"
                                                   id="phone"
                                                   name="phone"
                                                   value="{{ old('phone', Auth::user()->phone ?? '') }}"
                                                   placeholder="Enter your phone number">
                                        </div>
                                        @error('phone')
                                            <div class="form-error">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label for="role" class="form-label-modern">
                                            Role
                                        </label>
                                        <div class="input-wrapper">
                                            <i class="fas fa-user-tag input-icon"></i>
                                            <input type="text"
                                                   class="form-input-modern"
                                                   id="role"
                                                   value="{{ Auth::user()->roles->first()->name ?? 'Admin' }}"
                                                   disabled>
                                        </div>
                                        <div class="form-hint">
                                            <i class="fas fa-info-circle"></i>
                                            Role cannot be changed from profile settings
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group-modern">
                                <label for="bio" class="form-label-modern">
                                    Bio / About Me
                                </label>
                                <div class="input-wrapper">
                                    <textarea class="form-input-modern @error('bio') is-invalid @enderror"
                                              id="bio"
                                              name="bio"
                                              rows="4"
                                              placeholder="Tell us about yourself">{{ old('bio', Auth::user()->bio ?? '') }}</textarea>
                                </div>
                                @error('bio')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Update Profile
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i>
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Profile Picture Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-image"></i>
                            Profile Picture
                        </h3>
                        <p class="modern-card__subtitle">Upload or change your profile picture</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="profile-picture-section">
                            <div class="profile-picture-preview">
                                <div class="profile-avatar-large">
                                    <img src="{{ Auth::user()->avatar_url ?? asset('assets/images/placeholder.jpg') }}"
                                         alt="Profile Picture"
                                         id="profileAvatarPreview"
                                         onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                    <div class="profile-avatar-overlay">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="profile-picture-actions">
                                <form method="POST" action="{{ route('admin.profile.updateAvatar') }}" enctype="multipart/form-data" id="avatarForm">
                                    @csrf
                                    @method('PUT')
                                    <div class="file-upload-wrapper">
                                        <input type="file"
                                               class="file-upload-input"
                                               id="avatar"
                                               name="avatar"
                                               accept="image/*">
                                        <label for="avatar" class="file-upload-label">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <span>Choose New Photo</span>
                                        </label>
                                    </div>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        Recommended: Square image, at least 200x200 pixels. Max size: 2MB.
                                    </div>
                                    <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">
                                        <i class="fas fa-upload"></i>
                                        Upload Photo
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password Tab -->
            <div class="tab-pane" id="password-tab">
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-lock"></i>
                            Change Password
                        </h3>
                        <p class="modern-card__subtitle">Update your password to keep your account secure</p>
                    </div>
                    <div class="modern-card__body">
                        <form method="POST" action="{{ route('admin.profile.updatePassword') }}" class="modern-form" id="passwordForm">
                            @csrf
                            @method('PUT')

                            <div class="form-group-modern">
                                <label for="current_password" class="form-label-modern">
                                    Current Password <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-key input-icon"></i>
                                    <input type="password"
                                           class="form-input-modern @error('current_password') is-invalid @enderror"
                                           id="current_password"
                                           name="current_password"
                                           placeholder="Enter your current password"
                                           required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group-modern">
                                <label for="new_password" class="form-label-modern">
                                    New Password <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password"
                                           class="form-input-modern @error('new_password') is-invalid @enderror"
                                           id="new_password"
                                           name="new_password"
                                           placeholder="Enter your new password"
                                           required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i>
                                    Password must be at least 8 characters long
                                </div>
                                @error('new_password')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group-modern">
                                <label for="new_password_confirmation" class="form-label-modern">
                                    Confirm New Password <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password"
                                           class="form-input-modern @error('new_password_confirmation') is-invalid @enderror"
                                           id="new_password_confirmation"
                                           name="new_password_confirmation"
                                           placeholder="Confirm your new password"
                                           required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('new_password_confirmation')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('new_password_confirmation')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Update Password
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i>
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Tab -->
            <div class="tab-pane" id="security-tab">
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-shield-alt"></i>
                            Security Settings
                        </h3>
                        <p class="modern-card__subtitle">Manage your account security and privacy settings</p>
                    </div>
                    <div class="modern-card__body">
                        <!-- Two-Factor Authentication -->
                        <div class="security-item">
                            <div class="security-item__content">
                                <div class="security-item__info">
                                    <h4 class="security-item__title">
                                        <i class="fas fa-mobile-alt"></i>
                                        Two-Factor Authentication
                                    </h4>
                                    <p class="security-item__description">
                                        Add an extra layer of security to your account by enabling two-factor authentication
                                    </p>
                                </div>
                                <div class="security-item__action">
                                    <form method="POST" action="{{ route('admin.profile.updateTwoFactor') }}" id="twoFactorForm" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <label class="toggle-switch">
                                            <input type="checkbox"
                                                   id="twoFactorEnabled"
                                                   name="two_factor_enabled"
                                                   value="1"
                                                   {{ Auth::user()->two_factor_enabled ? 'checked' : '' }}
                                                   onchange="document.getElementById('twoFactorForm').submit();">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tab Functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.profile-tab');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // Remove active class from all tabs and panes
            tabs.forEach(t => t.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            // Add active class to clicked tab and corresponding pane
            this.classList.add('active');
            document.getElementById(targetTab + '-tab').classList.add('active');
        });
    });

    // Avatar Preview
    const avatarInput = document.getElementById('avatar');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileAvatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// Password Toggle Function
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const toggle = input.nextElementSibling;
    const icon = toggle.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

</script>
@endsection

