@extends('layouts.frontend.main')
@section('content')
@include('frontend.partials.page-header', [
    'title' => 'Edit Profile',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'My Account', 'url' => route('account.view-profile')],
        ['label' => 'Edit Profile', 'url' => null]
    ]
])
<section class="account-section">
    <div class="container">
        <div class="row">
            @include('frontend.account.partials.sidebar')

            <!-- Account Content -->
            <div class="col-lg-9 col-12">
                <!-- Mobile Menu Button -->
                <div class="account-content__mobile-menu">
                    <button class="account-menu-btn" id="accountMenuBtn" aria-label="Open Account Menu">
                        <i class="fas fa-bars"></i>
                        <span>Account Menu</span>
                    </button>
                </div>
                
                <!-- Edit Profile -->
                <div class="account-content">
                    <div class="account-block">
                        <h2 class="account-block__title">Edit Profile / Personal Info</h2>

                        <form class="account-form" id="editProfileForm" method="POST" action="{{ route('account.update-profile') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Profile Image Upload -->
                            <div class="form-group form-group--full">
                                <label class="form-label">Profile Picture</label>
                                <div class="profile-image-upload">
                                    <div class="profile-image-upload__preview">
                                        <img src="{{ $user->avatar && \Storage::disk('public')->exists($user->avatar) ? asset('storage/' . $user->avatar) : asset('assets/images/profile.png') }}" alt="Profile Preview" id="profileImagePreview" class="profile-image-upload__img" onerror="this.src='{{ asset('assets/images/profile.png') }}'">
                                        <div class="profile-image-upload__placeholder" id="profileImagePlaceholder" style="display: none;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="profile-image-upload__actions">
                                        <label for="profileImageInput" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-upload"></i>
                                            Upload Image
                                        </label>
                                        <input type="file" id="profileImageInput" name="avatar" class="profile-image-upload__input" accept="image/*" style="display: none;">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="removeProfileImage" style="{{ $user->avatar && \Storage::disk('public')->exists($user->avatar) ? 'display: inline-block;' : 'display: none;' }}">
                                            <i class="fas fa-times"></i>
                                            Remove
                                        </button>
                                    </div>
                                    <div class="form-help">
                                        <i class="fas fa-info-circle"></i>
                                        Recommended size: 200x200px. Max file size: 2MB
                                    </div>
                                </div>
                            </div>

                            <div class="account-form__grid">
                                <div class="form-group">
                                    <label for="editFirstName" class="form-label">First Name <span class="required">*</span></label>
                                    <input type="text" id="editFirstName" name="first_name" class="form-input" value="{{ $user->first_name ?? old('first_name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="editLastName" class="form-label">Last Name <span class="required">*</span></label>
                                    <input type="text" id="editLastName" name="last_name" class="form-input" value="{{ $user->last_name ?? old('last_name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="editEmail" class="form-label">Email Address <span class="required">*</span></label>
                                    <input type="email" id="editEmail" name="email" class="form-input" value="{{ $user->email ?? old('email') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="editPhone" class="form-label">Phone Number <span class="required">*</span></label>
                                    <input type="tel" id="editPhone" name="phone" class="form-input" value="{{ $user->userDetail->phone ?? old('phone') }}" pattern="[\d\+\s\-]+" inputmode="numeric" maxlength="20" required>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                                <div class="form-group">
                                    <label for="editDateOfBirth" class="form-label">Date of Birth</label>
                                    <input type="date" id="editDateOfBirth" name="date_of_birth" class="form-input" value="{{ $user->userDetail && $user->userDetail->date_of_birth ? \Carbon\Carbon::parse($user->userDetail->date_of_birth)->format('Y-m-d') : old('date_of_birth') }}">
                                </div>
                                <div class="form-group">
                                    <label for="editGender" class="form-label">Gender</label>
                                    <select id="editGender" name="gender" class="form-input">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ ($user->userDetail->gender ?? old('gender')) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ ($user->userDetail->gender ?? old('gender')) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ ($user->userDetail->gender ?? old('gender')) == 'other' ? 'selected' : '' }}>Other</option>
                                        <option value="prefer-not-to-say" {{ ($user->userDetail->gender ?? old('gender')) == 'prefer-not-to-say' ? 'selected' : '' }}>Prefer not to say</option>
                                    </select>
                                </div>
                            </div>

                            <div class="account-form__actions">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="{{ route('account.view-profile') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.FormSubmissionHandler) {
        window.FormSubmissionHandler.init('editProfileForm', {
            loadingText: 'Saving Changes...',
            timeout: 10000
        });
    }

    const profileImageInput = document.getElementById('profileImageInput');
    const profileImagePreview = document.getElementById('profileImagePreview');
    const profileImagePlaceholder = document.getElementById('profileImagePlaceholder');
    const removeProfileImageBtn = document.getElementById('removeProfileImage');
    const defaultImageSrc = '{{ asset("assets/images/profile.png") }}';

    if (profileImageInput) {
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                // Validate file type
                if (!file.type.match('image.*')) {
                    if (window.customAlert) {
                        window.customAlert('Please select an image file.', 'Invalid File', 'warning');
                    } else {
                        alert('Please select an image file.');
                    }
                    this.value = '';
                    return;
                }

                // Validate file size (2MB = 2 * 1024 * 1024 bytes)
                if (file.size > 2 * 1024 * 1024) {
                    if (window.customAlert) {
                        window.customAlert('Image size should not exceed 2MB.', 'File Too Large', 'warning');
                    } else {
                        alert('Image size should not exceed 2MB.');
                    }
                    this.value = '';
                    return;
                }

                // Create FileReader to preview image
                const reader = new FileReader();

                reader.onload = function(e) {
                    profileImagePreview.src = e.target.result;
                    profileImagePreview.style.display = 'block';
                    if (profileImagePlaceholder) {
                        profileImagePlaceholder.style.display = 'none';
                    }
                    if (removeProfileImageBtn) {
                        removeProfileImageBtn.style.display = 'inline-block';
                    }
                };

                reader.readAsDataURL(file);
            }
        });
    }

    if (removeProfileImageBtn) {
        removeProfileImageBtn.addEventListener('click', function() {
            if (profileImageInput) {
                profileImageInput.value = '';
            }
            profileImagePreview.src = defaultImageSrc;
            profileImagePreview.style.display = 'block';
            if (profileImagePlaceholder) {
                profileImagePlaceholder.style.display = 'none';
            }
            this.style.display = 'none';
        });
    }

    // Initialize native form validation for edit profile form
    function initEditProfileValidation() {
        if (typeof window.initFormValidationNative === 'undefined') {
            setTimeout(initEditProfileValidation, 100);
            return;
        }

        const form = document.getElementById('editProfileForm');
        if (!form) {
            setTimeout(initEditProfileValidation, 100);
            return;
        }

        const validationRules = {
            'first_name': {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            'last_name': {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            'email': {
                required: true,
                email: true,
                maxlength: 255
            },
            'phone': {
                required: true,
                nzPhone: true
            }
        };

        const validationMessages = {
            'first_name': {
                required: 'Please enter your first name.',
                minlength: 'First name must be at least 2 characters.',
                maxlength: 'First name cannot exceed 255 characters.'
            },
            'last_name': {
                required: 'Please enter your last name.',
                minlength: 'Last name must be at least 2 characters.',
                maxlength: 'Last name cannot exceed 255 characters.'
            },
            'email': {
                required: 'Please enter your email address.',
                email: 'Please enter a valid email address.',
                maxlength: 'Email cannot exceed 255 characters.'
            },
            'phone': {
                required: 'Please enter your phone number.',
                nzPhone: 'Please enter a valid New Zealand phone number (numbers only, e.g., 0211234567 or 091234567).'
            }
        };

        window.initFormValidationNative('#editProfileForm', {
            rules: validationRules,
            messages: validationMessages,
            onInvalid: function(errors, validator) {
                validator.scrollToFirstError();
            }
        });
    }

    initEditProfileValidation();
});
</script>
@endpush
@endsection
