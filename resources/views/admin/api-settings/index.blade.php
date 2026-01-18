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

    <div class="content-body">
        <form method="POST" action="{{ route('admin.api-settings.update') }}" id="apiSettingsForm">
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
                                EPOSNOW API Key
                            </label>
                            <div class="input-wrapper input-wrapper--with-action">
                                <input type="text"
                                       class="form-input-modern api-key-input @error('eposnow_api_key') is-invalid @enderror"
                                       id="eposnow_api_key"
                                       name="eposnow_api_key"
                                       value="{{ old('eposnow_api_key', $maskedSettings['eposnow_api_key'] ?? '') }}"
                                       placeholder="Enter EPOSNOW API Key"
                                       {{ !empty($maskedSettings['eposnow_api_key']) ? 'readonly' : '' }}
                                       data-masked="{{ !empty($maskedSettings['eposnow_api_key']) ? 'true' : 'false' }}">
                                @if(!empty($maskedSettings['eposnow_api_key']))
                                    <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('eposnow_api_key')" title="Change API Key">
                                        <i class="fas fa-edit api-key-change-btn__icon"></i>
                                        Change
                                    </button>
                                @endif
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
                                EPOSNOW API Secret
                            </label>
                            <div class="input-wrapper input-wrapper--with-action">
                                <input type="text"
                                       class="form-input-modern api-key-input @error('eposnow_api_secret') is-invalid @enderror"
                                       id="eposnow_api_secret"
                                       name="eposnow_api_secret"
                                       value="{{ old('eposnow_api_secret', $maskedSettings['eposnow_api_secret'] ?? '') }}"
                                       placeholder="Enter EPOSNOW API Secret"
                                       {{ !empty($maskedSettings['eposnow_api_secret']) ? 'readonly' : '' }}
                                       data-masked="{{ !empty($maskedSettings['eposnow_api_secret']) ? 'true' : 'false' }}">
                                @if(!empty($maskedSettings['eposnow_api_secret']))
                                    <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('eposnow_api_secret')" title="Change API Secret">
                                        <i class="fas fa-edit api-key-change-btn__icon"></i>
                                        Change
                                    </button>
                                @endif
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
                                EPOSNOW API Base URL
                            </label>
                            <div class="input-wrapper">
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
                                Stripe Publishable Key
                            </label>
                            <div class="input-wrapper input-wrapper--with-action">
                                <input type="text"
                                       class="form-input-modern api-key-input @error('stripe_key') is-invalid @enderror"
                                       id="stripe_key"
                                       name="stripe_key"
                                       value="{{ old('stripe_key', $maskedSettings['stripe_key'] ?? '') }}"
                                       placeholder="pk_test_... or pk_live_..."
                                       {{ !empty($maskedSettings['stripe_key']) ? 'readonly' : '' }}
                                       data-masked="{{ !empty($maskedSettings['stripe_key']) ? 'true' : 'false' }}">
                                @if(!empty($maskedSettings['stripe_key']))
                                    <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('stripe_key')" title="Change Publishable Key">
                                        <i class="fas fa-edit api-key-change-btn__icon"></i>
                                        Change
                                    </button>
                                @endif
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
                                Stripe Secret Key
                            </label>
                            <div class="input-wrapper input-wrapper--with-action">
                                <input type="text"
                                       class="form-input-modern api-key-input @error('stripe_secret') is-invalid @enderror"
                                       id="stripe_secret"
                                       name="stripe_secret"
                                       value="{{ old('stripe_secret', $maskedSettings['stripe_secret'] ?? '') }}"
                                       placeholder="sk_test_... or sk_live_..."
                                       {{ !empty($maskedSettings['stripe_secret']) ? 'readonly' : '' }}
                                       data-masked="{{ !empty($maskedSettings['stripe_secret']) ? 'true' : 'false' }}">
                                @if(!empty($maskedSettings['stripe_secret']))
                                    <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('stripe_secret')" title="Change Secret Key">
                                        <i class="fas fa-edit api-key-change-btn__icon"></i>
                                        Change
                                    </button>
                                @endif
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
                                Stripe Webhook Secret
                            </label>
                            <div class="input-wrapper input-wrapper--with-action">
                                <input type="text"
                                       class="form-input-modern api-key-input @error('stripe_webhook_secret') is-invalid @enderror"
                                       id="stripe_webhook_secret"
                                       name="stripe_webhook_secret"
                                       value="{{ old('stripe_webhook_secret', $maskedSettings['stripe_webhook_secret'] ?? '') }}"
                                       placeholder="whsec_..."
                                       {{ !empty($maskedSettings['stripe_webhook_secret']) ? 'readonly' : '' }}
                                       data-masked="{{ !empty($maskedSettings['stripe_webhook_secret']) ? 'true' : 'false' }}">
                                @if(!empty($maskedSettings['stripe_webhook_secret']))
                                    <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('stripe_webhook_secret')" title="Change Webhook Secret">
                                        <i class="fas fa-edit api-key-change-btn__icon"></i>
                                        Change
                                    </button>
                                @endif
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
                                    Google Client ID
                                </label>
                                <div class="input-wrapper input-wrapper--with-action">
                                    <input type="text"
                                           class="form-input-modern api-key-input @error('google_client_id') is-invalid @enderror"
                                           id="google_client_id"
                                           name="google_client_id"
                                           value="{{ old('google_client_id', $maskedSettings['google_client_id'] ?? '') }}"
                                           placeholder="Enter Google Client ID"
                                           {{ !empty($maskedSettings['google_client_id']) ? 'readonly' : '' }}
                                           data-masked="{{ !empty($maskedSettings['google_client_id']) ? 'true' : 'false' }}">
                                    @if(!empty($maskedSettings['google_client_id']))
                                        <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('google_client_id')" title="Change Client ID">
                                            <i class="fas fa-edit"></i>
                                            Change
                                        </button>
                                    @endif
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
                                    Google Client Secret
                                </label>
                                <div class="input-wrapper input-wrapper--with-action">
                                    <input type="text"
                                           class="form-input-modern api-key-input @error('google_client_secret') is-invalid @enderror"
                                           id="google_client_secret"
                                           name="google_client_secret"
                                           value="{{ old('google_client_secret', $maskedSettings['google_client_secret'] ?? '') }}"
                                           placeholder="Enter Google Client Secret"
                                           {{ !empty($maskedSettings['google_client_secret']) ? 'readonly' : '' }}
                                           data-masked="{{ !empty($maskedSettings['google_client_secret']) ? 'true' : 'false' }}">
                                    @if(!empty($maskedSettings['google_client_secret']))
                                        <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('google_client_secret')" title="Change Client Secret">
                                            <i class="fas fa-edit"></i>
                                            Change
                                        </button>
                                    @endif
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
                                    Facebook App ID
                                </label>
                                <div class="input-wrapper input-wrapper--with-action">
                                    <input type="text"
                                           class="form-input-modern api-key-input @error('facebook_client_id') is-invalid @enderror"
                                           id="facebook_client_id"
                                           name="facebook_client_id"
                                           value="{{ old('facebook_client_id', $maskedSettings['facebook_client_id'] ?? '') }}"
                                           placeholder="Enter Facebook App ID"
                                           {{ !empty($maskedSettings['facebook_client_id']) ? 'readonly' : '' }}
                                           data-masked="{{ !empty($maskedSettings['facebook_client_id']) ? 'true' : 'false' }}">
                                    @if(!empty($maskedSettings['facebook_client_id']))
                                        <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('facebook_client_id')" title="Change App ID">
                                            <i class="fas fa-edit"></i>
                                            Change
                                        </button>
                                    @endif
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
                                    Facebook App Secret
                                </label>
                                <div class="input-wrapper input-wrapper--with-action">
                                    <input type="text"
                                           class="form-input-modern api-key-input @error('facebook_client_secret') is-invalid @enderror"
                                           id="facebook_client_secret"
                                           name="facebook_client_secret"
                                           value="{{ old('facebook_client_secret', $maskedSettings['facebook_client_secret'] ?? '') }}"
                                           placeholder="Enter Facebook App Secret"
                                           {{ !empty($maskedSettings['facebook_client_secret']) ? 'readonly' : '' }}
                                           data-masked="{{ !empty($maskedSettings['facebook_client_secret']) ? 'true' : 'false' }}">
                                    @if(!empty($maskedSettings['facebook_client_secret']))
                                        <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('facebook_client_secret')" title="Change App Secret">
                                            <i class="fas fa-edit"></i>
                                            Change
                                        </button>
                                    @endif
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
                                NZ Post API Key
                            </label>
                            <div class="input-wrapper input-wrapper--with-action">
                                <input type="text"
                                       class="form-input-modern api-key-input @error('nzpost_api_key') is-invalid @enderror"
                                       id="nzpost_api_key"
                                       name="nzpost_api_key"
                                       value="{{ old('nzpost_api_key', $maskedSettings['nzpost_api_key'] ?? '') }}"
                                       placeholder="Enter NZ Post API Key"
                                       {{ !empty($maskedSettings['nzpost_api_key']) ? 'readonly' : '' }}
                                       data-masked="{{ !empty($maskedSettings['nzpost_api_key']) ? 'true' : 'false' }}">
                                @if(!empty($maskedSettings['nzpost_api_key']))
                                    <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('nzpost_api_key')" title="Change API Key">
                                        <i class="fas fa-edit api-key-change-btn__icon"></i>
                                        Change
                                    </button>
                                @endif
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
                                Google Maps API Key
                            </label>
                            <div class="input-wrapper input-wrapper--with-action">
                                <input type="text"
                                       class="form-input-modern api-key-input @error('google_map_api_key') is-invalid @enderror"
                                       id="google_map_api_key"
                                       name="google_map_api_key"
                                       value="{{ old('google_map_api_key', $maskedSettings['google_map_api_key'] ?? '') }}"
                                       placeholder="Enter Google Maps API Key"
                                       {{ !empty($maskedSettings['google_map_api_key']) ? 'readonly' : '' }}
                                       data-masked="{{ !empty($maskedSettings['google_map_api_key']) ? 'true' : 'false' }}">
                                @if(!empty($maskedSettings['google_map_api_key']))
                                    <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('google_map_api_key')" title="Change API Key">
                                        <i class="fas fa-edit api-key-change-btn__icon"></i>
                                        Change
                                    </button>
                                @endif
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
                                Instagram App ID
                            </label>
                            <div class="input-wrapper input-wrapper--with-action">
                                <input type="text"
                                       class="form-input-modern api-key-input @error('instagram_app_id') is-invalid @enderror"
                                       id="instagram_app_id"
                                       name="instagram_app_id"
                                       value="{{ old('instagram_app_id', $maskedSettings['instagram_app_id'] ?? '') }}"
                                       placeholder="Enter your Instagram App ID"
                                       {{ !empty($maskedSettings['instagram_app_id']) ? 'readonly' : '' }}
                                       data-masked="{{ !empty($maskedSettings['instagram_app_id']) ? 'true' : 'false' }}">
                                @if(!empty($maskedSettings['instagram_app_id']))
                                    <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('instagram_app_id')" title="Change App ID">
                                        <i class="fas fa-edit api-key-change-btn__icon"></i>
                                        Change
                                    </button>
                                @endif
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
                                Instagram App Secret
                            </label>
                            <div class="input-wrapper input-wrapper--with-action">
                                <input type="text"
                                       class="form-input-modern api-key-input @error('instagram_app_secret') is-invalid @enderror"
                                       id="instagram_app_secret"
                                       name="instagram_app_secret"
                                       value="{{ old('instagram_app_secret', $maskedSettings['instagram_app_secret'] ?? '') }}"
                                       placeholder="Enter your Instagram App Secret"
                                       {{ !empty($maskedSettings['instagram_app_secret']) ? 'readonly' : '' }}
                                       data-masked="{{ !empty($maskedSettings['instagram_app_secret']) ? 'true' : 'false' }}">
                                @if(!empty($maskedSettings['instagram_app_secret']))
                                    <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('instagram_app_secret')" title="Change App Secret">
                                        <i class="fas fa-edit api-key-change-btn__icon"></i>
                                        Change
                                    </button>
                                @endif
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
                                Instagram Access Token
                            </label>
                            <div class="input-wrapper input-wrapper--with-action">
                                <input type="text"
                                       class="form-input-modern api-key-input @error('instagram_access_token') is-invalid @enderror"
                                       id="instagram_access_token"
                                       name="instagram_access_token"
                                       value="{{ old('instagram_access_token', $maskedSettings['instagram_access_token'] ?? '') }}"
                                       placeholder="Enter your Instagram Access Token"
                                       {{ !empty($maskedSettings['instagram_access_token']) ? 'readonly' : '' }}
                                       data-masked="{{ !empty($maskedSettings['instagram_access_token']) ? 'true' : 'false' }}">
                                @if(!empty($maskedSettings['instagram_access_token']))
                                    <button type="button" class="api-key-change-btn" onclick="enableKeyEdit('instagram_access_token')" title="Change Access Token">
                                        <i class="fas fa-edit api-key-change-btn__icon"></i>
                                        Change
                                    </button>
                                @endif
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
                                Instagram User ID
                            </label>
                            <div class="input-wrapper">
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
                                    <strong>API Key Security</strong>
                                    <p>Keys are masked by default showing only first 4 and last 4 characters. Click "Change" button to edit existing keys</p>
                                </div>
                            </li>
                            <li class="tips-list__item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Encrypted Storage</strong>
                                    <p>All sensitive API keys are encrypted in the database using Laravel's encryption</p>
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
                                    @if(!empty($settings['stripe_key']) && !empty($settings['stripe_secret']))
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
                                    @if(!empty($settings['eposnow_api_key']) && !empty($settings['eposnow_api_secret']))
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
                                    @if(!empty($settings['google_client_id']) && !empty($settings['google_client_secret']))
                                        <span class="api-status-badge api-status-badge--success">Configured</span>
                                    @else
                                        <span class="api-status-badge api-status-badge--secondary">Optional</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="api-config-status__item">
                                <div class="api-config-status__label">
                                    <i class="fab fa-instagram api-config-status__label-icon"></i>
                                    Instagram
                                </div>
                                <div class="api-config-status__value">
                                    @if(!empty($settings['instagram_access_token']))
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
                                    @if(!empty($settings['nzpost_api_key']))
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
                                    <p class="api-services-list__description">OAuth login and Google Maps integration for store location</p>
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
/**
 * Enable editing for a masked API key
 */
function enableKeyEdit(inputId) {
    const input = document.getElementById(inputId);
    const button = input.parentElement.querySelector('.api-key-change-btn');
    
    if (!input || !button) return;
    
    // Remove readonly and clear masked value
    input.removeAttribute('readonly');
    input.value = '';
    input.focus();
    input.setAttribute('data-masked', 'false');
    
    // Add visual feedback
    input.classList.add('api-key-input--changed');
    input.placeholder = 'Enter new ' + input.getAttribute('name').replace(/_/g, ' ');
    
    // Hide the change button
    button.style.display = 'none';
    
    // Update background color
    input.style.backgroundColor = '#ffffff';
    input.style.cursor = 'text';
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

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin-api-settings.css') }}">
@endpush
@endsection
