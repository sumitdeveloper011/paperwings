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
        <div class="col-lg-8">
            <div class="modern-card modern-card--compact">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Admin User Information</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.admin-users.update', $user) }}" class="modern-form">
                        @csrf
                        @method('PUT')

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
                                               value="{{ old('first_name', $user->first_name) }}"
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
                                               value="{{ old('last_name', $user->last_name) }}"
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
                                       value="{{ old('email', $user->email) }}"
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
                                    <label for="password" class="form-label-modern">Password</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password"
                                               class="form-input-modern @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               placeholder="Leave blank to keep current password">
                                    </div>
                                    <small class="form-text text-muted">Leave blank to keep current password</small>
                                    @error('password')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="password_confirmation" class="form-label-modern">Confirm Password</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password"
                                               class="form-input-modern"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Confirm new password">
                                    </div>
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
                                       value="{{ old('phone', $user->phone) }}"
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
                                               {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                                        id="status" name="status" required {{ $user->hasRole('SuperAdmin') ? 'disabled' : '' }}>
                                    <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @if($user->hasRole('SuperAdmin'))
                                    <input type="hidden" name="status" value="1">
                                    <small class="form-text text-muted">SuperAdmin status cannot be changed</small>
                                @endif
                            </div>
                            @error('status')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Admin User
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
@endsection
