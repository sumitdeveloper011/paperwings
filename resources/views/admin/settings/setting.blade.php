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

                            <x-image-requirements type="logo" />

                            @if(!empty($settings['logo']))
                            <div class="current-image mb-3">
                                <p class="current-image-label">Current Logo:</p>
                                <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Current Logo" class="current-image-preview">
                            </div>
                            @endif

                            <div class="file-upload-wrapper">
                                <input type="file"
                                       class="file-upload-input @error('logo') is-invalid @enderror"
                                       id="logoInput"
                                       name="logo"
                                       accept="image/jpeg, image/png, image/jpg, image/gif, image/webp">
                                <label for="logoInput" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Choose Logo</span>
                                </label>
                            </div>
                            @error('logo')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror

                            <x-image-preview inputId="logoInput" previewId="logoPreview" previewImgId="logoPreviewImg" />
                        </div>

                        <!-- Icon Upload -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-favicon"></i>
                                Site Icon (Favicon)
                            </label>

                            <x-image-requirements type="icon" />

                            @if(!empty($settings['icon']))
                            <div class="current-image mb-3">
                                <p class="current-image-label">Current Icon:</p>
                                <img src="{{ asset('storage/' . $settings['icon']) }}" alt="Current Icon" class="current-image-preview current-image-preview--small">
                            </div>
                            @endif

                            <div class="file-upload-wrapper">
                                <input type="file"
                                       class="file-upload-input @error('icon') is-invalid @enderror"
                                       id="iconInput"
                                       name="icon"
                                       accept="image/jpeg, image/png, image/jpg, image/gif, image/webp, image/x-icon">
                                <label for="iconInput" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Choose Icon</span>
                                </label>
                            </div>
                            @error('icon')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror

                            <x-image-preview inputId="iconInput" previewId="iconPreview" previewImgId="iconPreviewImg" />
                        </div>

                        <!-- Breadcrumb Image Upload -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-image"></i>
                                Breadcrumb Background Image
                            </label>

                            <x-image-requirements type="breadcrumb" />

                            @if(!empty($settings['breadcrumb_image']))
                            <div class="current-image mb-3">
                                <p class="current-image-label">Current Breadcrumb Image:</p>
                                <img src="{{ asset('storage/' . $settings['breadcrumb_image']) }}" alt="Current Breadcrumb Image" class="current-image-preview">
                            </div>
                            @endif

                            <div class="file-upload-wrapper">
                                <input type="file"
                                       class="file-upload-input @error('breadcrumb_image') is-invalid @enderror"
                                       id="breadcrumbImageInput"
                                       name="breadcrumb_image"
                                       accept="image/jpeg, image/png, image/jpg, image/gif, image/webp">
                                <label for="breadcrumbImageInput" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Choose Breadcrumb Image</span>
                                </label>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                This image will be displayed as background on page headers/breadcrumbs across the site
                            </div>
                            @error('breadcrumb_image')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror

                            <x-image-preview inputId="breadcrumbImageInput" previewId="breadcrumbImagePreview" previewImgId="breadcrumbImagePreviewImg" />
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

                <!-- Cookie Consent Settings Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-cookie-bite"></i>
                            Cookie Consent Settings
                        </h3>
                        <p class="modern-card__subtitle">Configure cookie consent banner and preferences</p>
                    </div>
                    <div class="modern-card__body">
                        <!-- Enable Cookie Consent -->
                        <div class="form-group-modern">
                            <div class="form-check-modern">
                                <input type="checkbox"
                                       class="form-check-input-modern"
                                       id="cookie_consent_enabled"
                                       name="cookie_consent_enabled"
                                       value="1"
                                       {{ old('cookie_consent_enabled', $settings['cookie_consent_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label-modern" for="cookie_consent_enabled">
                                    <i class="fas fa-toggle-on"></i>
                                    Enable Cookie Consent Banner
                                </label>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                When enabled, a cookie consent banner will be displayed to users on their first visit.
                            </div>
                            @error('cookie_consent_enabled')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Cookie Consent Banner Text -->
                        <div class="form-group-modern">
                            <label for="cookie_consent_banner_text" class="form-label-modern">
                                <i class="fas fa-align-left"></i>
                                Banner Message
                            </label>
                            <div class="input-wrapper">
                                <textarea class="form-input-modern @error('cookie_consent_banner_text') is-invalid @enderror"
                                          id="cookie_consent_banner_text"
                                          name="cookie_consent_banner_text"
                                          rows="3"
                                          placeholder="Enter cookie consent banner message">{{ old('cookie_consent_banner_text', $settings['cookie_consent_banner_text'] ?? 'We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.') }}</textarea>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                This message will be displayed in the cookie consent banner.
                            </div>
                            @error('cookie_consent_banner_text')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Cookie Policy URL -->
                        <div class="form-group-modern">
                            <label for="cookie_policy_url" class="form-label-modern">
                                <i class="fas fa-link"></i>
                                Cookie Policy URL
                            </label>
                            <div class="input-wrapper">
                                <input type="text"
                                       class="form-input-modern @error('cookie_policy_url') is-invalid @enderror"
                                       id="cookie_policy_url"
                                       name="cookie_policy_url"
                                       value="{{ old('cookie_policy_url', $settings['cookie_policy_url'] ?? route('page.show', 'cookie-policy')) }}"
                                       placeholder="/cookie-policy">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                URL to your cookie policy page. Default: /cookie-policy
                            </div>
                            @error('cookie_policy_url')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Cookie Consent Position -->
                        <div class="form-group-modern">
                            <label for="cookie_consent_position" class="form-label-modern">
                                <i class="fas fa-arrows-alt-v"></i>
                                Banner Position
                            </label>
                            <div class="input-wrapper">
                                <select class="form-input-modern @error('cookie_consent_position') is-invalid @enderror"
                                        id="cookie_consent_position"
                                        name="cookie_consent_position">
                                    <option value="bottom" {{ old('cookie_consent_position', $settings['cookie_consent_position'] ?? 'bottom') == 'bottom' ? 'selected' : '' }}>Bottom</option>
                                    <option value="top" {{ old('cookie_consent_position', $settings['cookie_consent_position'] ?? 'bottom') == 'top' ? 'selected' : '' }}>Top</option>
                                </select>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Choose where the cookie consent banner should appear on the page.
                            </div>
                            @error('cookie_consent_position')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Cookie Consent Theme -->
                        <div class="form-group-modern">
                            <label for="cookie_consent_theme" class="form-label-modern">
                                <i class="fas fa-palette"></i>
                                Banner Theme
                            </label>
                            <div class="input-wrapper">
                                <select class="form-input-modern @error('cookie_consent_theme') is-invalid @enderror"
                                        id="cookie_consent_theme"
                                        name="cookie_consent_theme">
                                    <option value="light" {{ old('cookie_consent_theme', $settings['cookie_consent_theme'] ?? 'light') == 'light' ? 'selected' : '' }}>Light</option>
                                    <option value="dark" {{ old('cookie_consent_theme', $settings['cookie_consent_theme'] ?? 'light') == 'dark' ? 'selected' : '' }}>Dark</option>
                                </select>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Choose the color theme for the cookie consent banner.
                            </div>
                            @error('cookie_consent_theme')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
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

                <!-- Email Notification Preferences Section -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-bell"></i>
                            Email Notification Preferences
                        </h3>
                        <p class="modern-card__subtitle">Configure which email notifications to receive</p>
                    </div>
                    <div class="modern-card__body">
                        @php
                            $emailPreferences = json_decode(\App\Models\Setting::get('notification_email_preferences', '{}'), true);
                            $emailRecipients = json_decode(\App\Models\Setting::get('notification_email_recipients', '[]'), true);
                            if (empty($emailRecipients)) {
                                $settings = \App\Helpers\SettingHelper::all();
                                $adminEmail = \App\Helpers\SettingHelper::getFirstFromArraySetting($settings, 'emails');
                                $emailRecipients = $adminEmail ? [$adminEmail] : [];
                            }
                        @endphp

                        <!-- Notification Email Recipients -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-envelope"></i>
                                Notification Email Recipients
                            </label>
                            <div class="form-hint" style="margin-bottom: 15px;">
                                <i class="fas fa-info-circle"></i>
                                Email addresses that will receive admin notifications. If empty, uses the main contact email.
                            </div>
                            <div class="repeater-wrapper" id="notificationEmailRepeater">
                                <div class="repeater-items">
                                    @if(!empty($emailRecipients) && is_array($emailRecipients) && count($emailRecipients) > 0)
                                        @foreach($emailRecipients as $index => $email)
                                        <div class="repeater-item" data-index="{{ $index }}">
                                            <div class="input-wrapper">
                                                <input type="email"
                                                       class="form-input-modern"
                                                       name="notification_email_recipients[]"
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
                                                <input type="email"
                                                       class="form-input-modern"
                                                       name="notification_email_recipients[]"
                                                       value=""
                                                       placeholder="Enter email address">
                                                <button type="button" class="repeater-remove" onclick="removeRepeaterItem(this)" style="display: none;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addNotificationEmailItem()">
                                    <i class="fas fa-plus"></i>
                                    Add Email
                                </button>
                            </div>
                        </div>

                        <!-- Notification Type Toggles -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-toggle-on"></i>
                                Enable Email Notifications For
                            </label>
                            <div class="notification-preferences">
                                <!-- Order Notifications -->
                                <div class="preference-item">
                                    <div class="preference-content">
                                        <div class="preference-info">
                                            <h4 class="preference-title">
                                                <i class="fas fa-shopping-cart"></i>
                                                New Orders
                                            </h4>
                                            <p class="preference-description">Receive email when a new order is placed</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" 
                                                   name="notification_email_preferences[order]" 
                                                   value="1"
                                                   {{ ($emailPreferences['order'] ?? true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Contact Notifications -->
                                <div class="preference-item">
                                    <div class="preference-content">
                                        <div class="preference-info">
                                            <h4 class="preference-title">
                                                <i class="fas fa-envelope"></i>
                                                Contact Form Submissions
                                            </h4>
                                            <p class="preference-description">Receive email when someone submits the contact form</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" 
                                                   name="notification_email_preferences[contact]" 
                                                   value="1"
                                                   {{ ($emailPreferences['contact'] ?? true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Review Notifications -->
                                <div class="preference-item">
                                    <div class="preference-content">
                                        <div class="preference-info">
                                            <h4 class="preference-title">
                                                <i class="fas fa-star"></i>
                                                Product Reviews
                                            </h4>
                                            <p class="preference-description">Receive email when a new product review is submitted</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" 
                                                   name="notification_email_preferences[review]" 
                                                   value="1"
                                                   {{ ($emailPreferences['review'] ?? true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Stock Notifications -->
                                <div class="preference-item">
                                    <div class="preference-content">
                                        <div class="preference-info">
                                            <h4 class="preference-title">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Low Stock Alerts
                                            </h4>
                                            <p class="preference-description">Receive email when product stock falls below threshold</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" 
                                                   name="notification_email_preferences[stock]" 
                                                   value="1"
                                                   {{ ($emailPreferences['stock'] ?? true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <!-- System Notifications -->
                                <div class="preference-item">
                                    <div class="preference-content">
                                        <div class="preference-info">
                                            <h4 class="preference-title">
                                                <i class="fas fa-cog"></i>
                                                System Errors
                                            </h4>
                                            <p class="preference-description">Receive email for critical system errors and alerts</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" 
                                                   name="notification_email_preferences[system]" 
                                                   value="1"
                                                   {{ ($emailPreferences['system'] ?? true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-info" style="margin-top: 20px;">
                            <div class="info-item">
                                <i class="fas fa-info-circle"></i>
                                <span>In-app notifications will still be created regardless of email preferences</span>
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
// Image preview functionality for logo and icon
document.addEventListener('DOMContentLoaded', function() {
    // Logo preview
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const logoPreviewImg = document.getElementById('logoPreviewImg');

    if (logoInput && logoPreview && logoPreviewImg) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    logoPreviewImg.src = e.target.result;
                    logoPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                logoPreview.style.display = 'none';
            }
        });
    }

    // Icon preview
    const iconInput = document.getElementById('iconInput');
    const iconPreview = document.getElementById('iconPreview');
    const iconPreviewImg = document.getElementById('iconPreviewImg');

    if (iconInput && iconPreview && iconPreviewImg) {
        iconInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    iconPreviewImg.src = e.target.result;
                    iconPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                iconPreview.style.display = 'none';
            }
        });
    }
});

function removeImagePreview() {
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const iconInput = document.getElementById('iconInput');
    const iconPreview = document.getElementById('iconPreview');

    if (logoInput && logoPreview) {
        logoInput.value = '';
        logoPreview.style.display = 'none';
    }

    if (iconInput && iconPreview) {
        iconInput.value = '';
        iconPreview.style.display = 'none';
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
    } else {
        // Hide remove button if only one item left
        const removeBtn = items.querySelector('.repeater-remove');
        if (removeBtn) removeBtn.style.display = 'none';
    }
}

// Custom function for notification email recipients
function addNotificationEmailItem() {
    const repeater = document.getElementById('notificationEmailRepeater');
    const items = repeater.querySelector('.repeater-items');
    const existingItems = items.querySelectorAll('.repeater-item');
    const newIndex = existingItems.length;

    const newItem = document.createElement('div');
    newItem.className = 'repeater-item';
    newItem.setAttribute('data-index', newIndex);
    newItem.innerHTML = `
        <div class="input-wrapper">
            <input type="email"
                   class="form-input-modern"
                   name="notification_email_recipients[]"
                   value=""
                   placeholder="Enter email address">
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
        const newRemoveBtn = newItem.querySelector('.repeater-remove');
        if (newRemoveBtn) newRemoveBtn.style.display = 'flex';
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

});
</script>

<style>
/* Notification Preferences Styles */
.notification-preferences {
    margin-top: 20px;
}

.preference-item {
    padding: 20px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 15px;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.preference-item:hover {
    background-color: #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.preference-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.preference-info {
    flex: 1;
}

.preference-title {
    margin: 0 0 8px 0;
    font-size: 16px;
    font-weight: 600;
    color: #374E94;
    display: flex;
    align-items: center;
    gap: 10px;
}

.preference-title i {
    font-size: 18px;
}

.preference-description {
    margin: 0;
    font-size: 14px;
    color: #666666;
    line-height: 1.5;
}

/* Toggle Switch Styles */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
    flex-shrink: 0;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: #374E94;
}

.toggle-switch input:focus + .toggle-slider {
    box-shadow: 0 0 1px #374E94;
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(26px);
}

.toggle-switch input:disabled + .toggle-slider {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
@endsection

