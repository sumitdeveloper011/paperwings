@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-cog"></i>
                    Site Settings
                </h1>
                <p class="page-header__subtitle">Manage your website configuration and preferences</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="settings-form" id="settingsForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Left Column - Main Settings -->
            <div class="col-lg-8">
                <!-- Logo & Icon Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-image"></i>
                            Brand Identity
                        </h3>
                        <p class="modern-card__subtitle">Upload your logo and favicon</p>
                    </div>
                    <div class="modern-card__body">
                        <!-- Logo Upload -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-image"></i>
                                Site Logo
                            </label>
                            <div class="file-upload-wrapper">
                                <div class="file-upload-area" id="logoUploadArea">
                                    <input type="file"
                                           name="logo"
                                           id="logoInput"
                                           class="file-input"
                                           accept="image/*"
                                           onchange="previewImage(this, 'logoPreview')">
                                    <div class="file-upload-content">
                                        <div class="file-upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <p class="file-upload-text">
                                            <span class="file-upload-highlight">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="file-upload-hint">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                    <div class="file-preview" id="logoPreview" style="display: none;">
                                        <img src="" alt="Logo Preview" class="preview-image">
                                        <button type="button" class="preview-remove" onclick="removePreview('logoInput', 'logoPreview')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @if(!empty($settings['logo']))
                                <div class="current-image">
                                    <p class="current-image-label">Current Logo:</p>
                                    <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Current Logo" class="current-image-preview">
                                </div>
                                @endif
                            </div>
                            @error('logo')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Icon Upload -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-favicon"></i>
                                Site Icon (Favicon)
                            </label>
                            <div class="file-upload-wrapper">
                                <div class="file-upload-area file-upload-area--small" id="iconUploadArea">
                                    <input type="file"
                                           name="icon"
                                           id="iconInput"
                                           class="file-input"
                                           accept="image/*"
                                           onchange="previewImage(this, 'iconPreview')">
                                    <div class="file-upload-content">
                                        <div class="file-upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <p class="file-upload-text">
                                            <span class="file-upload-highlight">Click to upload</span> favicon
                                        </p>
                                        <p class="file-upload-hint">ICO, PNG up to 500KB (Recommended: 32x32 or 16x16)</p>
                                    </div>
                                    <div class="file-preview file-preview--small" id="iconPreview" style="display: none;">
                                        <img src="" alt="Icon Preview" class="preview-image">
                                        <button type="button" class="preview-remove" onclick="removePreview('iconInput', 'iconPreview')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @if(!empty($settings['icon']))
                                <div class="current-image">
                                    <p class="current-image-label">Current Icon:</p>
                                    <img src="{{ asset('storage/' . $settings['icon']) }}" alt="Current Icon" class="current-image-preview current-image-preview--small">
                                </div>
                                @endif
                            </div>
                            @error('icon')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Meta Tags Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-tags"></i>
                            Meta Tags <span class="badge badge-warning">Important</span>
                        </h3>
                        <p class="modern-card__subtitle">SEO and social media meta information</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-group-modern">
                            <label for="meta_title" class="form-label-modern">
                                <i class="fas fa-heading"></i>
                                Meta Title
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-heading input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('meta_title') is-invalid @enderror"
                                       id="meta_title"
                                       name="meta_title"
                                       value="{{ old('meta_title', $settings['meta_title'] ?? '') }}"
                                       placeholder="Enter meta title (50-60 characters recommended)"
                                       maxlength="60">
                                <div class="char-counter">
                                    <span id="metaTitleCounter">0</span>/60
                                </div>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                The title that appears in search engine results and browser tabs
                            </div>
                            @error('meta_title')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="meta_description" class="form-label-modern">
                                <i class="fas fa-align-left"></i>
                                Meta Description
                            </label>
                            <div class="input-wrapper">
                                <textarea class="form-input-modern @error('meta_description') is-invalid @enderror"
                                          id="meta_description"
                                          name="meta_description"
                                          rows="4"
                                          placeholder="Enter meta description (150-160 characters recommended)"
                                          maxlength="160">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                                <div class="char-counter">
                                    <span id="metaDescCounter">0</span>/160
                                </div>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Brief description that appears in search engine results
                            </div>
                            @error('meta_description')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="meta_keywords" class="form-label-modern">
                                <i class="fas fa-key"></i>
                                Meta Keywords
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('meta_keywords') is-invalid @enderror"
                                       id="meta_keywords"
                                       name="meta_keywords"
                                       value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}"
                                       placeholder="keyword1, keyword2, keyword3">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Comma-separated keywords for SEO (optional but recommended)
                            </div>
                            @error('meta_keywords')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="meta_author" class="form-label-modern">
                                <i class="fas fa-user"></i>
                                Meta Author
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('meta_author') is-invalid @enderror"
                                       id="meta_author"
                                       name="meta_author"
                                       value="{{ old('meta_author', $settings['meta_author'] ?? '') }}"
                                       placeholder="Author name or company name">
                            </div>
                            @error('meta_author')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Info Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-address-book"></i>
                            Contact Information
                        </h3>
                        <p class="modern-card__subtitle">Manage your business contact details</p>
                    </div>
                    <div class="modern-card__body">
                        <!-- Address -->
                        <div class="form-group-modern">
                            <label for="address" class="form-label-modern">
                                <i class="fas fa-map-marker-alt"></i>
                                Address
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <textarea class="form-input-modern @error('address') is-invalid @enderror"
                                          id="address"
                                          name="address"
                                          rows="3"
                                          placeholder="Enter your business address">{{ old('address', $settings['address'] ?? '') }}</textarea>
                            </div>
                            @error('address')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Google Map -->
                        <div class="form-group-modern">
                            <label for="google_map" class="form-label-modern">
                                <i class="fas fa-map"></i>
                                Google Map Embed
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-map input-icon"></i>
                                <textarea class="form-input-modern @error('google_map') is-invalid @enderror"
                                          id="google_map"
                                          name="google_map"
                                          rows="4"
                                          placeholder="Paste Google Maps embed code or iframe URL here">{{ old('google_map', $settings['google_map'] ?? '') }}</textarea>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Paste your Google Maps embed code or iframe URL. You can get this from Google Maps by clicking "Share" → "Embed a map"
                            </div>
                            @error('google_map')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            @if(!empty($settings['google_map']))
                            <div class="form-hint" style="margin-top: 1rem;">
                                <strong>Current Map Preview:</strong>
                                <div style="margin-top: 0.5rem; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
                                    {!! $settings['google_map'] !!}
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Google Map API Key -->
                        <div class="form-group-modern">
                            <label for="google_map_api_key" class="form-label-modern">
                                <i class="fas fa-key"></i>
                                Google Map API Key
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('google_map_api_key') is-invalid @enderror"
                                       id="google_map_api_key"
                                       name="google_map_api_key"
                                       value="{{ old('google_map_api_key', $settings['google_map_api_key'] ?? '') }}"
                                       placeholder="Enter Google Maps API Key">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Optional: Enter your Google Maps API Key if you want to use dynamic map instead of embed code. Get your API key from <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>
                            </div>
                            @error('google_map_api_key')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Email Repeater -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-envelope"></i>
                                Email Addresses
                            </label>
                            <div class="repeater-wrapper" id="emailRepeater">
                                <div class="repeater-items">
                                    @if(!empty($settings['emails']) && is_array($settings['emails']) && count($settings['emails']) > 0)
                                        @foreach($settings['emails'] as $index => $email)
                                        <div class="repeater-item" data-index="{{ $index }}">
                                            <div class="input-wrapper">
                                                <i class="fas fa-envelope input-icon"></i>
                                                <input type="email"
                                                       class="form-input-modern"
                                                       name="emails[]"
                                                       value="{{ $email }}"
                                                       placeholder="Enter email address">
                                                <button type="button" class="repeater-remove" onclick="removeRepeaterItem(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="repeater-item" data-index="0">
                                            <div class="input-wrapper">
                                                <i class="fas fa-envelope input-icon"></i>
                                                <input type="email"
                                                       class="form-input-modern"
                                                       name="emails[]"
                                                       value="{{ old('emails.0', '') }}"
                                                       placeholder="Enter email address">
                                                <button type="button" class="repeater-remove" onclick="removeRepeaterItem(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm repeater-add" onclick="addRepeaterItem('emailRepeater', 'email')">
                                    <i class="fas fa-plus"></i>
                                    Add Email
                                </button>
                            </div>
                        </div>

                        <!-- Phone Repeater -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-phone"></i>
                                Phone Numbers
                            </label>
                            <div class="repeater-wrapper" id="phoneRepeater">
                                <div class="repeater-items">
                                    @if(!empty($settings['phones']) && is_array($settings['phones']) && count($settings['phones']) > 0)
                                        @foreach($settings['phones'] as $index => $phone)
                                        <div class="repeater-item" data-index="{{ $index }}">
                                            <div class="input-wrapper">
                                                <i class="fas fa-phone input-icon"></i>
                                                <input type="tel"
                                                       class="form-input-modern"
                                                       name="phones[]"
                                                       value="{{ $phone }}"
                                                       placeholder="Enter phone number">
                                                <button type="button" class="repeater-remove" onclick="removeRepeaterItem(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="repeater-item" data-index="0">
                                            <div class="input-wrapper">
                                                <i class="fas fa-phone input-icon"></i>
                                                <input type="tel"
                                                       class="form-input-modern"
                                                       name="phones[]"
                                                       value="{{ old('phones.0', '') }}"
                                                       placeholder="Enter phone number">
                                                <button type="button" class="repeater-remove" onclick="removeRepeaterItem(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm repeater-add" onclick="addRepeaterItem('phoneRepeater', 'phone')">
                                    <i class="fas fa-plus"></i>
                                    Add Phone
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Links Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-share-alt"></i>
                            Social Media Links
                        </h3>
                        <p class="modern-card__subtitle">Connect your social media profiles</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="social-links-grid">
                            <!-- Facebook -->
                            <div class="form-group-modern">
                                <label for="social_facebook" class="form-label-modern">
                                    <i class="fab fa-facebook-f"></i>
                                    Facebook
                                </label>
                                <div class="input-wrapper">
                                    <i class="fab fa-facebook-f input-icon"></i>
                                    <input type="url"
                                           class="form-input-modern @error('social_facebook') is-invalid @enderror"
                                           id="social_facebook"
                                           name="social_facebook"
                                           value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
                                           placeholder="https://facebook.com/yourpage">
                                </div>
                                @error('social_facebook')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Twitter -->
                            <div class="form-group-modern">
                                <label for="social_twitter" class="form-label-modern">
                                    <i class="fab fa-twitter"></i>
                                    Twitter
                                </label>
                                <div class="input-wrapper">
                                    <i class="fab fa-twitter input-icon"></i>
                                    <input type="url"
                                           class="form-input-modern @error('social_twitter') is-invalid @enderror"
                                           id="social_twitter"
                                           name="social_twitter"
                                           value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}"
                                           placeholder="https://twitter.com/yourhandle">
                                </div>
                                @error('social_twitter')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Instagram -->
                            <div class="form-group-modern">
                                <label for="social_instagram" class="form-label-modern">
                                    <i class="fab fa-instagram"></i>
                                    Instagram
                                </label>
                                <div class="input-wrapper">
                                    <i class="fab fa-instagram input-icon"></i>
                                    <input type="url"
                                           class="form-input-modern @error('social_instagram') is-invalid @enderror"
                                           id="social_instagram"
                                           name="social_instagram"
                                           value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}"
                                           placeholder="https://instagram.com/yourprofile">
                                </div>
                                @error('social_instagram')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- LinkedIn -->
                            <div class="form-group-modern">
                                <label for="social_linkedin" class="form-label-modern">
                                    <i class="fab fa-linkedin-in"></i>
                                    LinkedIn
                                </label>
                                <div class="input-wrapper">
                                    <i class="fab fa-linkedin-in input-icon"></i>
                                    <input type="url"
                                           class="form-input-modern @error('social_linkedin') is-invalid @enderror"
                                           id="social_linkedin"
                                           name="social_linkedin"
                                           value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}"
                                           placeholder="https://linkedin.com/company/yourcompany">
                                </div>
                                @error('social_linkedin')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- YouTube -->
                            <div class="form-group-modern">
                                <label for="social_youtube" class="form-label-modern">
                                    <i class="fab fa-youtube"></i>
                                    YouTube
                                </label>
                                <div class="input-wrapper">
                                    <i class="fab fa-youtube input-icon"></i>
                                    <input type="url"
                                           class="form-input-modern @error('social_youtube') is-invalid @enderror"
                                           id="social_youtube"
                                           name="social_youtube"
                                           value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}"
                                           placeholder="https://youtube.com/yourchannel">
                                </div>
                                @error('social_youtube')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Pinterest -->
                            <div class="form-group-modern">
                                <label for="social_pinterest" class="form-label-modern">
                                    <i class="fab fa-pinterest"></i>
                                    Pinterest
                                </label>
                                <div class="input-wrapper">
                                    <i class="fab fa-pinterest input-icon"></i>
                                    <input type="url"
                                           class="form-input-modern @error('social_pinterest') is-invalid @enderror"
                                           id="social_pinterest"
                                           name="social_pinterest"
                                           value="{{ old('social_pinterest', $settings['social_pinterest'] ?? '') }}"
                                           placeholder="https://pinterest.com/yourprofile">
                                </div>
                                @error('social_pinterest')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
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
                        <div class="form-hint" style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
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
                                <input type="text"
                                       class="form-input-modern @error('instagram_app_id') is-invalid @enderror"
                                       id="instagram_app_id"
                                       name="instagram_app_id"
                                       value="{{ old('instagram_app_id', $settings['instagram_app_id'] ?? '') }}"
                                       placeholder="Enter your Instagram App ID">
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
                                       value="{{ old('instagram_app_secret', $settings['instagram_app_secret'] ?? '') }}"
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
                                       value="{{ old('instagram_access_token', $settings['instagram_access_token'] ?? '') }}"
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
                                       value="{{ old('instagram_user_id', $settings['instagram_user_id'] ?? '') }}"
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

                <!-- API Keys & Private Keys Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-key"></i>
                            API Keys & Private Keys <span class="badge badge-warning">Sensitive</span>
                        </h3>
                        <p class="modern-card__subtitle">Manage your API keys and private credentials securely</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-hint" style="margin-bottom: 1.5rem; padding: 1rem; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                            <strong><i class="fas fa-exclamation-triangle"></i> Security Notice:</strong>
                            <p style="margin: 0.5rem 0 0 0;">These keys are sensitive information. Store them securely and never share them publicly.</p>
                        </div>

                        <!-- Private Key 1 -->
                        <div class="form-group-modern">
                            <label for="private_key_1" class="form-label-modern">
                                <i class="fas fa-lock"></i>
                                Private Key 1
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('private_key_1') is-invalid @enderror"
                                       id="private_key_1"
                                       name="private_key_1"
                                       value="{{ old('private_key_1', $settings['private_key_1'] ?? '') }}"
                                       placeholder="Enter private key 1">
                                <button type="button" class="password-toggle" onclick="togglePassword('private_key_1')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_private_key_1"></i>
                                </button>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Enter your first private key or API secret
                            </div>
                            @error('private_key_1')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Private Key 2 -->
                        <div class="form-group-modern">
                            <label for="private_key_2" class="form-label-modern">
                                <i class="fas fa-lock"></i>
                                Private Key 2
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('private_key_2') is-invalid @enderror"
                                       id="private_key_2"
                                       name="private_key_2"
                                       value="{{ old('private_key_2', $settings['private_key_2'] ?? '') }}"
                                       placeholder="Enter private key 2">
                                <button type="button" class="password-toggle" onclick="togglePassword('private_key_2')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_private_key_2"></i>
                                </button>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Enter your second private key or API secret
                            </div>
                            @error('private_key_2')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Private Key 3 -->
                        <div class="form-group-modern">
                            <label for="private_key_3" class="form-label-modern">
                                <i class="fas fa-lock"></i>
                                Private Key 3
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password"
                                       class="form-input-modern @error('private_key_3') is-invalid @enderror"
                                       id="private_key_3"
                                       name="private_key_3"
                                       value="{{ old('private_key_3', $settings['private_key_3'] ?? '') }}"
                                       placeholder="Enter private key 3">
                                <button type="button" class="password-toggle" onclick="togglePassword('private_key_3')" title="Show/Hide">
                                    <i class="fas fa-eye" id="toggle_private_key_3"></i>
                                </button>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Enter your third private key or API secret
                            </div>
                            @error('private_key_3')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Social Login Configuration Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-sign-in-alt"></i>
                            Social Login Configuration
                        </h3>
                        <p class="modern-card__subtitle">Configure Google and Facebook OAuth for social login</p>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-hint" style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
                            <strong><i class="fas fa-info-circle"></i> Setup Instructions:</strong>
                            <ol style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                                <li>Create OAuth applications in Google Cloud Console and Facebook Developers</li>
                                <li>Get your Client ID and Client Secret from each platform</li>
                                <li>Enter the credentials below and enable the providers you want to use</li>
                                <li>Make sure to set the correct redirect URIs in your OAuth apps</li>
                            </ol>
                        </div>

                        <!-- Google OAuth Configuration -->
                        <div class="social-provider-section" style="margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">
                                    <i class="fab fa-google" style="color: #4285F4;"></i>
                                    Google OAuth
                                </h4>
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
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        Found in Google Cloud Console → APIs & Services → Credentials
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
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        Found in Google Cloud Console → APIs & Services → Credentials
                                    </div>
                                    @error('google_client_secret')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Google Redirect URI Info -->
                                <div class="form-hint" style="background: #e7f3ff; padding: 0.75rem; border-radius: 4px;">
                                    <strong>Redirect URI:</strong> <code>{{ env('APP_URL') }}/auth/google/callback</code>
                                    <br>
                                    <small>Make sure this URI is added to your Google OAuth app's authorized redirect URIs.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Facebook OAuth Configuration -->
                        <div class="social-provider-section" style="padding: 1.5rem; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">
                                    <i class="fab fa-facebook-f" style="color: #1877F2;"></i>
                                    Facebook OAuth
                                </h4>
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
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        Found in Facebook Developers → Your App → Settings → Basic
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
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        Found in Facebook Developers → Your App → Settings → Basic
                                    </div>
                                    @error('facebook_client_secret')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Facebook Redirect URI Info -->
                                <div class="form-hint" style="background: #e7f3ff; padding: 0.75rem; border-radius: 4px;">
                                    <strong>Redirect URI:</strong> <code>{{ env('APP_URL') }}/auth/facebook/callback</code>
                                    <br>
                                    <small>Make sure this URI is added to your Facebook App's Valid OAuth Redirect URIs.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Current Status Display -->
                        <div class="form-group-modern" style="margin-top: 2rem;">
                            <div class="ga-status-box">
                                <h4 class="ga-status-box__title">
                                    <i class="fas fa-info-circle"></i>
                                    Current Social Login Status
                                </h4>
                                <div class="ga-status-box__content">
                                    <div class="ga-status-item">
                                        <span class="ga-status-label">
                                            <i class="fab fa-google"></i>
                                            Google Login:
                                        </span>
                                        <span class="ga-status-value">
                                            @if(isset($settings['google_login_enabled']) && $settings['google_login_enabled'] == '1')
                                                <span class="status-badge status-badge--success">Enabled</span>
                                            @else
                                                <span class="status-badge status-badge--warning">Disabled</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="ga-status-item">
                                        <span class="ga-status-label">
                                            <i class="fab fa-facebook-f"></i>
                                            Facebook Login:
                                        </span>
                                        <span class="ga-status-value">
                                            @if(isset($settings['facebook_login_enabled']) && $settings['facebook_login_enabled'] == '1')
                                                <span class="status-badge status-badge--success">Enabled</span>
                                            @else
                                                <span class="status-badge status-badge--warning">Disabled</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Analytics Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fab fa-google"></i>
                            Google Analytics
                        </h3>
                        <p class="modern-card__subtitle">Track your website visitors and e-commerce performance</p>
                    </div>
                    <div class="modern-card__body">
                        <!-- Google Analytics ID (GA4) -->
                        <div class="form-group-modern">
                            <label for="google_analytics_id" class="form-label-modern">
                                <i class="fas fa-chart-line"></i>
                                Google Analytics Measurement ID (GA4)
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-chart-line input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('google_analytics_id') is-invalid @enderror"
                                       id="google_analytics_id"
                                       name="google_analytics_id"
                                       value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}"
                                       placeholder="G-XXXXXXXXXX">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Your Google Analytics 4 Measurement ID (starts with "G-"). Find it in Admin → Data Streams → Your Stream → Measurement ID
                            </div>
                            @error('google_analytics_id')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Google Analytics Status -->
                        <div class="form-group-modern">
                            <div class="form-check-modern">
                                <input type="checkbox"
                                       class="form-check-input-modern"
                                       id="google_analytics_enabled"
                                       name="google_analytics_enabled"
                                       value="1"
                                       {{ old('google_analytics_enabled', $settings['google_analytics_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label-modern" for="google_analytics_enabled">
                                    <i class="fas fa-toggle-on"></i>
                                    Enable Google Analytics Tracking
                                </label>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                When enabled, Google Analytics will track page views, events, and e-commerce transactions on your website.
                            </div>
                        </div>

                        <!-- E-commerce Tracking -->
                        <div class="form-group-modern">
                            <div class="form-check-modern">
                                <input type="checkbox"
                                       class="form-check-input-modern"
                                       id="google_analytics_ecommerce"
                                       name="google_analytics_ecommerce"
                                       value="1"
                                       {{ old('google_analytics_ecommerce', $settings['google_analytics_ecommerce'] ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label-modern" for="google_analytics_ecommerce">
                                    <i class="fas fa-shopping-cart"></i>
                                    Enable E-commerce Tracking
                                </label>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Track purchase events, revenue, and product performance. Requires Enhanced E-commerce to be enabled in your GA4 property.
                            </div>
                        </div>

                        <!-- Current Status Display -->
                        <div class="form-group-modern">
                            <div class="ga-status-box">
                                <h4 class="ga-status-box__title">
                                    <i class="fas fa-info-circle"></i>
                                    Current Configuration Status
                                </h4>
                                <div class="ga-status-box__content">
                                    <div class="ga-status-item">
                                        <span class="ga-status-label">
                                            <i class="fas fa-fingerprint"></i>
                                            Measurement ID:
                                        </span>
                                        <span class="ga-status-value">
                                            @if(!empty($settings['google_analytics_id']))
                                                <code>{{ $settings['google_analytics_id'] }}</code>
                                                <span class="status-badge status-badge--success">Configured</span>
                                            @else
                                                <span class="text-muted">Not Set</span>
                                                <span class="status-badge status-badge--danger">Not Configured</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="ga-status-item">
                                        <span class="ga-status-label">
                                            <i class="fas fa-toggle-on"></i>
                                            Tracking Status:
                                        </span>
                                        <span class="ga-status-value">
                                            @if(isset($settings['google_analytics_enabled']) && $settings['google_analytics_enabled'] == '1')
                                                <span class="status-badge status-badge--success">Active</span>
                                            @else
                                                <span class="status-badge status-badge--warning">Inactive</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="ga-status-item">
                                        <span class="ga-status-label">
                                            <i class="fas fa-shopping-cart"></i>
                                            E-commerce Tracking:
                                        </span>
                                        <span class="ga-status-value">
                                            @if(isset($settings['google_analytics_ecommerce']) && $settings['google_analytics_ecommerce'] == '1')
                                                <span class="status-badge status-badge--success">Enabled</span>
                                            @else
                                                <span class="status-badge status-badge--secondary">Disabled</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if(!empty($settings['google_analytics_id']) && isset($settings['google_analytics_enabled']) && $settings['google_analytics_enabled'] == '1')
                                    <div class="ga-status-item">
                                        <span class="ga-status-label">
                                            <i class="fas fa-external-link-alt"></i>
                                            View Analytics:
                                        </span>
                                        <span class="ga-status-value">
                                            <a href="https://analytics.google.com" target="_blank" class="ga-link">
                                                Open Google Analytics Dashboard
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Setup Instructions -->
                        <div class="form-group-modern">
                            <div class="info-box">
                                <h4 class="info-box__title">
                                    <i class="fas fa-book"></i>
                                    Setup Instructions
                                </h4>
                                <ol class="info-box__list">
                                    <li>Go to <a href="https://analytics.google.com" target="_blank">Google Analytics</a> and create a GA4 property (if you don't have one)</li>
                                    <li>Navigate to Admin → Data Streams → Web → Your Stream</li>
                                    <li>Copy your Measurement ID (format: G-XXXXXXXXXX)</li>
                                    <li>Paste it in the field above and enable tracking</li>
                                    <li>Enable Enhanced E-commerce in your GA4 property settings for purchase tracking</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Settings Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-window-restore"></i>
                            Footer Settings
                        </h3>
                        <p class="modern-card__subtitle">Customize your website footer content</p>
                    </div>
                    <div class="modern-card__body">
                        <!-- Footer Tagline -->
                        <div class="form-group-modern">
                            <label for="footer_tagline" class="form-label-modern">
                                <i class="fas fa-quote-left"></i>
                                Footer Tagline/Description
                            </label>
                            <div class="input-wrapper">
                                <textarea class="form-input-modern @error('footer_tagline') is-invalid @enderror"
                                          id="footer_tagline"
                                          name="footer_tagline"
                                          rows="3"
                                          placeholder="Enter footer tagline or description">{{ old('footer_tagline', $settings['footer_tagline'] ?? '') }}</textarea>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                This text appears below the logo in the footer. If empty, meta description will be used.
                            </div>
                            @error('footer_tagline')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Working Hours -->
                        <div class="form-group-modern">
                            <label for="working_hours" class="form-label-modern">
                                <i class="fas fa-clock"></i>
                                Working Hours
                            </label>
                            <div class="input-wrapper">
                                <textarea class="form-input-modern @error('working_hours') is-invalid @enderror"
                                          id="working_hours"
                                          name="working_hours"
                                          rows="3"
                                          placeholder="Monday - Friday: 9:00-20:00&#10;Saturday: 11:00 - 15:00">{{ old('working_hours', $settings['working_hours'] ?? 'Monday - Friday: 9:00-20:00' . "\n" . 'Saturday: 11:00 - 15:00') }}</textarea>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Enter your business working hours. You can use line breaks for multiple lines.
                            </div>
                            @error('working_hours')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Copyright Text -->
                        <div class="form-group-modern">
                            <label for="copyright_text" class="form-label-modern">
                                <i class="fas fa-copyright"></i>
                                Copyright Text
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-copyright input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('copyright_text') is-invalid @enderror"
                                       id="copyright_text"
                                       name="copyright_text"
                                       value="{{ old('copyright_text', $settings['copyright_text'] ?? 'Copyright © ' . date('Y') . ' Paper Wings. All rights reserved.') }}"
                                       placeholder="Copyright © {{ date('Y') }} Paper Wings. All rights reserved.">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Copyright text displayed at the bottom of the footer. Use {YEAR} to auto-insert current year.
                            </div>
                            @error('copyright_text')
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
                                Save All Settings
                            </button>
                            <button type="reset" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-undo"></i>
                                Reset Changes
                            </button>
                        </div>
                        <div class="form-info">
                            <div class="info-item">
                                <i class="fas fa-info-circle"></i>
                                <span>Changes will be applied immediately after saving</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-lightbulb"></i>
                            Quick Tips
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <ul class="tips-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Meta tags are crucial for SEO. Keep them relevant and concise.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Logo should be high quality and optimized for web.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Favicon should be 32x32 or 16x16 pixels for best results.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Keep contact information up to date for better customer service.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<script>
// Image Preview Function
function previewImage(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    const uploadContent = preview.parentElement.querySelector('.file-upload-content');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.querySelector('img').src = e.target.result;
            preview.style.display = 'flex';
            if (uploadContent) {
                uploadContent.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    }
}

// Remove Preview Function
function removePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const uploadContent = preview.parentElement.querySelector('.file-upload-content');

    input.value = '';
    preview.style.display = 'none';
    if (uploadContent) {
        uploadContent.style.display = 'block';
    }
}

// Repeater Functions
function addRepeaterItem(repeaterId, type) {
    const repeater = document.getElementById(repeaterId);
    const items = repeater.querySelector('.repeater-items');
    const existingItems = items.querySelectorAll('.repeater-item');
    const newIndex = existingItems.length;

    const iconClass = type === 'email' ? 'fa-envelope' : 'fa-phone';
    const inputType = type === 'email' ? 'email' : 'tel';
    const placeholder = type === 'email' ? 'Enter email address' : 'Enter phone number';

    const newItem = document.createElement('div');
    newItem.className = 'repeater-item';
    newItem.setAttribute('data-index', newIndex);
    newItem.innerHTML = `
        <div class="input-wrapper">
            <i class="fas ${iconClass} input-icon"></i>
            <input type="${inputType}"
                   class="form-input-modern"
                   name="${type}s[]"
                   value=""
                   placeholder="${placeholder}">
            <button type="button" class="repeater-remove" onclick="removeRepeaterItem(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    items.appendChild(newItem);

    // Show remove buttons if more than one item
    if (existingItems.length > 0) {
        existingItems.forEach(item => {
            const removeBtn = item.querySelector('.repeater-remove');
            if (removeBtn) removeBtn.style.display = 'flex';
        });
    }
}

function removeRepeaterItem(button) {
    const item = button.closest('.repeater-item');
    const items = item.parentElement;

    if (items.querySelectorAll('.repeater-item').length > 1) {
        item.remove();
    }
}

// Character Counter and Quick Tips Position
document.addEventListener('DOMContentLoaded', function() {
    // Calculate Quick Tips sticky position based on Save Settings card height
    const saveCard = document.querySelector('.modern-card--sticky');
    const quickTipsCard = saveCard ? saveCard.nextElementSibling : null;

    if (saveCard && quickTipsCard && quickTipsCard.classList.contains('modern-card')) {
        function updateQuickTipsPosition() {
            const saveCardHeight = saveCard.offsetHeight;
            const saveCardTop = 100; // top position of sticky Save Settings card
            const margin = 24; // 1.5rem = 24px
            const quickTipsTop = saveCardTop + saveCardHeight + margin;

            quickTipsCard.style.top = quickTipsTop + 'px';
        }

        // Update on load
        updateQuickTipsPosition();

        // Update on window resize
        window.addEventListener('resize', updateQuickTipsPosition);

        // Update when content changes (e.g., if form expands)
        const observer = new ResizeObserver(updateQuickTipsPosition);
        observer.observe(saveCard);
    }

    const metaTitle = document.getElementById('meta_title');
    const metaDesc = document.getElementById('meta_description');
    const metaTitleCounter = document.getElementById('metaTitleCounter');
    const metaDescCounter = document.getElementById('metaDescCounter');

    if (metaTitle && metaTitleCounter) {
        metaTitleCounter.textContent = metaTitle.value.length;
        metaTitle.addEventListener('input', function() {
            metaTitleCounter.textContent = this.value.length;
        });
    }

    if (metaDesc && metaDescCounter) {
        metaDescCounter.textContent = metaDesc.value.length;
        metaDesc.addEventListener('input', function() {
            metaDescCounter.textContent = this.value.length;
        });
    }

    // Drag and drop for file uploads
    const uploadAreas = document.querySelectorAll('.file-upload-area');
    uploadAreas.forEach(area => {
        const input = area.querySelector('.file-input');

        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            area.style.borderColor = 'var(--primary-color)';
            area.style.background = 'rgba(55, 78, 148, 0.05)';
        });

        area.addEventListener('dragleave', function(e) {
            e.preventDefault();
            area.style.borderColor = 'var(--border-color)';
            area.style.background = 'var(--background-color)';
        });

        area.addEventListener('drop', function(e) {
            e.preventDefault();
            area.style.borderColor = 'var(--border-color)';
            area.style.background = 'var(--background-color)';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                const previewId = input.id === 'logoInput' ? 'logoPreview' : 'iconPreview';
                previewImage(input, previewId);
            }
        });
    });

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
                    input.required = false; // Make optional
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

    // Test Instagram Connection
    document.getElementById('testInstagramConnection')?.addEventListener('click', function() {
        const button = this;
        const resultDiv = document.getElementById('instagramTestResult');
        const originalText = button.innerHTML;

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<div class="form-hint"><i class="fas fa-spinner fa-spin"></i> Testing connection...</div>';

        // Get form data
        const formData = new FormData(document.getElementById('settingsForm'));
        const data = {
            instagram_app_id: formData.get('instagram_app_id'),
            instagram_app_secret: formData.get('instagram_app_secret'),
            instagram_access_token: formData.get('instagram_access_token'),
            instagram_user_id: formData.get('instagram_user_id'),
        };

        fetch('{{ route("admin.settings.test-instagram") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = `<div class="form-hint" style="color: #28a745; background: #d4edda; padding: 1rem; border-radius: 8px; border-left: 4px solid #28a745;">
                    <strong><i class="fas fa-check-circle"></i> Connection Successful!</strong><br>
                    Username: ${data.data.username || 'N/A'}<br>
                    Account Type: ${data.data.account_type || 'N/A'}<br>
                    Media Count: ${data.data.media_count || 0}
                </div>`;
            } else {
                resultDiv.innerHTML = `<div class="form-error" style="padding: 1rem; border-radius: 8px;">
                    <i class="fas fa-exclamation-circle"></i> ${data.message || 'Connection failed. Please check your credentials.'}
                </div>`;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `<div class="form-error" style="padding: 1rem; border-radius: 8px;">
                <i class="fas fa-exclamation-circle"></i> Error: ${error.message}
            </div>`;
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        });
    });
});
</script>
@endsection

