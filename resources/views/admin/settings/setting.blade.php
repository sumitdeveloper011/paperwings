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

<style>
/* Settings Page Specific Styles */
.settings-form {
    padding-bottom: 2rem;
}

.modern-card--sticky {
    position: sticky;
    top: 100px;
}

.social-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

/* File Upload Styles */
.file-upload-wrapper {
    margin-bottom: 1rem;
}

.file-upload-area {
    position: relative;
    border: 2px dashed var(--border-color);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    background: var(--background-color);
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.file-upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(55, 78, 148, 0.02);
}

.file-upload-area--small {
    min-height: 120px;
    padding: 1.5rem;
}

.file-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}

.file-upload-content {
    z-index: 1;
}

.file-upload-icon {
    font-size: 3rem;
    color: var(--accent-color);
    margin-bottom: 1rem;
}

.file-upload-text {
    margin: 0.5rem 0;
    color: var(--text-primary);
    font-weight: 500;
}

.file-upload-highlight {
    color: var(--primary-color);
}

.file-upload-hint {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0.5rem 0 0 0;
}

.file-preview {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.file-preview--small {
    max-width: 100px;
    max-height: 100px;
}

.preview-image {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
    object-fit: contain;
}

.file-preview--small .preview-image {
    max-height: 100px;
}

.preview-remove {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--danger-color);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(233, 92, 103, 0.3);
    transition: all 0.3s ease;
    z-index: 3;
}

.preview-remove:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(233, 92, 103, 0.4);
}

.current-image {
    margin-top: 1rem;
    padding: 1rem;
    background: rgba(55, 78, 148, 0.05);
    border-radius: 8px;
}

.current-image-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.current-image-preview {
    max-width: 200px;
    max-height: 100px;
    border-radius: 8px;
    object-fit: contain;
    border: 2px solid var(--border-color);
}

.current-image-preview--small {
    max-width: 64px;
    max-height: 64px;
}

/* Character Counter */
.char-counter {
    position: absolute;
    bottom: 8px;
    right: 12px;
    font-size: 0.75rem;
    color: var(--text-secondary);
    background: var(--background-color);
    padding: 2px 6px;
    border-radius: 4px;
}

.input-wrapper textarea + .char-counter {
    bottom: 8px;
}

/* Form Group Spacing */
.settings-form .form-group-modern {
    margin-bottom: 1.5rem;
}

.settings-form .form-group-modern:last-child {
    margin-bottom: 0;
}

/* Repeater Styles */
.repeater-wrapper {
    margin-top: 0;
}

.repeater-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.repeater-item {
    position: relative;
}

.repeater-item .input-wrapper {
    position: relative;
}

.repeater-remove {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--danger-color);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 2;
}

.repeater-remove:hover {
    background: #d04552;
    transform: translateY(-50%) scale(1.1);
}

.repeater-item:only-child .repeater-remove {
    display: none;
}

.repeater-add {
    margin-top: 1rem;
    width: 100%;
}

/* Form Actions */
.form-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.form-info {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.info-item i {
    color: var(--info-color);
    margin-top: 2px;
}

/* Tips List */
.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.tips-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.6;
}

.tips-list li i {
    color: var(--success-color);
    margin-top: 2px;
    flex-shrink: 0;
}

/* Responsive */
@media (max-width: 992px) {
    .modern-card--sticky {
        position: relative;
        top: 0;
    }
    
    .social-links-grid {
        grid-template-columns: 1fr;
    }
}
</style>

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

// Character Counter
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endsection

