@if ($gs->is_cookie == 1)
    <div id="cookie-consent" class="cookie-consent" aria-live="polite" aria-label="@lang('Cookie consent')" hidden>
        <div class="cookie-consent__panel">
            <div class="cookie-consent__main">
                <div class="cookie-consent__icon" aria-hidden="true">
                    <i class="fas fa-cookie-bite"></i>
                </div>
                <div class="cookie-consent__copy">
                    <h3>@lang('We value your privacy')</h3>
                    <p>
                        @lang('We use cookies to ensure the website works properly, remember your preferences, and — with your consent — analyze traffic and improve our services. You can accept all cookies, reject non-essential cookies, or manage your preferences below.')
                        <a href="{{ route('front.privacy') }}" class="cookie-consent__link">
                            {{ optional($langg)->language === 'Italian' ? 'Informativa sulla Privacy' : __('Privacy Policy') }}
                        </a>
                        ·
                        <a href="{{ route('front.cookie') }}" class="cookie-consent__link">
                            {{ optional($langg)->language === 'Italian' ? 'Cookie Policy' : __('Cookie Policy') }}
                        </a>
                    </p>
                </div>
                <div class="cookie-consent__actions">
                    <button type="button" class="cookie-consent__btn cookie-consent__btn--primary" data-cookie-action="accept-all">
                        @lang('Accept all')
                    </button>
                    <button type="button" class="cookie-consent__btn cookie-consent__btn--outline" data-cookie-action="reject-all">
                        @lang('Reject non-essential')
                    </button>
                    <button type="button" class="cookie-consent__btn cookie-consent__btn--ghost" data-cookie-action="toggle-settings" aria-expanded="false" aria-controls="cookie-consent-settings">
                        @lang('Cookie settings')
                    </button>
                </div>
            </div>

            <div id="cookie-consent-settings" class="cookie-consent__settings" hidden>
                <div class="cookie-consent__setting">
                    <div>
                        <strong>@lang('Essential cookies')</strong>
                        <span>@lang('Required for security, session management, and basic site functionality.')</span>
                    </div>
                    <div class="cookie-consent__setting-badge">@lang('Always active')</div>
                </div>
                <div class="cookie-consent__setting">
                    <div>
                        <strong>@lang('Analytics cookies')</strong>
                        <span>@lang('Help us understand how visitors use the website so we can improve it.')</span>
                    </div>
                    <label class="cookie-consent__switch">
                        <input type="checkbox" id="cookie-pref-analytics">
                        <span></span>
                    </label>
                </div>
                <div class="cookie-consent__setting">
                    <div>
                        <strong>@lang('Marketing cookies')</strong>
                        <span>@lang('Used to deliver relevant content and measure campaign performance.')</span>
                    </div>
                    <label class="cookie-consent__switch">
                        <input type="checkbox" id="cookie-pref-marketing">
                        <span></span>
                    </label>
                </div>
                <div class="cookie-consent__settings-actions">
                    <button type="button" class="cookie-consent__btn cookie-consent__btn--primary" data-cookie-action="save-preferences">
                        @lang('Save preferences')
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
