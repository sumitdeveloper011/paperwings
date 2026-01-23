<div id="cookiePreferencesModal" class="cookie-preferences-modal" style="display: none;">
    <div class="cookie-preferences-modal__overlay"></div>
    <div class="cookie-preferences-modal__content">
        <div class="cookie-preferences-modal__header">
            <h2 class="cookie-preferences-modal__title">
                <i class="fas fa-cog"></i>
                Cookie Preferences
            </h2>
            <button type="button" class="cookie-preferences-modal__close" id="cookieModalClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cookie-preferences-modal__body">
            <p class="cookie-preferences-modal__description">
                We use cookies to enhance your browsing experience, analyze site traffic, and personalize content. 
                You can choose which cookies to accept. Essential cookies are always enabled as they are necessary for the website to function.
            </p>

            <div class="cookie-preferences-modal__categories">
                <div class="cookie-category">
                    <div class="cookie-category__header">
                        <div class="cookie-category__info">
                            <h3 class="cookie-category__title">Essential Cookies</h3>
                            <p class="cookie-category__description">
                                These cookies are necessary for the website to function properly. They enable core functionality such as security, network management, and accessibility.
                            </p>
                        </div>
                        <div class="cookie-category__toggle">
                            <label for="essentialCookies" class="cookie-toggle">
                                <input type="checkbox" id="essentialCookies" checked disabled>
                                <span class="cookie-toggle__slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="cookie-category">
                    <div class="cookie-category__header">
                        <div class="cookie-category__info">
                            <h3 class="cookie-category__title">Analytics Cookies</h3>
                            <p class="cookie-category__description">
                                These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously (e.g., Google Analytics).
                            </p>
                        </div>
                        <div class="cookie-category__toggle">
                            <label for="analyticsCookies" class="cookie-toggle">
                                <input type="checkbox" id="analyticsCookies" class="cookie-preference-checkbox" data-category="analytics">
                                <span class="cookie-toggle__slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="cookie-category">
                    <div class="cookie-category__header">
                        <div class="cookie-category__info">
                            <h3 class="cookie-category__title">Marketing Cookies</h3>
                            <p class="cookie-category__description">
                                These cookies are used to deliver advertisements relevant to you and your interests. They also help measure the effectiveness of advertising campaigns.
                            </p>
                        </div>
                        <div class="cookie-category__toggle">
                            <label for="marketingCookies" class="cookie-toggle">
                                <input type="checkbox" id="marketingCookies" class="cookie-preference-checkbox" data-category="marketing">
                                <span class="cookie-toggle__slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="cookie-category">
                    <div class="cookie-category__header">
                        <div class="cookie-category__info">
                            <h3 class="cookie-category__title">Functionality Cookies</h3>
                            <p class="cookie-category__description">
                                These cookies allow the website to remember choices you make (such as your language or region) and provide enhanced, personalized features.
                            </p>
                        </div>
                        <div class="cookie-category__toggle">
                            <label for="functionalityCookies" class="cookie-toggle">
                                <input type="checkbox" id="functionalityCookies" class="cookie-preference-checkbox" data-category="functionality">
                                <span class="cookie-toggle__slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cookie-preferences-modal__footer">
            <a href="{{ route('page.show', 'cookie-policy') }}" class="cookie-preferences-modal__link" target="_blank">
                <i class="fas fa-info-circle"></i>
                Learn more about cookies
            </a>
            <div class="cookie-preferences-modal__actions">
                <button type="button" class="cookie-preferences-modal__btn cookie-preferences-modal__btn--reject" id="cookieModalRejectAll">
                    Reject All
                </button>
                <button type="button" class="cookie-preferences-modal__btn cookie-preferences-modal__btn--accept" id="cookieModalAcceptAll">
                    Accept All
                </button>
                <button type="button" class="cookie-preferences-modal__btn cookie-preferences-modal__btn--save" id="cookieModalSave">
                    Save Preferences
                </button>
            </div>
        </div>
    </div>
</div>
