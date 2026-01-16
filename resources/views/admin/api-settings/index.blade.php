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
                                       value="{{ old('eposnow_api_key', \App\Helpers\SettingHelper::get('eposnow_api_key', env('EPOSNOW_API_KEY', ''))) }}"
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
                                       value="{{ old('eposnow_api_secret', \App\Helpers\SettingHelper::get('eposnow_api_secret', env('EPOSNOW_API_SECRET', ''))) }}"
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
                                       value="{{ old('eposnow_api_base', \App\Helpers\SettingHelper::get('eposnow_api_base', env('EPOSNOW_API_BASE', ''))) }}"
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
                                       value="{{ old('stripe_key', \App\Helpers\SettingHelper::get('stripe_key', env('STRIPE_KEY', ''))) }}"
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
                                       value="{{ old('stripe_webhook_secret', \App\Helpers\SettingHelper::get('stripe_webhook_secret', env('STRIPE_WEBHOOK_SECRET', ''))) }}"
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

                        <div id="googleFields" style="display: {{ old('google_login_enabled', $settings['google_login_enabled'] ?? '0') == '1' ? 'block' : 'none' }};">
                            <div class="form-group-modern">
                                <label for="google_client_id" class="form-label-modern">
                                    <i class="fab fa-google"></i>
                                    Google Client ID
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-key input-icon"></i>
                                    <input type="password"
                                           class="form-input-modern @error('google_client_id') is-invalid @enderror"
                                           id="google_client_id"
                                           name="google_client_id"
                                           value="{{ old('google_client_id', \App\Helpers\SettingHelper::get('google_client_id', env('GOOGLE_CLIENT_ID', ''))) }}"
                                           placeholder="Enter Google Client ID">
                                    <button type="button" class="password-toggle" onclick="togglePassword('google_client_id')" title="Show/Hide">
                                        <i class="fas fa-eye" id="toggle_google_client_id"></i>
                                    </button>
                                </div>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i>
                                    Get your keys from <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a>
                                </div>
                                @error('google_client_id')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

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
                                           value="{{ old('google_client_secret', \App\Helpers\SettingHelper::get('google_client_secret', env('GOOGLE_CLIENT_SECRET', ''))) }}"
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
                        </div>

                        <!-- Facebook OAuth -->
                        <div class="form-group-modern" style="margin-top: 1.5rem;">
                            <label class="form-label-modern">
                                <input type="checkbox" name="facebook_login_enabled" value="1" id="facebook_login_enabled" {{ old('facebook_login_enabled', $settings['facebook_login_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                <i class="fab fa-facebook"></i>
                                Enable Facebook Login
                            </label>
                        </div>

                        <div id="facebookFields" style="display: {{ old('facebook_login_enabled', $settings['facebook_login_enabled'] ?? '0') == '1' ? 'block' : 'none' }};">
                            <div class="form-group-modern">
                                <label for="facebook_client_id" class="form-label-modern">
                                    <i class="fab fa-facebook"></i>
                                    Facebook App ID
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-key input-icon"></i>
                                    <input type="password"
                                           class="form-input-modern @error('facebook_client_id') is-invalid @enderror"
                                           id="facebook_client_id"
                                           name="facebook_client_id"
                                           value="{{ old('facebook_client_id', $settings['facebook_client_id'] ?? env('FACEBOOK_CLIENT_ID', '')) }}"
                                           placeholder="Enter Facebook App ID">
                                    <button type="button" class="password-toggle" onclick="togglePassword('facebook_client_id')" title="Show/Hide">
                                        <i class="fas fa-eye" id="toggle_facebook_client_id"></i>
                                    </button>
                                </div>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i>
                                    Get your keys from <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers</a>
                                </div>
                                @error('facebook_client_id')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

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
                                           value="{{ old('facebook_client_secret', \App\Helpers\SettingHelper::get('facebook_client_secret', env('FACEBOOK_CLIENT_SECRET', ''))) }}"
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
                        </div>
                    </div>
                </div>

                <!-- Address Services Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-map-marker-alt"></i>
                            Address Services Configuration
                        </h3>
                        <p class="modern-card__subtitle">Configure NZ Post address validation</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-hint" style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #28a745;">
                            <strong><i class="fas fa-info-circle"></i> NZ Post Integration:</strong>
                            <p style="margin: 0.5rem 0 0 0;">Enter your NZ Post API key to enable address autocomplete and validation during checkout.</p>
                        </div>

                        <div class="form-group-modern">
                            <label for="nzpost_api_key" class="form-label-modern">
                                <i class="fas fa-key"></i>
                                NZ Post API Key
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('nzpost_api_key') is-invalid @enderror"
                                       id="nzpost_api_key"
                                       name="nzpost_api_key"
                                       value="{{ old('nzpost_api_key', \App\Helpers\SettingHelper::get('nzpost_api_key', env('NZPOST_API_KEY', ''))) }}"
                                       placeholder="Enter NZ Post API Key">
                                <button type="button" class="password-toggle" onclick="togglePassword('nzpost_api_key')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_nzpost_api_key"></i>
                                </button>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Get your API key from <a href="https://www.nzpost.co.nz/business/developer-centre" target="_blank">NZ Post Developer Centre</a>
                            </div>
                            @error('nzpost_api_key')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Google Maps API Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-map"></i>
                            Google Maps API Configuration
                        </h3>
                        <p class="modern-card__subtitle">Configure Google Maps API for dynamic maps</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-hint" style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #4285F4;">
                            <strong><i class="fas fa-info-circle"></i> Google Maps Integration:</strong>
                            <p style="margin: 0.5rem 0 0 0;">Enter your Google Maps API Key to enable dynamic maps instead of embed code. Get your API key from <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>.</p>
                        </div>

                        <div class="form-group-modern">
                            <label for="google_map_api_key" class="form-label-modern">
                                <i class="fas fa-key"></i>
                                Google Maps API Key
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('google_map_api_key') is-invalid @enderror"
                                       id="google_map_api_key"
                                       name="google_map_api_key"
                                       value="{{ old('google_map_api_key', \App\Helpers\SettingHelper::get('google_map_api_key', env('GOOGLE_MAP_API_KEY', ''))) }}"
                                       placeholder="Enter Google Maps API Key">
                                <button type="button" class="password-toggle" onclick="togglePassword('google_map_api_key')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_google_map_api_key"></i>
                                </button>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Optional: Used for dynamic maps. If not provided, embed code from Site Settings will be used.
                            </div>
                            @error('google_map_api_key')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Instagram API Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fab fa-instagram"></i>
                            Instagram API Configuration
                        </h3>
                        <p class="modern-card__subtitle">Configure Instagram API to display your posts on the website</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-hint" style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #E4405F;">
                            <strong><i class="fas fa-info-circle"></i> Setup Instructions:</strong>
                            <ol style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                                <li>Convert your Instagram account to a Business or Creator account</li>
                                <li>Create a Facebook App at <a href="https://developers.facebook.com/" target="_blank">developers.facebook.com</a></li>
                                <li>Add Instagram Basic Display product to your app</li>
                                <li>Generate an Access Token from the Instagram Basic Display settings</li>
                                <li>Enter your credentials below</li>
                            </ol>
                        </div>

                        <!-- App ID -->
                        <div class="form-group-modern">
                            <label for="instagram_app_id" class="form-label-modern">
                                <i class="fas fa-key"></i>
                                Instagram App ID
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('instagram_app_id') is-invalid @enderror"
                                       id="instagram_app_id"
                                       name="instagram_app_id"
                                       value="{{ old('instagram_app_id', \App\Helpers\SettingHelper::get('instagram_app_id', env('INSTAGRAM_APP_ID', ''))) }}"
                                       placeholder="Enter your Instagram App ID">
                                <button type="button" class="password-toggle" onclick="togglePassword('instagram_app_id')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_instagram_app_id"></i>
                                </button>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Found in your Facebook App Dashboard → Settings → Basic
                            </div>
                            @error('instagram_app_id')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- App Secret -->
                        <div class="form-group-modern">
                            <label for="instagram_app_secret" class="form-label-modern">
                                <i class="fas fa-lock"></i>
                                Instagram App Secret
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('instagram_app_secret') is-invalid @enderror"
                                       id="instagram_app_secret"
                                       name="instagram_app_secret"
                                       value="{{ old('instagram_app_secret', \App\Helpers\SettingHelper::get('instagram_app_secret', env('INSTAGRAM_APP_SECRET', ''))) }}"
                                       placeholder="Enter your Instagram App Secret">
                                <button type="button" class="password-toggle" onclick="togglePassword('instagram_app_secret')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_instagram_app_secret"></i>
                                </button>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Found in your Facebook App Dashboard → Settings → Basic
                            </div>
                            @error('instagram_app_secret')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Access Token -->
                        <div class="form-group-modern">
                            <label for="instagram_access_token" class="form-label-modern">
                                <i class="fas fa-token"></i>
                                Instagram Access Token
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-token input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('instagram_access_token') is-invalid @enderror"
                                       id="instagram_access_token"
                                       name="instagram_access_token"
                                       value="{{ old('instagram_access_token', \App\Helpers\SettingHelper::get('instagram_access_token', env('INSTAGRAM_ACCESS_TOKEN', ''))) }}"
                                       placeholder="Enter your Instagram Access Token">
                                <button type="button" class="password-toggle" onclick="togglePassword('instagram_access_token')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_instagram_access_token"></i>
                                </button>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Generated from Instagram Basic Display → User Token Generator (expires in 60 days)
                            </div>
                            @error('instagram_access_token')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- User ID -->
                        <div class="form-group-modern">
                            <label for="instagram_user_id" class="form-label-modern">
                                <i class="fas fa-user"></i>
                                Instagram User ID
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('instagram_user_id') is-invalid @enderror"
                                       id="instagram_user_id"
                                       name="instagram_user_id"
                                       value="{{ old('instagram_user_id', \App\Helpers\SettingHelper::get('instagram_user_id', env('INSTAGRAM_USER_ID', ''))) }}"
                                       placeholder="Enter your Instagram User ID">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Your Instagram Business/Creator account ID (usually found in the API response)
                            </div>
                            @error('instagram_user_id')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Test Connection Button -->
                        <div class="form-group-modern">
                            <button type="button" class="btn btn-outline-primary" id="testInstagramConnection">
                                <i class="fas fa-plug"></i>
                                Test Connection
                            </button>
                            <div id="instagramTestResult" style="margin-top: 1rem; display: none;"></div>
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

    // Add event listeners for checkboxes
    const googleCheckbox = document.getElementById('google_login_enabled');
    const facebookCheckbox = document.getElementById('facebook_login_enabled');

    if (googleCheckbox) {
        googleCheckbox.addEventListener('change', function() {
            toggleProviderFields('google');
        });
    }

    if (facebookCheckbox) {
        facebookCheckbox.addEventListener('change', function() {
            toggleProviderFields('facebook');
        });
    }

    // Instagram Test Connection
    const testInstagramBtn = document.getElementById('testInstagramConnection');
    if (testInstagramBtn) {
        testInstagramBtn.addEventListener('click', function() {
            const btn = this;
            const resultDiv = document.getElementById('instagramTestResult');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
            resultDiv.style.display = 'none';
            
            const formData = {
                instagram_app_id: document.getElementById('instagram_app_id')?.value || '',
                instagram_app_secret: document.getElementById('instagram_app_secret')?.value || '',
                instagram_access_token: document.getElementById('instagram_access_token')?.value || '',
                instagram_user_id: document.getElementById('instagram_user_id')?.value || ''
            };
            
            fetch('{{ route("admin.api-settings.test-instagram") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.style.display = 'block';
                if (data.success) {
                    resultDiv.className = 'alert alert-success';
                    resultDiv.innerHTML = `
                        <strong><i class="fas fa-check-circle"></i> ${data.message}</strong>
                        ${data.data ? `
                            <ul style="margin-top: 0.5rem; margin-bottom: 0;">
                                <li>Username: ${data.data.username || 'N/A'}</li>
                                <li>Account Type: ${data.data.account_type || 'N/A'}</li>
                                <li>Media Count: ${data.data.media_count || 0}</li>
                            </ul>
                        ` : ''}
                    `;
                } else {
                    resultDiv.className = 'alert alert-danger';
                    resultDiv.innerHTML = `<strong><i class="fas fa-times-circle"></i> ${data.message}</strong>`;
                }
            })
            .catch(error => {
                resultDiv.style.display = 'block';
                resultDiv.className = 'alert alert-danger';
                resultDiv.innerHTML = `<strong><i class="fas fa-times-circle"></i> Error: ${error.message}</strong>`;
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    }
});
</script>
@endsection

