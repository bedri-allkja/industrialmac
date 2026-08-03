<footer class="gs-footer-section">
    <div class="container">
        <div class="row footer-row gy-4">
            <div class="col-lg-3 col-md-6 col-12 left-info">
                <a href="{{ route('front.index') }}">
                    <img class="logo mb-3" src="{{ site_brand_logo() }}" alt="{{ $gs->title }}">
                </a>

                <div class="footer-contact-info">
                    <a href="{{ route('front.contact') }}">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Via Della Volta 37, Brescia 25124, Italy
                    </a>
                    <a href="mailto:{{ $ps->email ?: 'info@industrialmac.it' }}">
                        <i class="fas fa-envelope me-2"></i>
                        {{ $ps->email ?: 'info@industrialmac.it' }}
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <h5>@lang('Company')</h5>
                <ul class="footer-category-links">
                    <li><a href="{{ route('front.index') }}">@lang('Homepage')</a></li>
                    <li><a href="{{ route('front.about') }}">@lang('About Us')</a></li>
                    <li><a href="{{ route('front.quality') }}">@lang('Quality Policy')</a></li>
                    <li><a href="{{ route('front.faq') }}">@lang('Questions & Answers')</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <h5>@lang('Contacts')</h5>
                <ul class="footer-category-links">
                    <li><a href="{{ route('front.contact') }}">@lang('Contacts')</a></li>
                    <li>
                        <a href="{{ route('front.privacy') }}">
                            {{ optional($langg)->language === 'Italian' ? 'Informativa sulla Privacy' : __('Privacy Policy') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('front.cookie') }}">
                            {{ optional($langg)->language === 'Italian' ? 'Cookie Policy' : __('Cookie Policy') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('front.legal') }}">
                            {{ optional($langg)->language === 'Italian' ? 'Note Legali' : __('Legal Notice') }}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <h5>@lang('Address')</h5>
                <div class="ratio ratio-4x3 mb-2" style="border-radius:8px;overflow:hidden;">
                    <iframe
                        title="Industrialmac map"
                        src="https://www.google.com/maps?q=Via+Della+Volta+37,+25124+Brescia,+Italy&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        style="border:0;width:100%;height:100%;"
                        allowfullscreen>
                    </iframe>
                </div>
                <p class="small text-white-50 mb-0">Via Della Volta 37, Brescia 25124, Italy</p>
            </div>
        </div>

        <div class="footer-disclaimer">
            <p>@lang('Our company is not an authorized distributor or representative of the brands featured on our website. Our company is not an authorized distributor or representative of the manufacturer of all brands shown on the website and in the catalog, and the private brand names and trademarks displayed on this website are the property of their respective owners.')</p>
        </div>
    </div>

    <div class="gs-footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="footer-bottom-content text-center">
                        <p>{{ $gs->copyright }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
