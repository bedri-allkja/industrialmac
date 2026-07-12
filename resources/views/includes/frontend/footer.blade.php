<footer class="gs-footer-section">
    <div class="container">
        <div class="row footer-row gy-4">
            <div class="col-lg-3 col-md-6 col-12 left-info">
                <a href="{{ route('front.index') }}">
                    <img class="logo mb-3" src="{{ site_brand_logo() }}" alt="{{ $gs->title }}">
                </a>

                <div class="footer-contact-info">
                    <a href="tel:{{ $ps->phone }}">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        {{ $ps->street }}
                    </a>
                    <a href="tel:{{ $ps->phone }}">
                        <i class="fas fa-phone-alt me-2"></i>
                        {{ $ps->phone }}
                    </a>
                    <a href="mailto:{{ $ps->email }}">
                        <i class="fas fa-envelope me-2"></i>
                        {{ $ps->email }}
                    </a>
                </div>

                <div class="social-links mt-3">
                    @foreach (DB::table('social_links')->where('user_id', 0)->where('status', 1)->get() as $link)
                        <a href="{{ $link->link ?? '#' }}" target="_blank" rel="noopener">
                            <i class="{{ $link->icon }}"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <h5>@lang('Company')</h5>
                <ul class="footer-category-links">
                    @if ($ps->home == 1)
                        <li><a href="{{ route('front.index') }}">{{ __('Homepage') }}</a></li>
                    @endif
                    @foreach ($pages->where('header', '=', 1) as $data)
                        <li><a href="{{ route('front.vendor', $data->slug) }}">{{ $data->title }}</a></li>
                    @endforeach
                    @if ($ps->contact == 1)
                        <li><a href="{{ route('front.contact') }}">{{ __('Contact Us') }}</a></li>
                    @endif
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <h5>@lang('Brands')</h5>
                <ul class="footer-category-links">
                    @foreach (($navCategories ?? $categories)->take(6) as $cate)
                        <li>
                            <a href="{{ route('front.category', $cate->slug) }}">{{ $cate->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <h5>@lang('Address')</h5>
                <ul class="footer-category-links">
                    <li>
                        <i class="fas fa-map-marker-alt me-1" style="color:rgba(255,255,255,0.5)"></i>
                        {{ $ps->street }}
                    </li>
                    <li>
                        <i class="fas fa-phone-alt me-1" style="color:rgba(255,255,255,0.5)"></i>
                        <a href="tel:{{ $ps->phone }}">{{ $ps->phone }}</a>
                    </li>
                    <li>
                        <i class="fas fa-envelope me-1" style="color:rgba(255,255,255,0.5)"></i>
                        <a href="mailto:{{ $ps->email }}">{{ $ps->email }}</a>
                    </li>
                </ul>
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
