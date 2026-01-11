@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Admin User
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
        <!-- Main Form -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.admin-users.update', $user->uuid) }}" class="modern-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('first_name') is-invalid @enderror"
                                           id="first_name"
                                           name="first_name"
                                           value="{{ old('first_name', $user->first_name) }}"
                                           placeholder="Enter first name"
                                           required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('last_name') is-invalid @enderror"
                                           id="last_name"
                                           name="last_name"
                                           value="{{ old('last_name', $user->last_name) }}"
                                           placeholder="Enter last name"
                                           required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="Enter email address"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="password-input-wrapper" style="position: relative;">
                                        <input type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               placeholder="Leave blank to keep current password">
                                        <button type="button" id="togglePassword" class="password-toggle-btn" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #666; cursor: pointer;">
                                            <i class="fas fa-eye" id="passwordIcon"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Leave blank to keep current password.
                                    </small>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <div class="password-input-wrapper" style="position: relative;">
                                        <input type="password"
                                               class="form-control"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Confirm new password">
                                        <button type="button" id="toggleConfirmPassword" class="password-toggle-btn" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #666; cursor: pointer;">
                                            <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="Enter phone number">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Profile Image -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-image"></i>
                            Profile Image
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div class="image-preview-wrapper">
                                <div class="image-preview" id="currentImagePreview" style="position: relative; display: inline-block; border-radius: 50%; overflow: hidden; border: 3px solid #dee2e6; width: 150px; height: 150px;">
                                    <img src="{{ $user->avatar_url }}"
                                         alt="{{ $user->name }}"
                                         style="width: 100%; height: 100%; object-fit: cover; display: block;"
                                         onerror="this.src='{{ asset('assets/images/profile.png') }}'">
                                    @if($user->avatar)
                                    <button type="button" class="image-preview__remove" onclick="removeCurrentImage()" style="position: absolute; top: 5px; right: 5px; background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                @if($user->avatar)
                                    Current image will be replaced if you upload a new one.
                                @else
                                    No image uploaded. Default profile image will be used.
                                @endif
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Upload New Image</label>

                            <!-- New Image Preview -->
                            <div class="image-preview-wrapper" id="newImagePreview" style="display: none; margin-bottom: 1rem;">
                                <div class="image-preview" style="position: relative; display: inline-block; border-radius: 50%; overflow: hidden; border: 3px solid #dee2e6; width: 150px; height: 150px;">
                                    <img id="previewImg" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                    <button type="button" class="image-preview__remove" onclick="removeImagePreview()" style="position: absolute; top: 5px; right: 5px; background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <input type="file"
                                   class="form-control @error('avatar') is-invalid @enderror"
                                   id="avatar"
                                   name="avatar"
                                   accept="image/*">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Recommended size: 300x300px. Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB.
                            </small>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Roles and Status -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-user-tag"></i>
                            Roles & Status
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="mb-3">
                            <label class="form-label">Roles <span class="text-danger">*</span></label>
                            <div class="roles-wrapper" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 1rem;">
                                @foreach($roles as $role)
                                    <div class="form-check mb-2">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               id="role_{{ $role->id }}"
                                               name="roles[]"
                                               value="{{ $role->id }}"
                                               {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            <strong>{{ $role->name }}</strong>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Select one or more roles to assign to this admin user.
                            </small>
                            @error('roles')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required {{ $user->hasRole('SuperAdmin') ? 'disabled' : '' }}>
                                <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @if($user->hasRole('SuperAdmin'))
                                <input type="hidden" name="status" value="1">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    SuperAdmin status cannot be changed.
                                </small>
                            @else
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Active users can log in to the admin panel. Inactive users cannot access the system.
                                </small>
                            @endif
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i>
                        Update Admin User
                    </button>
                    <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            @include('admin.admin-user.partials.tips')
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
    const passwordIcon = document.getElementById('passwordIcon');
    const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');

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

    // Image preview functionality
    const imageInput = document.getElementById('avatar');
    const newImagePreview = document.getElementById('newImagePreview');
    const previewImg = document.getElementById('previewImg');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    newImagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                newImagePreview.style.display = 'none';
            }
        });
    }
});

// Remove new image preview
function removeImagePreview() {
    const imageInput = document.getElementById('avatar');
    const newImagePreview = document.getElementById('newImagePreview');
    if (imageInput) {
        imageInput.value = '';
    }
    if (newImagePreview) {
        newImagePreview.style.display = 'none';
    }
}

// Remove current image (for edit page)
function removeCurrentImage() {
    if (confirm('Are you sure you want to remove the current image? You will need to upload a new one.')) {
        const currentPreview = document.getElementById('currentImagePreview');
        if (currentPreview) {
            currentPreview.style.display = 'none';
        }
        // Add hidden input to indicate image removal
        const form = document.querySelector('form');
        if (form && !form.querySelector('input[name="remove_avatar"]')) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'remove_avatar';
            hiddenInput.value = '1';
            form.appendChild(hiddenInput);
        }
    }
}
</script>
</div>
@endsection
