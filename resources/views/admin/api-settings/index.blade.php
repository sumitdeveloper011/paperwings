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
                <p class="page-header__subtitle">Manage all API keys and credentials</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.api-settings.update') }}" class="settings-form" id="apiSettingsForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Left Column - API Keys -->
            <div class="col-lg-8">
                <!-- EPOSNOW API Keys Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-shopping-cart"></i>
                            EPOSNOW API Configuration
                        </h3>
                        <p class="modern-card__subtitle">Configure EPOSNOW integration credentials</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-hint" style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
                            <strong><i class="fas fa-info-circle"></i> EPOSNOW Integration:</strong>
                            <p style="margin: 0.5rem 0 0 0;">Enter your EPOSNOW API credentials to enable product and category synchronization.</p>
                        </div>

                        <!-- EPOSNOW API Key -->
                        <div class="form-group-modern">
                            <label for="eposnow_api_key" class="form-label-modern">
                                <i class="fas fa-key"></i>
                                EPOSNOW API Key
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('eposnow_api_key') is-invalid @enderror"
                                       id="eposnow_api_key"
                                       name="eposnow_api_key"
                                       value="{{ old('eposnow_api_key', $settings['eposnow_api_key'] ?? env('EPOSNOW_API_KEY', '')) }}"
                                       placeholder="Enter EPOSNOW API Key">
                                <button type="button" class="password-toggle" onclick="togglePassword('eposnow_api_key')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_eposnow_api_key"></i>
                                </button>
                            </div>
                            @error('eposnow_api_key')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- EPOSNOW API Secret -->
                        <div class="form-group-modern">
                            <label for="eposnow_api_secret" class="form-label-modern">
                                <i class="fas fa-lock"></i>
                                EPOSNOW API Secret
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('eposnow_api_secret') is-invalid @enderror"
                                       id="eposnow_api_secret"
                                       name="eposnow_api_secret"
                                       value="{{ old('eposnow_api_secret', $settings['eposnow_api_secret'] ?? env('EPOSNOW_API_SECRET', '')) }}"
                                       placeholder="Enter EPOSNOW API Secret">
                                <button type="button" class="password-toggle" onclick="togglePassword('eposnow_api_secret')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_eposnow_api_secret"></i>
                                </button>
                            </div>
                            @error('eposnow_api_secret')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- EPOSNOW API Base URL -->
                        <div class="form-group-modern">
                            <label for="eposnow_api_base" class="form-label-modern">
                                <i class="fas fa-link"></i>
                                EPOSNOW API Base URL
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-link input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('eposnow_api_base') is-invalid @enderror"
                                       id="eposnow_api_base"
                                       name="eposnow_api_base"
                                       value="{{ old('eposnow_api_base', $settings['eposnow_api_base'] ?? env('EPOSNOW_API_BASE', '')) }}"
                                       placeholder="https://api.eposnowhq.com">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Base URL for EPOSNOW API endpoints
                            </div>
                            @error('eposnow_api_base')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Stripe API Keys Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fab fa-stripe"></i>
                            Stripe Payment Configuration
                        </h3>
                        <p class="modern-card__subtitle">Configure Stripe payment gateway credentials</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-hint" style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #635BFF;">
                            <strong><i class="fas fa-info-circle"></i> Stripe Integration:</strong>
                            <p style="margin: 0.5rem 0 0 0;">Enter your Stripe API keys to enable payment processing. Get your keys from <a href="https://dashboard.stripe.com/apikeys" target="_blank">Stripe Dashboard</a>.</p>
                        </div>

                        <!-- Stripe Publishable Key -->
                        <div class="form-group-modern">
                            <label for="stripe_key" class="form-label-modern">
                                <i class="fas fa-key"></i>
                                Stripe Publishable Key
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('stripe_key') is-invalid @enderror"
                                       id="stripe_key"
                                       name="stripe_key"
                                       value="{{ old('stripe_key', $settings['stripe_key'] ?? env('STRIPE_KEY', '')) }}"
                                       placeholder="pk_test_... or pk_live_...">
                                <button type="button" class="password-toggle" onclick="togglePassword('stripe_key')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_stripe_key"></i>
                                </button>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Your Stripe publishable key (starts with pk_test_ or pk_live_)
                            </div>
                            @error('stripe_key')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Stripe Secret Key -->
                        <div class="form-group-modern">
                            <label for="stripe_secret" class="form-label-modern">
                                <i class="fas fa-lock"></i>
                                Stripe Secret Key
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('stripe_secret') is-invalid @enderror"
                                       id="stripe_secret"
                                       name="stripe_secret"
                                       value="{{ old('stripe_secret', $settings['stripe_secret'] ?? env('STRIPE_SECRET', '')) }}"
                                       placeholder="sk_test_... or sk_live_...">
                                <button type="button" class="password-toggle" onclick="togglePassword('stripe_secret')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_stripe_secret"></i>
                                </button>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Your Stripe secret key (starts with sk_test_ or sk_live_). Keep this secure!
                            </div>
                            @error('stripe_secret')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Stripe Webhook Secret -->
                        <div class="form-group-modern">
                            <label for="stripe_webhook_secret" class="form-label-modern">
                                <i class="fas fa-key"></i>
                                Stripe Webhook Secret
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('stripe_webhook_secret') is-invalid @enderror"
                                       id="stripe_webhook_secret"
                                       name="stripe_webhook_secret"
                                       value="{{ old('stripe_webhook_secret', $settings['stripe_webhook_secret'] ?? env('STRIPE_WEBHOOK_SECRET', '')) }}"
                                       placeholder="whsec_...">
                                <button type="button" class="password-toggle" onclick="togglePassword('stripe_webhook_secret')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_stripe_webhook_secret"></i>
                                </button>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Your Stripe webhook signing secret (starts with whsec_). Used to verify webhook events from Stripe.
                            </div>
                            @error('stripe_webhook_secret')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Google OAuth Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fab fa-google"></i>
                            Google OAuth Configuration
                        </h3>
                        <p class="modern-card__subtitle">Configure Google social login</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-hint" style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #4285F4;">
                            <strong><i class="fas fa-info-circle"></i> Setup Instructions:</strong>
                            <ol style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                                <li>Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                                <li>Create OAuth 2.0 credentials</li>
                                <li>Add redirect URI: <code>{{ env('APP_URL') }}/auth/google/callback</code></li>
                                <li>Copy Client ID and Secret below</li>
                            </ol>
                        </div>

                        <!-- Enable Google Login -->
                        <div class="form-group-modern">
                            <div class="form-check-modern">
                                <input type="checkbox"
                                       class="form-check-input-modern"
                                       id="google_login_enabled"
                                       name="google_login_enabled"
                                       value="1"
                                       {{ old('google_login_enabled', $settings['google_login_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                                       onchange="toggleProviderFields('google')">
                                <label class="form-check-label-modern" for="google_login_enabled">
                                    <i class="fas fa-toggle-on"></i>
                                    Enable Google Login
                                </label>
                            </div>
                        </div>

                        <div id="googleFields">
                            <!-- Google Client ID -->
                            <div class="form-group-modern">
                                <label for="google_client_id" class="form-label-modern">
                                    <i class="fas fa-id-card"></i>
                                    Google Client ID
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text"
                                           class="form-input-modern @error('google_client_id') is-invalid @enderror"
                                           id="google_client_id"
                                           name="google_client_id"
                                           value="{{ old('google_client_id', $settings['google_client_id'] ?? env('GOOGLE_CLIENT_ID', '')) }}"
                                           placeholder="Enter Google Client ID">
                                </div>
                                @error('google_client_id')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Google Client Secret -->
                            <div class="form-group-modern">
                                <label for="google_client_secret" class="form-label-modern">
                                    <i class="fas fa-lock"></i>
                                    Google Client Secret
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password"
                                           class="form-input-modern @error('google_client_secret') is-invalid @enderror"
                                           id="google_client_secret"
                                           name="google_client_secret"
                                           value="{{ old('google_client_secret', $settings['google_client_secret'] ?? env('GOOGLE_CLIENT_SECRET', '')) }}"
                                           placeholder="Enter Google Client Secret">
                                    <button type="button" class="password-toggle" onclick="togglePassword('google_client_secret')" title="Show/Hide">
                                        <i class="fas fa-eye" id="toggle_google_client_secret"></i>
                                    </button>
                                </div>
                                @error('google_client_secret')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Redirect URI Info -->
                            <div class="form-hint" style="background: #e7f3ff; padding: 0.75rem; border-radius: 4px;">
                                <strong>Redirect URI:</strong> <code>{{ env('APP_URL') }}/auth/google/callback</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Facebook OAuth Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fab fa-facebook-f"></i>
                            Facebook OAuth Configuration
                        </h3>
                        <p class="modern-card__subtitle">Configure Facebook social login</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-hint" style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #1877F2;">
                            <strong><i class="fas fa-info-circle"></i> Setup Instructions:</strong>
                            <ol style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                                <li>Go to <a href="https://developers.facebook.com/" target="_blank">Facebook Developers</a></li>
                                <li>Create a new app and add Facebook Login product</li>
                                <li>Add redirect URI: <code>{{ env('APP_URL') }}/auth/facebook/callback</code></li>
                                <li>Copy App ID and App Secret below</li>
                            </ol>
                        </div>

                        <!-- Enable Facebook Login -->
                        <div class="form-group-modern">
                            <div class="form-check-modern">
                                <input type="checkbox"
                                       class="form-check-input-modern"
                                       id="facebook_login_enabled"
                                       name="facebook_login_enabled"
                                       value="1"
                                       {{ old('facebook_login_enabled', $settings['facebook_login_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                                       onchange="toggleProviderFields('facebook')">
                                <label class="form-check-label-modern" for="facebook_login_enabled">
                                    <i class="fas fa-toggle-on"></i>
                                    Enable Facebook Login
                                </label>
                            </div>
                        </div>

                        <div id="facebookFields">
                            <!-- Facebook App ID -->
                            <div class="form-group-modern">
                                <label for="facebook_client_id" class="form-label-modern">
                                    <i class="fas fa-id-card"></i>
                                    Facebook App ID
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text"
                                           class="form-input-modern @error('facebook_client_id') is-invalid @enderror"
                                           id="facebook_client_id"
                                           name="facebook_client_id"
                                           value="{{ old('facebook_client_id', $settings['facebook_client_id'] ?? env('FACEBOOK_CLIENT_ID', '')) }}"
                                           placeholder="Enter Facebook App ID">
                                </div>
                                @error('facebook_client_id')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Facebook App Secret -->
                            <div class="form-group-modern">
                                <label for="facebook_client_secret" class="form-label-modern">
                                    <i class="fas fa-lock"></i>
                                    Facebook App Secret
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password"
                                           class="form-input-modern @error('facebook_client_secret') is-invalid @enderror"
                                           id="facebook_client_secret"
                                           name="facebook_client_secret"
                                           value="{{ old('facebook_client_secret', $settings['facebook_client_secret'] ?? env('FACEBOOK_CLIENT_SECRET', '')) }}"
                                           placeholder="Enter Facebook App Secret">
                                    <button type="button" class="password-toggle" onclick="togglePassword('facebook_client_secret')" title="Show/Hide">
                                        <i class="fas fa-eye" id="toggle_facebook_client_secret"></i>
                                    </button>
                                </div>
                                @error('facebook_client_secret')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Redirect URI Info -->
                            <div class="form-hint" style="background: #e7f3ff; padding: 0.75rem; border-radius: 4px;">
                                <strong>Redirect URI:</strong> <code>{{ env('APP_URL') }}/auth/facebook/callback</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Actions -->
            <div class="col-lg-4">
                <!-- Save Card -->
                <div class="modern-card modern-card--sticky">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-save"></i>
                            Save Settings
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-save"></i>
                                Save API Settings
                            </button>
                            <button type="reset" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-undo"></i>
                                Reset Changes
                            </button>
                        </div>
                        <div class="form-info">
                            <div class="info-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>All keys are stored securely</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-info-circle"></i>
                                <span>Changes apply immediately</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Tips -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-shield-alt"></i>
                            Security Tips
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <ul class="tips-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Never share your API keys publicly</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Use test keys for development</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Rotate keys regularly for security</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Keep secret keys confidential</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Password toggle function
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const toggle = document.getElementById('toggle_' + inputId);

    if (input.type === 'password') {
        input.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

// Toggle provider fields based on enable/disable checkbox
function toggleProviderFields(provider) {
    const checkbox = document.getElementById(provider + '_login_enabled');
    const fieldsDiv = document.getElementById(provider + 'Fields');
    const inputs = fieldsDiv.querySelectorAll('input[type="text"], input[type="password"]');
    
    if (checkbox && fieldsDiv) {
        if (checkbox.checked) {
            fieldsDiv.style.opacity = '1';
            inputs.forEach(input => {
                input.disabled = false;
            });
        } else {
            fieldsDiv.style.opacity = '0.6';
            inputs.forEach(input => {
                input.disabled = true;
            });
        }
    }
}

// Initialize provider fields on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleProviderFields('google');
    toggleProviderFields('facebook');
});
</script>
@endsection

