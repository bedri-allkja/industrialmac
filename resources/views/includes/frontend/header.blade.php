@php
    $currentLanguage = Session::has('language')
        ? $languges->where('id', Session::get('language'))->first()
        : $languges->where('is_default', '=', 1)->first();
    $aboutPage = $pages->where('header', '=', 1)->first();
    $aboutSlug = $aboutPage ? $aboutPage->slug : 'about';
    // Contact details shown in the top bar. Editable in the admin panel at
    // Menu Page Settings > Contact Us Page (falls back to defaults if empty).
    $headerEmail = !empty($ps->email) ? $ps->email : 'info@industrialmac.it';
@endphp

<header class="header-section im-header header-stikcy">
    {{-- Top info bar --}}
    <div class="im-topbar d-none d-md-block">
        <div class="container">
            <div class="im-topbar__inner">
                <ul class="im-topbar__left">
                    <li>
                        <a href="mailto:{{ $headerEmail }}"><i class="fas fa-envelope"></i> {{ $headerEmail }}</a>
                    </li>
                </ul>

                <div class="im-topbar__center">@lang('Fast shipping across Italy and Europe')</div>

                <ul class="im-topbar__right">
                    <li>
                        <a href="{{ route('front.contact') }}">
                            <i class="fas fa-headset"></i> @lang('Customer support')
                        </a>
                    </li>
                    @if ($languges->count() > 1)
                        <li class="im-lang">
                            <button class="im-lang__btn" type="button">
                                {{ $currentLanguage->language ?? __('English') }}
                                <i class="fas fa-chevron-down ms-1"></i>
                            </button>
                            <ul class="im-lang__menu">
                                @foreach ($languges as $language)
                                    <li>
                                        <a class="im-lang__link {{ optional($currentLanguage)->id == $language->id ? 'active' : '' }}"
                                            href="{{ route('front.language', $language->id) }}">{{ $language->language }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    {{-- Main bar: logo, nav, actions --}}
    <div class="im-header-main header-top">
        <div class="container">
            <div class="im-header-main__inner">
                <div class="im-header-main__left">
                    <button type="button" class="im-header-toggle header-toggle mobile-menu-toggle d-xl-none" aria-label="@lang('Menu')">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a class="im-header-logo" href="{{ route('front.index') }}">
                        <img class="logo" src="{{ site_brand_logo() }}" alt="{{ $gs->title }}">
                    </a>
                </div>

                <nav class="im-nav d-none d-xl-block">
                    <ul class="im-nav__menu">
                        <li class="im-nav__item {{ request()->routeIs('front.index') ? 'active' : '' }}">
                            <a class="im-nav__link" href="{{ route('front.index') }}">@lang('Home')</a>
                        </li>

                        <li class="im-nav__item im-nav__item--mega {{ request()->routeIs('front.category') ? 'active' : '' }}">
                            <a class="im-nav__link" href="{{ route('front.category') }}">
                                @lang('Products') <i class="fas fa-chevron-down im-nav__chevron"></i>
                            </a>
                            <div class="im-mega">
                                <div class="im-mega__inner">
                                    <div class="im-mega__grid">
                                        @foreach (($megaCategories ?? collect()) as $category)
                                            <a class="im-mega__link" href="{{ route('front.category', $category->slug) }}">
                                                {{ $category->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="im-mega__footer">
                                        <a class="im-mega__all" href="{{ route('front.categories') }}">
                                            @lang('View all brands') <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="im-nav__item {{ request()->routeIs('front.categories') ? 'active' : '' }}">
                            <a class="im-nav__link" href="{{ route('front.categories') }}">@lang('Brands')</a>
                        </li>

                        <li class="im-nav__item im-nav__item--mega {{ request()->routeIs('front.about') || request()->routeIs('front.quality') || request()->routeIs('front.faq') ? 'active' : '' }}">
                            <a class="im-nav__link" href="{{ route('front.about') }}">
                                @lang('Company') <i class="fas fa-chevron-down im-nav__chevron"></i>
                            </a>
                            <div class="im-mega im-mega--simple">
                                <div class="im-mega__inner">
                                    <div class="im-mega__grid">
                                        <a class="im-mega__link" href="{{ route('front.about') }}">@lang('About Us')</a>
                                        <a class="im-mega__link" href="{{ route('front.quality') }}">@lang('Quality Policy')</a>
                                        <a class="im-mega__link" href="{{ route('front.faq') }}">@lang('Questions & Answers')</a>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="im-nav__item {{ request()->routeIs('front.contact') ? 'active' : '' }}">
                            <a class="im-nav__link" href="{{ route('front.contact') }}">@lang('Contacts')</a>
                        </li>
                    </ul>
                </nav>

                <div class="im-header-actions">
                    <a href="{{ route('front.cart') }}" class="im-header-cart" title="@lang('Cart')">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    <a href="{{ route('front.quote') }}" class="im-quote-btn">
                        @lang('Request Quote') <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
