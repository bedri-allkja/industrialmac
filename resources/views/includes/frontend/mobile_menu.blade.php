@php
    $aboutPage = $pages->where('header', '=', 1)->first();
    $aboutSlug = $aboutPage ? $aboutPage->slug : 'about';
@endphp

<!-- Mobile Menu -->
<div class="mobile-menu">
    <div class="mobile-menu-top">
        <img src="{{ site_brand_logo() }}" alt="{{ $gs->title }}" class="mobile-menu-brand-logo">
        <svg class="close" role="button" tabindex="0" aria-label="@lang('Close menu')" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" style="cursor:pointer;">
            <path d="M18 6L6 18M6 6L18 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </div>

    <nav>
        <div class="nav justify-content-between pt-3" id="mobile-menu-tabs" role="tablist">
            <button class="flex-grow-1 state-left-btn active active-tab-btn" id="main-menu-tab" data-bs-toggle="tab"
                data-bs-target="#main-menu" type="button" role="tab" aria-controls="main-menu"
                aria-selected="true">@lang('MENU')</button>
            <button class="flex-grow-1 state-right-btn active-tab-btn" id="categories-tab" data-bs-toggle="tab"
                data-bs-target="#categories" type="button" role="tab" aria-controls="categories"
                aria-selected="false">@lang('BRANDS')</button>
        </div>
    </nav>

    <div class="tab-content" id="mobile-menu-tab-content">
        <div class="tab-pane fade show active table-responsive tb-tb" id="main-menu" role="tabpanel"
            aria-labelledby="main-menu-tab">
            <div class="mobile-menu-widget">
                <div class="single-product-widget">
                    <div class="product-cat-widget">
                        <ul class="accordion">
                            <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                            <li><a href="{{ route('front.category') }}">@lang('Products')</a></li>
                            <li><a href="{{ route('front.categories') }}">@lang('Brands')</a></li>
                            <li><a href="{{ route('front.about') }}">@lang('Company')</a></li>
                            <li><a href="{{ route('front.quality') }}">@lang('Quality Policy')</a></li>
                            <li><a href="{{ route('front.faq') }}">@lang('Questions & Answers')</a></li>
                            <li><a href="{{ route('front.contact') }}">@lang('Contacts')</a></li>
                            <li><a href="{{ route('front.cart') }}">@lang('Cart')</a></li>
                        </ul>

                        <div class="auth-actions-btn gap-3 d-flex flex-column mt-3">
                            <a class="template-btn" href="{{ route('front.quote') }}">
                                @lang('Request Quote') <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade table-responsive tb-tb" id="categories" role="tabpanel"
            aria-labelledby="categories-tab">
            <div class="mobile-menu-widget">
                <div class="single-product-widget">
                    <div class="product-cat-widget">
                        <ul class="accordion im-cat-list">
                            @foreach (($megaCategories ?? $categories) as $category)
                                <li>
                                    <a href="{{ route('front.category', $category->slug) }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
