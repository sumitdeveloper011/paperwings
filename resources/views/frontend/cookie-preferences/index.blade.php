@extends('layouts.frontend.main')

@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Cookie Preferences',
        'subtitle' => 'Manage your cookie preferences',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Cookie Preferences', 'url' => null]
        ]
    ])

    <section class="cookie-preferences-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="cookie-preferences-card">
                        <div class="cookie-preferences-card__header">
                            <h2 class="cookie-preferences-card__title">
                                <i class="fas fa-cog"></i>
                                Your Cookie Preferences
                            </h2>
                            <p class="cookie-preferences-card__description">
                                We respect your privacy. Choose which cookies you want to accept. You can change these preferences at any time.
                            </p>
                        </div>

                        <div class="cookie-preferences-card__body">
                            <form id="cookiePreferencesForm" class="cookie-preferences-form">
                                @csrf

                                <div class="cookie-category cookie-category--page">
                                    <div class="cookie-category__header">
                                        <div class="cookie-category__info">
                                            <h3 class="cookie-category__title">Essential Cookies</h3>
                                            <p class="cookie-category__description">
                                                These cookies are necessary for the website to function properly. They enable core functionality such as security, network management, and accessibility. These cookies cannot be disabled.
                                            </p>
                                        </div>
                                        <div class="cookie-category__toggle">
                                            <input type="checkbox" id="essentialCookiesPage" checked disabled>
                                            <label for="essentialCookiesPage" class="cookie-toggle">
                                                <span class="cookie-toggle__slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="cookie-category cookie-category--page">
                                    <div class="cookie-category__header">
                                        <div class="cookie-category__info">
                                            <h3 class="cookie-category__title">Analytics Cookies</h3>
                                            <p class="cookie-category__description">
                                                These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously (e.g., Google Analytics).
                                            </p>
                                        </div>
                                        <div class="cookie-category__toggle">
                                            <input type="checkbox" id="analyticsCookiesPage" class="cookie-preference-checkbox" data-category="analytics" {{ $preferences['analytics_cookies'] ? 'checked' : '' }}>
                                            <label for="analyticsCookiesPage" class="cookie-toggle">
                                                <span class="cookie-toggle__slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="cookie-category cookie-category--page">
                                    <div class="cookie-category__header">
                                        <div class="cookie-category__info">
                                            <h3 class="cookie-category__title">Marketing Cookies</h3>
                                            <p class="cookie-category__description">
                                                These cookies are used to deliver advertisements relevant to you and your interests. They also help measure the effectiveness of advertising campaigns.
                                            </p>
                                        </div>
                                        <div class="cookie-category__toggle">
                                            <input type="checkbox" id="marketingCookiesPage" class="cookie-preference-checkbox" data-category="marketing" {{ $preferences['marketing_cookies'] ? 'checked' : '' }}>
                                            <label for="marketingCookiesPage" class="cookie-toggle">
                                                <span class="cookie-toggle__slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="cookie-category cookie-category--page">
                                    <div class="cookie-category__header">
                                        <div class="cookie-category__info">
                                            <h3 class="cookie-category__title">Functionality Cookies</h3>
                                            <p class="cookie-category__description">
                                                These cookies allow the website to remember choices you make (such as your language or region) and provide enhanced, personalized features.
                                            </p>
                                        </div>
                                        <div class="cookie-category__toggle">
                                            <input type="checkbox" id="functionalityCookiesPage" class="cookie-preference-checkbox" data-category="functionality" {{ $preferences['functionality_cookies'] ? 'checked' : '' }}>
                                            <label for="functionalityCookiesPage" class="cookie-toggle">
                                                <span class="cookie-toggle__slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="cookie-preferences-form__actions">
                                    <button type="button" class="cookie-preferences-form__btn cookie-preferences-form__btn--reject" id="rejectAllPage">
                                        Reject All
                                    </button>
                                    <button type="button" class="cookie-preferences-form__btn cookie-preferences-form__btn--accept" id="acceptAllPage">
                                        Accept All
                                    </button>
                                    <button type="submit" class="cookie-preferences-form__btn cookie-preferences-form__btn--save">
                                        <i class="fas fa-save"></i>
                                        Save Preferences
                                    </button>
                                </div>
                            </form>

                            <div class="cookie-preferences-card__footer">
                                <a href="{{ route('page.show', 'cookie-policy') }}" class="cookie-preferences-card__link" target="_blank" rel="noopener noreferrer">
                                    <i class="fas fa-info-circle"></i>
                                    Learn more about our Cookie Policy
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cookiePreferencesForm');
    const rejectAllBtn = document.getElementById('rejectAllPage');
    const acceptAllBtn = document.getElementById('acceptAllPage');

    if (window.FormSubmissionHandler && form) {
        window.FormSubmissionHandler.init('cookiePreferencesForm', {
            loadingText: 'Saving Preferences...',
            timeout: 10000
        });
    }

    if (rejectAllBtn) {
        rejectAllBtn.addEventListener('click', function() {
            document.getElementById('analyticsCookiesPage').checked = false;
            document.getElementById('marketingCookiesPage').checked = false;
            document.getElementById('functionalityCookiesPage').checked = false;
            if (form) form.dispatchEvent(new Event('submit'));
        });
    }

    if (acceptAllBtn) {
        acceptAllBtn.addEventListener('click', function() {
            document.getElementById('analyticsCookiesPage').checked = true;
            document.getElementById('marketingCookiesPage').checked = true;
            document.getElementById('functionalityCookiesPage').checked = true;
            if (form) form.dispatchEvent(new Event('submit'));
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const preferences = {
                analytics_cookies: document.getElementById('analyticsCookiesPage').checked,
                marketing_cookies: document.getElementById('marketingCookiesPage').checked,
                functionality_cookies: document.getElementById('functionalityCookiesPage').checked,
            };

            if (window.CookieConsent) {
                window.CookieConsent.savePreferences(preferences, function() {
                    if (window.showToast) {
                        window.showToast('Cookie preferences saved successfully!', 'success');
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                });
            }
        });
    }
});
</script>
@endpush
