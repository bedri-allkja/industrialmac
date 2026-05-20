<header class="header-section position-relative z-2 header-stikcy">
    <div class="info-bar d-none d-md-block">
        <div class="container">
            <div class="info-row d-flex justify-content-between align-items-center">
                <div class="info-left">
                    <ul class="d-flex align-items-center gap-3">
                        <li><a href="{{ route('front.contact') }}">@lang('Quick Quote')</a></li>
                        <li><a href="{{ route('front.contact') }}">@lang('Contact Us')</a></li>
                    </ul>
                </div>
                <div class="info-right">
                    <ul class="d-flex align-items-center gap-3">
                        @if (Auth::guard('web')->check())
                            <li><a href="{{ route('user-dashboard') }}">
                                <i class="fas fa-user me-1"></i> @lang('Dashboard')
                            </a></li>
                        @elseif(Auth::guard('rider')->check())
                            <li><a href="{{ route('rider-dashboard') }}">
                                <i class="fas fa-user me-1"></i> @lang('Dashboard')
                            </a></li>
                        @else
                            <li><a href="{{ route('user.login') }}">
                                <i class="fas fa-user me-1"></i> @lang('My Account')
                            </a></li>
                        @endif

                        <li class="d-none d-md-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="2" height="16" viewBox="0 0 2 16" fill="none">
                                <path d="M1 0V16" stroke="white" stroke-opacity="0.4" />
                            </svg>
                        </li>

                        <li class="d-flex gap-2 align-items-center">
                            <i class="fas fa-phone-alt" style="font-size:12px;color:rgba(255,255,255,0.8)"></i>
                            <a href="tel:{{ $ps->phone }}">{{ $ps->phone }}</a>
                        </li>

                        @if ($gs->is_currency == 1)
                            <li class="d-none d-md-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="2" height="16" viewBox="0 0 2 16" fill="none">
                                    <path d="M1 0V16" stroke="white" stroke-opacity="0.4" />
                                </svg>
                            </li>
                            <li class="d-flex gap-1 align-items-center">
                                <div class="dropdown">
                                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                        {{ Session::has('currency')
                                            ? $currencies->where('id', '=', Session::get('currency'))->first()->name
                                            : DB::table('currencies')->where('is_default', '=', 1)->first()->name }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @foreach ($currencies as $currency)
                                            <li>
                                                <a class="dropdown-item dropdown__item {{ Session::has('currency')
                                                    ? (Session::get('currency') == $currency->id ? 'active' : '')
                                                    : ($currencies->where('is_default', '=', 1)->first()->id == $currency->id ? 'active' : '') }}"
                                                    href="{{ route('front.currency', $currency->id) }}">{{ $currency->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @endif

                        <li class="d-none d-md-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="2" height="16" viewBox="0 0 2 16" fill="none">
                                <path d="M1 0V16" stroke="white" stroke-opacity="0.4" />
                            </svg>
                        </li>
                        <li class="d-flex gap-1 align-items-center">
                            <div class="dropdown">
                                <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                    @lang('English')
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @foreach ($languges as $language)
                                        <li>
                                            <a class="dropdown-item dropdown__item {{ Session::has('language')
                                                ? (Session::get('language') == $language->id ? 'active' : '')
                                                : ($languges->where('is_default', '=', 1)->first()->id == $language->id ? 'active' : '') }}"
                                                href="{{ route('front.language', $language->id) }}">{{ $language->language }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="header-top">
        <div class="container">
            <div class="create-navbar d-flex align-items-center justify-content-between">
                <div class="nav-left d-flex align-items-center gap-3">
                    <button type="button" class="header-toggle mobile-menu-toggle d-flex d-xl-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M3 12H21M3 6H21M3 18H15" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <a class="header-logo-wrapper" href="{{ route('front.index') }}">
                        <img class="logo" src="{{ site_brand_logo() }}" alt="{{ $gs->title }}">
                    </a>
                </div>

                <div class="nav-center d-none d-xl-flex justify-content-center">
                    <ul class="d-flex align-items-center nav-menus">
                        <li>
                            <a href="{{ route('front.index') }}" class="nav-link {{ request()->path() == '/' ? 'active' : '' }}">@lang('Homepage')</a>
                        </li>
                        <li class="has-megamenu {{ request()->path() == 'category' ? 'active' : '' }}">
                            <a href="{{ route('front.category') }}">@lang('Brands')</a>
                            <span class="has-submenu-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 9L12 15L18 9" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div class="megamenu cat-megamenu">
                                <div class="row w-100">
                                    @foreach ($categories as $category)
                                        <div class="col-lg-3">
                                            <div class="single-menu mt-30">
                                                <h5><a href="{{ route('front.category', [$category->slug]) }}">{{ $category->name }}</a></h5>
                                                @if ($category->subs->count() > 0)
                                                    <ul>
                                                        @foreach ($category->subs as $subcategory)
                                                            <li><a href="{{ route('front.category', [$category->slug, $subcategory->slug]) }}{{ !empty(request()->input('search')) ? '?search=' . request()->input('search') : '' }}">{{ $subcategory->name }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="{{ route('front.category') }}" class="nav-link">@lang('Categories')</a>
                        </li>
                        <li class="has-submenu">
                            <a href="javascript:void(0)">@lang('About Us')</a>
                            <span class="has-submenu-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 9L12 15L18 9" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <ul class="dropdown-menu">
                                @foreach ($pages->where('header', '=', 1) as $data)
                                    <li>
                                        <a class="dropdown-item dropdown__item" href="{{ route('front.vendor', $data->slug) }}">{{ $data->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        <li>
                            <a href="{{ route('front.contact') }}" class="nav-link {{ request()->path() == 'contact' ? 'active' : '' }}">@lang('Contact Us')</a>
                        </li>
                    </ul>
                </div>

                <div class="nav-right d-flex align-items-center gap-2">
                    <form class="esporim-search-bar d-none d-lg-flex"
                        action="{{ route('front.category') }}" method="GET">
                        <input type="text" name="search" placeholder="@lang('Search Products')"
                            value="{{ request()->input('search') }}">
                        <button type="submit" aria-label="@lang('Search')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M21 21L17.5 17.5M20 11.5C20 16.19 16.19 20 11.5 20C6.81 20 3 16.19 3 11.5C3 6.81 6.81 3 11.5 3C16.19 3 20 6.81 20 11.5Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>

                    <div class="icon-circle d-lg-none">
                        <button id="searchIcon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M21 21L17.5 17.5M20 11.5C20 16.19 16.19 20 11.5 20C6.81 20 3 16.19 3 11.5C3 6.81 6.81 3 11.5 3C16.19 3 20 6.81 20 11.5Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                    @php
                        $cart = Session::has('cart') ? Session::get('cart')->items : [];
                    @endphp
                    <div class="icon-circle">
                        <a href="{{ route('front.cart') }}">
                            <span class="cart-count" id="cart-count">{{ $cart ? count($cart) : 0 }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M2 2H3.306C3.552 2 3.675 2 3.774 2.045C3.861 2.085 3.935 2.149 3.987 2.23C4.046 2.322 4.063 2.443 4.098 2.687L4.571 6M4.571 6L5.623 13.731C5.757 14.713 5.824 15.203 6.058 15.572C6.265 15.898 6.561 16.156 6.911 16.317C7.309 16.5 7.804 16.5 8.794 16.5H17.352C18.295 16.5 18.766 16.5 19.151 16.33C19.491 16.181 19.782 15.94 19.992 15.634C20.231 15.288 20.319 14.825 20.496 13.899L21.819 6.95C21.881 6.624 21.912 6.461 21.867 6.334C21.828 6.222 21.75 6.128 21.648 6.068C21.531 6 21.365 6 21.033 6H4.571ZM10 21C10 21.552 9.552 22 9 22C8.448 22 8 21.552 8 21C8 20.448 8.448 20 9 20C9.552 20 10 20.448 10 21ZM18 21C18 21.552 17.552 22 17 22C16.448 22 16 21.552 16 21C16 20.448 16.448 20 17 20C17.552 20 18 20.448 18 21Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
