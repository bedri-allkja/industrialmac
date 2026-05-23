@php
    $compareCount = Session::has('compare') ? count(Session::get('compare')->items) : 0;
    $wishlistCount = Auth::guard('web')->check() ? Auth::guard('web')->user()->wishlistCount() : 0;
    $currentLanguage = Session::has('language')
        ? $languges->where('id', Session::get('language'))->first()
        : $languges->where('is_default', '=', 1)->first();
    $aboutPage = $pages->where('header', '=', 1)->first();
    $navCategories = $categories->take(4);
@endphp

<header class="header-section ignavo-header position-relative header-stikcy">
    {{-- Row 1: Utility bar --}}
    <div class="ignavo-header-utility d-none d-md-block">
        <div class="container">
            <div class="ignavo-header-utility__inner">
                <ul class="ignavo-header-utility__links">
                    @if ($aboutPage)
                        <li><a href="{{ route('front.vendor', $aboutPage->slug) }}">@lang('About Us')</a></li>
                    @else
                        <li><a href="{{ route('front.contact') }}">@lang('About Us')</a></li>
                    @endif
                    @if ($ps->faq == 1)
                        <li><a href="{{ route('front.faq') }}">@lang('FAQ')</a></li>
                    @endif
                    <li>
                        @if (Auth::guard('web')->check())
                            <a href="{{ route('user-order-track') }}">@lang('Order Tracking')</a>
                        @else
                            <a href="{{ route('user.login') }}">@lang('Order Tracking')</a>
                        @endif
                    </li>
                </ul>
                <ul class="ignavo-header-utility__meta">
                    <li>
                        <div class="dropdown">
                            <button class="ignavo-header-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $currentLanguage->language ?? __('English') }}
                                <i class="fas fa-chevron-down ms-1"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @foreach ($languges as $language)
                                    <li>
                                        <a class="dropdown-item {{ Session::has('language')
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

    {{-- Rows 2 + 3: sticky block --}}
    <div class="ignavo-header-sticky header-top">
        {{-- Row 2: Logo, garage, search, account tools --}}
        <div class="ignavo-header-main">
            <div class="container">
                <div class="ignavo-header-main__inner">
                    <div class="ignavo-header-main__left">
                        <button type="button" class="ignavo-header-toggle header-toggle mobile-menu-toggle d-xl-none" aria-label="@lang('Menu')">
                            <i class="fas fa-bars"></i>
                        </button>

                        <a class="ignavo-header-logo" href="{{ route('front.index') }}">
                            <img class="logo" src="{{ site_brand_logo() }}" alt="{{ $gs->title }}">
                        </a>
                    </div>

                    @if (!request()->routeIs('front.index'))
                        <div class="ignavo-header-main__center d-none d-lg-block">
                            @include('includes.frontend.product-search-form')
                        </div>
                    @endif

                    <div class="ignavo-header-main__right">
                        <button type="button" class="ignavo-header-icon-btn d-lg-none" id="searchIcon" aria-label="@lang('Search')">
                            <i class="fas fa-search"></i>
                        </button>

                        @if (Auth::guard('web')->check())
                            <a href="{{ route('user-dashboard') }}" class="ignavo-header-account d-none d-md-flex">
                                <span class="ignavo-header-account__icon"><i class="fas fa-user"></i></span>
                                <span class="ignavo-header-account__text">
                                    <small>@lang('Welcome')</small>
                                    <strong>@lang('Account')</strong>
                                </span>
                            </a>
                        @elseif(Auth::guard('rider')->check())
                            <a href="{{ route('rider-dashboard') }}" class="ignavo-header-account d-none d-md-flex">
                                <span class="ignavo-header-account__icon"><i class="fas fa-user"></i></span>
                                <span class="ignavo-header-account__text">
                                    <small>@lang('Rider')</small>
                                    <strong>@lang('Account')</strong>
                                </span>
                            </a>
                        @else
                            <a href="{{ route('user.login') }}" class="ignavo-header-account d-none d-md-flex">
                                <span class="ignavo-header-account__icon"><i class="fas fa-user"></i></span>
                                <span class="ignavo-header-account__text">
                                    <small>@lang('Sign In')</small>
                                    <strong>@lang('Account')</strong>
                                </span>
                            </a>
                        @endif

                        @if (Auth::guard('web')->check())
                            <a href="{{ route('user-wishlists') }}" class="ignavo-header-icon-btn" title="@lang('Wishlist')">
                                <i class="far fa-heart"></i>
                                <span class="ignavo-header-badge" id="wishlist-count">{{ $wishlistCount }}</span>
                            </a>
                        @else
                            <a href="{{ route('user.login') }}" class="ignavo-header-icon-btn" title="@lang('Wishlist')">
                                <i class="far fa-heart"></i>
                                <span class="ignavo-header-badge" id="wishlist-count">0</span>
                            </a>
                        @endif

                        <a href="{{ route('product.compare') }}" class="ignavo-header-icon-btn" title="@lang('Compare')">
                            <i class="fas fa-exchange-alt"></i>
                            <span class="ignavo-header-badge" id="compare-count">{{ $compareCount }}</span>
                        </a>

                        <a href="{{ route('front.quote') }}" class="ignavo-header-icon-btn" title="@lang('Request Quote')">
                            <i class="fas fa-file-invoice"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Navigation --}}
        <div class="ignavo-header-nav d-none d-xl-block">
            <div class="container">
                <div class="ignavo-header-nav__inner">
                    <ul class="ignavo-header-nav__menu">
                        <li class="ignavo-header-nav__categories has-megamenu">
                            <a href="{{ route('front.categories') }}" class="ignavo-header-nav__cat-btn">
                                <i class="fas fa-bars"></i>
                                <span>@lang('All Categories')</span>
                            </a>
                            <div class="megamenu cat-megamenu">
                                <div class="row w-100">
                                    @foreach ($categories as $category)
                                        <div class="col-lg-3">
                                            <div class="single-menu mt-30">
                                                <h5><a href="{{ route('front.category', [$category->slug]) }}">{{ $category->name }}</a></h5>
                                                @if ($category->subs->count() > 0)
                                                    <ul>
                                                        @foreach ($category->subs as $subcategory)
                                                            <li>
                                                                <a href="{{ route('front.category', [$category->slug, $subcategory->slug]) }}{{ !empty(request()->input('search')) ? '?search=' . request()->input('search') : '' }}">
                                                                    {{ $subcategory->name }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </li>

                        <li class="{{ request()->path() == '/' ? 'active' : '' }}">
                            <a href="{{ route('front.index') }}">@lang('Home')</a>
                        </li>

                        @foreach ($navCategories as $category)
                            <li class="{{ request()->segment(2) == $category->slug ? 'active' : '' }}">
                                <a href="{{ route('front.category', $category->slug) }}">{{ $category->name }}</a>
                            </li>
                        @endforeach

                        @if ($ps->contact == 1)
                            <li class="{{ request()->path() == 'contact' ? 'active' : '' }}">
                                <a href="{{ route('front.contact') }}">@lang('Contact')</a>
                            </li>
                        @endif
                    </ul>

                    <div class="ignavo-header-nav__promo">
                        <a href="{{ route('front.category') }}?type=best" class="ignavo-header-nav__best">
                            <i class="fas fa-percent"></i>
                            <span>@lang('Best Seller')</span>
                        </a>
                        <a href="{{ route('front.category') }}?type=flash" class="ignavo-header-nav__sale-badge">@lang('Sale')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
