@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit User
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="modern-form" id="userEditForm">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="modern-card form-section">
                    <div class="modern-card__header">
                        <div class="modern-card__header-content">
                            <h3 class="modern-card__title">
                                <i class="fas fa-user"></i>
                                Basic Information
                            </h3>
                            <p class="modern-card__subtitle">Update user's personal details</p>
                        </div>
                    </div>
                    <div class="modern-card__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="first_name" class="form-label-modern">
                                        First Name <span class="required">*</span>
                                        <span class="form-tooltip" data-tooltip="Enter the user's first name. This will be displayed in their profile.">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text"
                                               class="form-input-modern @error('first_name') is-invalid @enderror"
                                               id="first_name"
                                               name="first_name"
                                               value="{{ old('first_name', $user->first_name) }}"
                                               placeholder="Enter first name"
                                               required>
                                    </div>
                                    @error('first_name')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                    <div class="form-tip">
                                        <i class="fas fa-lightbulb"></i>
                                        <span>Use the user's legal first name as it appears on official documents.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="last_name" class="form-label-modern">
                                        Last Name <span class="required">*</span>
                                        <span class="form-tooltip" data-tooltip="Enter the user's last name or surname.">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text"
                                               class="form-input-modern @error('last_name') is-invalid @enderror"
                                               id="last_name"
                                               name="last_name"
                                               value="{{ old('last_name', $user->last_name) }}"
                                               placeholder="Enter last name"
                                               required>
                                    </div>
                                    @error('last_name')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                    <div class="form-tip">
                                        <i class="fas fa-lightbulb"></i>
                                        <span>Ensure the last name matches the first name for consistency.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label for="email" class="form-label-modern">
                                Email Address <span class="required">*</span>
                                <span class="form-tooltip" data-tooltip="Email must be unique and valid. This will be used for account login and notifications.">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email"
                                       class="form-input-modern @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $user->email) }}"
                                       placeholder="Enter email address"
                                       required>
                            </div>
                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            <div class="form-tip form-tip--warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span><strong>Important:</strong> Changing email will require the user to verify the new email address.</span>
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label for="phone" class="form-label-modern">
                                Phone Number
                                <span class="form-tooltip" data-tooltip="Optional phone number for contact purposes. Include country code if applicable.">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('phone') is-invalid @enderror"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="Enter phone number (e.g., +1 234 567 8900)">
                            </div>
                            @error('phone')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            <div class="form-tip">
                                <i class="fas fa-lightbulb"></i>
                                <span>Include country code for international numbers (e.g., +1 for USA, +44 for UK).</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Section -->
                <div class="modern-card form-section" style="margin-top: 1.5rem;">
                    <div class="modern-card__header">
                        <div class="modern-card__header-content">
                            <h3 class="modern-card__title">
                                <i class="fas fa-shield-alt"></i>
                                Security Settings
                            </h3>
                            <p class="modern-card__subtitle">Manage password and account security</p>
                        </div>
                    </div>
                    <div class="modern-card__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="password" class="form-label-modern">
                                        New Password
                                        <span class="form-tooltip" data-tooltip="Leave blank to keep the current password. Minimum 8 characters recommended.">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password"
                                               class="form-input-modern @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               placeholder="Enter new password">
                                    </div>
                                    @error('password')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                    <div class="form-tip form-tip--info">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Leave blank if you don't want to change the password. Password must be at least 8 characters.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="password_confirmation" class="form-label-modern">
                                        Confirm Password
                                        <span class="form-tooltip" data-tooltip="Re-enter the new password to confirm it matches.">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password"
                                               class="form-input-modern"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Confirm new password">
                                    </div>
                                    <div class="form-tip">
                                        <i class="fas fa-lightbulb"></i>
                                        <span>Both password fields must match exactly.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions & Status Section -->
                <div class="modern-card form-section" style="margin-top: 1.5rem;">
                    <div class="modern-card__header">
                        <div class="modern-card__header-content">
                            <h3 class="modern-card__title">
                                <i class="fas fa-user-shield"></i>
                                Permissions & Status
                            </h3>
                            <p class="modern-card__subtitle">Manage user roles and account status</p>
                        </div>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                User Roles
                                <span class="form-tooltip" data-tooltip="Select one or more roles to assign permissions. Each role grants specific access rights.">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                            </label>
                            <div class="roles-wrapper">
                                <div class="roles-grid">
                                    @foreach($roles as $role)
                                        <div class="role-checkbox-item">
                                            <input type="checkbox"
                                                   class="role-checkbox"
                                                   id="role_{{ $role->id }}"
                                                   name="roles[]"
                                                   value="{{ $role->id }}"
                                                   {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="role-label" for="role_{{ $role->id }}">
                                                <div class="role-label__icon">
                                                    <i class="fas fa-user-tag"></i>
                                                </div>
                                                <div class="role-label__content">
                                                    <strong class="role-label__name">{{ $role->name }}</strong>
                                                    @if($role->description)
                                                        <span class="role-label__description">{{ $role->description }}</span>
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('roles')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            <div class="form-tip form-tip--info">
                                <i class="fas fa-info-circle"></i>
                                <span><strong>Tip:</strong> Users can have multiple roles. Select all roles that apply to grant combined permissions.</span>
                            </div>
                        </div>

                        <div class="form-group-modern" style="margin-top: 1.5rem;">
                            <label for="status" class="form-label-modern">
                                Account Status <span class="required">*</span>
                                <span class="form-tooltip" data-tooltip="Active users can log in and use the system. Inactive users cannot access their account.">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-toggle-on input-icon"></i>
                                <select class="form-input-modern @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            <div class="form-tip form-tip--warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span><strong>Note:</strong> Setting status to Inactive will prevent the user from logging in immediately.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions-section">
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i>
                            Update User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                    <div class="form-actions-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>All changes will be saved immediately. The user will be notified of any significant changes.</span>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tips Sidebar -->
        <div class="col-lg-4">
            <div class="modern-card tips-sidebar">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-lightbulb"></i>
                        Editing Tips
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="tips-list">
                        <div class="tip-item">
                            <div class="tip-item__icon tip-item__icon--info">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="tip-item__content">
                                <strong>Email Changes</strong>
                                <p>Changing a user's email requires verification. The user will receive an email to confirm the new address.</p>
                            </div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-item__icon tip-item__icon--warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="tip-item__content">
                                <strong>Password Security</strong>
                                <p>Only update passwords when necessary. Users can reset their own passwords via the login page.</p>
                            </div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-item__icon tip-item__icon--success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="tip-item__content">
                                <strong>Role Management</strong>
                                <p>Assign roles carefully. Each role grants specific permissions that affect what users can access and modify.</p>
                            </div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-item__icon tip-item__icon--primary">
                                <i class="fas fa-user-lock"></i>
                            </div>
                            <div class="tip-item__content">
                                <strong>Account Status</strong>
                                <p>Use "Inactive" status to temporarily disable access without deleting the account. Users can be reactivated later.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Form Sections */
    .form-section {
        margin-bottom: 1.5rem;
    }

    /* Form Tooltips */
    .form-tooltip {
        display: inline-flex;
        align-items: center;
        margin-left: 0.5rem;
        color: #6c757d;
        cursor: help;
        position: relative;
    }

    .form-tooltip i {
        font-size: 0.875rem;
        transition: color 0.2s ease;
    }

    .form-tooltip:hover i {
        color: var(--primary-color);
    }

    .form-tooltip[data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        padding: 0.5rem 0.75rem;
        background: #2c3e50;
        color: white;
        border-radius: 6px;
        font-size: 0.8rem;
        white-space: nowrap;
        z-index: 1000;
        margin-bottom: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .form-tooltip[data-tooltip]:hover::before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: #2c3e50;
        margin-bottom: -5px;
        z-index: 1000;
    }

    /* Form Tips */
    .form-tip {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        margin-top: 0.5rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 6px;
        font-size: 0.875rem;
        line-height: 1.5;
        border-left: 3px solid #6c757d;
    }

    .form-tip i {
        color: #6c757d;
        margin-top: 0.125rem;
        flex-shrink: 0;
    }

    .form-tip--info {
        background: #e7f3ff;
        border-left-color: var(--info-color);
    }

    .form-tip--info i {
        color: var(--info-color);
    }

    .form-tip--warning {
        background: #fff3cd;
        border-left-color: var(--warning-color);
    }

    .form-tip--warning i {
        color: var(--warning-color);
    }

    .form-tip--success {
        background: #d4edda;
        border-left-color: var(--success-color);
    }

    .form-tip--success i {
        color: var(--success-color);
    }

    /* Roles Section */
    .roles-wrapper {
        margin-top: 0.5rem;
    }

    .roles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
        max-height: 400px;
        overflow-y: auto;
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background: #f8f9fa;
    }

    .role-checkbox-item {
        position: relative;
    }

    .role-checkbox {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .role-label {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .role-label:hover {
        border-color: var(--primary-color);
        box-shadow: 0 2px 8px rgba(40, 80, 163, 0.1);
    }

    .role-checkbox:checked + .role-label {
        border-color: var(--primary-color);
        background: rgba(40, 80, 163, 0.05);
    }

    .role-label__icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .role-checkbox:checked + .role-label .role-label__icon {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    }

    .role-label__content {
        flex: 1;
        min-width: 0;
    }

    .role-label__name {
        display: block;
        font-size: 1rem;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .role-label__description {
        display: block;
        font-size: 0.8rem;
        color: #6c757d;
        line-height: 1.4;
    }

    /* Form Actions Section */
    .form-actions-section {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e9ecef;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .form-actions-hint {
        margin-top: 1rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6c757d;
    }

    .form-actions-hint i {
        color: var(--info-color);
    }

    /* Tips Sidebar */
    .tips-sidebar {
        position: sticky;
        top: 100px;
    }

    .tips-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .tip-item {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .tip-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .tip-item__icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .tip-item__icon--info {
        background: linear-gradient(135deg, var(--info-color), #7ea5c0);
    }

    .tip-item__icon--warning {
        background: linear-gradient(135deg, var(--warning-color), #f5a742);
    }

    .tip-item__icon--success {
        background: linear-gradient(135deg, var(--success-color), #9bc84d);
    }

    .tip-item__icon--primary {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    }

    .tip-item__content {
        flex: 1;
        min-width: 0;
    }

    .tip-item__content strong {
        display: block;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .tip-item__content p {
        margin: 0;
        font-size: 0.85rem;
        color: #6c757d;
        line-height: 1.5;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .tips-sidebar {
            position: static;
            margin-top: 1.5rem;
        }

        .roles-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
    </style>
</div>
@endsection

