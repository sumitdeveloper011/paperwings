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

// Toggle provider fields visibility
function toggleProviderFields(provider) {
    const fields = document.getElementById(provider + 'Fields');
    const checkbox = document.getElementById(provider + '_login_enabled');

    if (fields) {
        if (checkbox && checkbox.checked) {
            fields.style.display = 'block';
        } else {
            fields.style.display = 'none';
        }
    }
}

// Initialize provider fields visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleProviderFields('google');
    toggleProviderFields('facebook');
});
</script>
@endsection

