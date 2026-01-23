@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-key"></i>
                    API Settings
                </h1>
                <p class="page-header__subtitle">Manage API settings and feature toggles</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="content-body">
        <form method="POST" action="{{ route('admin.api-settings.update') }}" id="apiSettingsForm">
            @csrf
            @method('PUT')

            <div class="row">
            <!-- Left Column - Settings -->
            <div class="col-lg-8">
                <!-- Platform Fee -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-percentage"></i>
                            Platform Fee
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                Enable Platform Fee (Recommended: <span style="color: #dc3545;">Disabled</span>)
                            </label>
                            <label class="toggle-switch">
                                <input type="checkbox" 
                                       id="platform_fee_enabled" 
                                       name="platform_fee_enabled" 
                                       value="1"
                                       {{ old('platform_fee_enabled', $settings['platform_fee_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                                       disabled>
                                <span class="toggle-slider"></span>
                            </label>
                            <div class="form-hint" style="margin-top: 12px; padding: 12px; background-color: #e3f2fd; border-left: 4px solid #2196f3; border-radius: 4px;">
                                <strong><i class="fas fa-info-circle"></i> Future Implementation:</strong>
                                <p style="margin: 8px 0 0 0;">This feature is currently disabled and will be available in a future update. Platform fee functionality allows you to charge customers an additional fee on top of their order total.</p>
                            </div>
                        </div>

                        <div class="form-group-modern" id="platform_fee_percentage_group" style="{{ old('platform_fee_enabled', $settings['platform_fee_enabled'] ?? '0') == '1' ? '' : 'display: none;' }}">
                            <label for="platform_fee_percentage" class="form-label-modern">
                                Platform Fee Percentage (%)
                            </label>
                            <div class="input-wrapper">
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       class="form-input-modern @error('platform_fee_percentage') is-invalid @enderror"
                                       id="platform_fee_percentage"
                                       name="platform_fee_percentage"
                                       value="{{ old('platform_fee_percentage', $settings['platform_fee_percentage'] ?? '0') }}"
                                       placeholder="2.5">
                                <span class="input-suffix">%</span>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Percentage of order total to charge as platform fee (e.g., 2.5 for 2.5%)
                            </div>
                            @error('platform_fee_percentage')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pass Stripe Fee to Customer -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-credit-card"></i>
                            Pass Stripe Processing Fee to Customer
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                Pass Stripe Fee to Customer (Recommended: <span style="color: #dc3545;">Disabled</span>)
                            </label>
                            <label class="toggle-switch">
                                <input type="checkbox" 
                                       id="pass_stripe_fee_to_customer" 
                                       name="pass_stripe_fee_to_customer" 
                                       value="1" 
                                       {{ old('pass_stripe_fee_to_customer', $settings['pass_stripe_fee_to_customer'] ?? '0') == '1' ? 'checked' : '' }}
                                       disabled>
                                <span class="toggle-slider"></span>
                            </label>
                            <div class="form-hint" style="margin-top: 12px; padding: 12px; background-color: #e3f2fd; border-left: 4px solid #2196f3; border-radius: 4px;">
                                <strong><i class="fas fa-info-circle"></i> Future Implementation:</strong>
                                <p style="margin: 8px 0 0 0;">This feature is currently disabled and will be available in a future update. When enabled, the Stripe processing fee (2.9% + $0.30 per transaction) will be passed to customers and added to their order total.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Login APIs Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-sign-in-alt"></i>
                            Social Login Configuration
                        </h3>
                        <p class="modern-card__subtitle">Configure Google and Facebook OAuth for social login</p>
                    </div>
                    <div class="modern-card__body">
                        <!-- Google OAuth -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <input type="checkbox" name="google_login_enabled" value="1" id="google_login_enabled" {{ old('google_login_enabled', $settings['google_login_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                <i class="fab fa-google"></i>
                                Enable Google Login
                            </label>
                        </div>

                        <div class="form-hint" style="margin-top: 12px; padding: 12px; background-color: #e3f2fd; border-left: 4px solid #2196f3; border-radius: 4px;">
                            <strong><i class="fas fa-info-circle"></i> API Keys Configuration:</strong>
                            <p style="margin: 8px 0 0 0;">Google OAuth API keys should be configured in your <code>.env</code> file using <code>GOOGLE_CLIENT_ID</code> and <code>GOOGLE_CLIENT_SECRET</code> environment variables.</p>
                        </div>

                        <!-- Facebook OAuth -->
                        <div class="form-group-modern" style="margin-top: 1.5rem;">
                            <label class="form-label-modern">
                                <input type="checkbox" name="facebook_login_enabled" value="1" id="facebook_login_enabled" {{ old('facebook_login_enabled', $settings['facebook_login_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                <i class="fab fa-facebook"></i>
                                Enable Facebook Login
                            </label>
                        </div>

                        <div class="form-hint" style="margin-top: 12px; padding: 12px; background-color: #e3f2fd; border-left: 4px solid #2196f3; border-radius: 4px;">
                            <strong><i class="fas fa-info-circle"></i> API Keys Configuration:</strong>
                            <p style="margin: 8px 0 0 0;">Facebook OAuth API keys should be configured in your <code>.env</code> file using <code>FACEBOOK_CLIENT_ID</code> and <code>FACEBOOK_CLIENT_SECRET</code> environment variables.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column - Sidebar -->
            <div class="col-lg-4">
                <!-- Tips Card -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-lightbulb"></i>
                            Tips
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <ul class="tips-list">
                            <li class="tips-list__item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Environment Variables</strong>
                                    <p>All API keys are configured in your <code>.env</code> file for security. Never commit API keys to version control.</p>
                                </div>
                            </li>
                            <li class="tips-list__item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Secure Configuration</strong>
                                    <p>API keys are read directly from environment variables, ensuring they are never stored in the database.</p>
                                </div>
                            </li>
                            <li class="tips-list__item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Test vs Production</strong>
                                    <p>Use test/sandbox keys (pk_test_, sk_test_) during development. Switch to live keys (pk_live_, sk_live_) only in production</p>
                                </div>
                            </li>
                            <li class="tips-list__item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Never Share Publicly</strong>
                                    <p>Never commit API keys to version control or share them in public forums, emails, or screenshots</p>
                                </div>
                            </li>
                            <li class="tips-list__item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Regular Rotation</strong>
                                    <p>Change your API keys periodically (every 3-6 months) for enhanced security, especially after team member changes</p>
                                </div>
                            </li>
                            <li class="tips-list__item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Monitor API Usage</strong>
                                    <p>Regularly check your API dashboards (Stripe, Instagram, etc.) for unusual activity or unauthorized access</p>
                                </div>
                            </li>
                            <li class="tips-list__item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Required vs Optional</strong>
                                    <p>Stripe is required for payments. Other services (Google OAuth, Instagram, NZ Post) are optional features</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Configuration Status Card -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-info-circle"></i>
                            Configuration Status
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="api-config-status">
                            <div class="api-config-status__item">
                                <div class="api-config-status__label">
                                    <i class="fab fa-stripe api-config-status__label-icon"></i>
                                    Stripe
                                </div>
                                <div class="api-config-status__value">
                                    @if(!empty(config('services.stripe.key')) && !empty(config('services.stripe.secret')))
                                        <span class="api-status-badge api-status-badge--success">Configured</span>
                                    @else
                                        <span class="api-status-badge api-status-badge--warning">Required</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="api-config-status__item">
                                <div class="api-config-status__label">
                                    <i class="fas fa-shopping-cart api-config-status__label-icon"></i>
                                    EPOSNOW
                                </div>
                                <div class="api-config-status__value">
                                    @if(!empty(config('eposnow.api_key')))
                                        <span class="api-status-badge api-status-badge--success">Configured</span>
                                    @else
                                        <span class="api-status-badge api-status-badge--secondary">Optional</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="api-config-status__item">
                                <div class="api-config-status__label">
                                    <i class="fab fa-google api-config-status__label-icon"></i>
                                    Google OAuth
                                </div>
                                <div class="api-config-status__value">
                                    @if(!empty(config('services.google.client_id')) && !empty(config('services.google.client_secret')))
                                        <span class="api-status-badge api-status-badge--success">Configured</span>
                                    @else
                                        <span class="api-status-badge api-status-badge--secondary">Optional</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="api-config-status__item">
                                <div class="api-config-status__label">
                                    <i class="fas fa-map-marker-alt api-config-status__label-icon"></i>
                                    NZ Post
                                </div>
                                <div class="api-config-status__value">
                                    @if(!empty(config('services.nzpost.api_key')))
                                        <span class="api-status-badge api-status-badge--success">Configured</span>
                                    @else
                                        <span class="api-status-badge api-status-badge--secondary">Optional</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Services Overview Card -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-question-circle"></i>
                            What Each API Does
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <ul class="api-services-list">
                            <li class="api-services-list__item">
                                <i class="fab fa-stripe api-services-list__icon"></i>
                                <div class="api-services-list__content">
                                    <div class="api-services-list__title">Stripe</div>
                                    <p class="api-services-list__description">Payment processing for customer orders and subscription management</p>
                                </div>
                            </li>
                            <li class="api-services-list__item">
                                <i class="fas fa-shopping-cart api-services-list__icon"></i>
                                <div class="api-services-list__content">
                                    <div class="api-services-list__title">EPOSNOW</div>
                                    <p class="api-services-list__description">Product and inventory synchronization from POS system</p>
                                </div>
                            </li>
                            <li class="api-services-list__item">
                                <i class="fab fa-google api-services-list__icon"></i>
                                <div class="api-services-list__content">
                                    <div class="api-services-list__title">Google Services</div>
                                    <p class="api-services-list__description">OAuth login for social authentication</p>
                                </div>
                            </li>
                            <li class="api-services-list__item">
                                <i class="fab fa-facebook api-services-list__icon"></i>
                                <div class="api-services-list__content">
                                    <div class="api-services-list__title">Facebook Login</div>
                                    <p class="api-services-list__description">Social authentication allowing users to sign in with Facebook</p>
                                </div>
                            </li>
                            <li class="api-services-list__item">
                                <i class="fab fa-instagram api-services-list__icon"></i>
                                <div class="api-services-list__content">
                                    <div class="api-services-list__title">Instagram</div>
                                    <p class="api-services-list__description">Display Instagram feed and posts directly on your website</p>
                                </div>
                            </li>
                            <li class="api-services-list__item">
                                <i class="fas fa-map-marker-alt api-services-list__icon"></i>
                                <div class="api-services-list__content">
                                    <div class="api-services-list__title">NZ Post</div>
                                    <p class="api-services-list__description">Address validation and autocomplete for checkout process</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-save"></i>
                            Actions
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save API Settings
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i>Reset Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Platform Fee Toggle
document.addEventListener('DOMContentLoaded', function() {
    const platformFeeEnabled = document.getElementById('platform_fee_enabled');
    const platformFeePercentageGroup = document.getElementById('platform_fee_percentage_group');
    
    if (platformFeeEnabled && platformFeePercentageGroup) {
        platformFeeEnabled.addEventListener('change', function() {
            if (this.checked) {
                platformFeePercentageGroup.style.display = 'block';
            } else {
                platformFeePercentageGroup.style.display = 'none';
            }
        });
    }
});
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin-api-settings.css') }}">
@endpush
@endsection
