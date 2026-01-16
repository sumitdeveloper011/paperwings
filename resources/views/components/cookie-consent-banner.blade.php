@php
    $cookieConsentService = app(\App\Services\CookieConsentService::class);
    $hasConsent = $cookieConsentService->hasConsentCookie();
    $settings = \App\Helpers\SettingHelper::all();
    $cookieConsentEnabled = isset($settings['cookie_consent_enabled']) && $settings['cookie_consent_enabled'] == '1';
    $bannerText = $settings['cookie_consent_banner_text'] ?? 'We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.';
    $cookiePolicyUrl = $settings['cookie_policy_url'] ?? route('page.show', 'cookie-policy');
@endphp

@if($cookieConsentEnabled && !$hasConsent)
<div id="cookieConsentBanner" class="cookie-consent-banner">
    <div class="cookie-consent-banner__container">
        <div class="cookie-consent-banner__content">
            <div class="cookie-consent-banner__text">
                <i class="fas fa-cookie-bite"></i>
                <p>{{ $bannerText }}</p>
            </div>
            <div class="cookie-consent-banner__actions">
                <a href="{{ $cookiePolicyUrl }}" class="cookie-consent-banner__link" target="_blank">
                    Learn More
                </a>
                <button type="button" class="cookie-consent-banner__btn cookie-consent-banner__btn--reject" id="cookieRejectAll">
                    Reject All
                </button>
                <button type="button" class="cookie-consent-banner__btn cookie-consent-banner__btn--customize" id="cookieCustomize">
                    Customize
                </button>
                <button type="button" class="cookie-consent-banner__btn cookie-consent-banner__btn--accept" id="cookieAcceptAll">
                    Accept All
                </button>
            </div>
        </div>
    </div>
</div>
@endif
