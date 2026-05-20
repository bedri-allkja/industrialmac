<!-- Mobile Menu -->
<div class="mobile-menu">
    <div class="mobile-menu-top">
        <img src="{{ site_brand_logo() }}" alt="{{ $gs->title }}" class="mobile-menu-brand-logo">
        <svg class="close" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
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
                aria-selected="false">@lang('CATEGORIES')</button>
        </div>
    </nav>

    <div class="tab-content" id="mobile-menu-tab-content">
        <div class="tab-pane fade show active table-responsive tb-tb" id="main-menu" role="tabpanel"
            aria-labelledby="main-menu-tab" style="color: white;">
            <div class="mobile-menu-widget">
                <div class="single-product-widget">
                    <div class="product-cat-widget">
                        <ul class="accordion">
                            <li><a href="{{ route('front.index') }}">@lang('Homepage')</a></li>
                            <li><a href="{{ route('front.category') }}">@lang('Categories')</a></li>
                            <li>
                                <a href="#" data-bs-toggle="collapse" data-bs-target="#child_level_1"
                                    aria-controls="child_level_1" aria-expanded="false" class="collapsed">
                                    @lang('About Us')
                                </a>
                                <ul id="child_level_1" class="accordion-collapse collapse ms-3">
                                    @foreach ($pages->where('header', '=', 1) as $data)
                                        <li>
                                            <a href="{{ route('front.vendor', $data->slug) }}">{{ $data->title }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                            <li><a href="{{ route('front.contact') }}">@lang('Contact Us')</a></li>
                        </ul>

                        <div class="auth-actions-btn gap-3 d-flex flex-column mt-3">
                            @if (Auth::guard('web')->check() && Auth::guard('web')->user()->is_vendor == 2)
                                <a class="template-btn" href="{{ route('vendor.dashboard') }}">@lang('Vendor Dashboard')</a>
                            @elseif (!Auth::guard('web')->check() && !Auth::guard('rider')->check())
                                <a class="template-btn" href="{{ route('vendor.login') }}">@lang('Vendor Login')</a>
                            @endif

                            @if (Auth::guard('rider')->check())
                                <a class="template-btn" href="{{ route('rider-dashboard') }}">@lang('Rider Dashboard')</a>
                            @elseif (!Auth::guard('web')->check() && !Auth::guard('rider')->check())
                                <a class="template-btn" href="{{ route('rider.login') }}">@lang('Rider Login')</a>
                            @endif

                            @if (Auth::guard('web')->check() && Auth::guard('web')->user()->is_vendor != 2)
                                <a class="template-btn" href="{{ route('user-dashboard') }}">@lang('Dashboard')</a>
                            @elseif (!Auth::guard('web')->check() && !Auth::guard('rider')->check())
                                <a class="template-btn" href="{{ route('user.login') }}">@lang('Login')</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade table-responsive tb-tb" id="categories" role="tabpanel"
            aria-labelledby="categories-tab" style="color: white;">
            <div class="mobile-menu-widget">
                <div class="single-product-widget">
                    <div class="product-cat-widget">
                        <ul class="accordion">
                            @foreach ($categories as $category)
                                @if ($category->subs->count() > 0)
                                    <li>
                                        @php $isCategoryActive = Request::segment(2) === $category->slug; @endphp
                                        <div class="d-flex justify-content-between align-items-lg-baseline">
                                            <a href="{{ route('front.category', $category->slug) }}"
                                                class="{{ $isCategoryActive ? 'sidebar-active-color' : '' }}">
                                                {{ $category->name }}
                                            </a>
                                            <button type="button" data-bs-toggle="collapse"
                                                data-bs-target="#{{ $category->slug }}_level_2"
                                                aria-controls="{{ $category->slug }}_level_2"
                                                aria-expanded="{{ $isCategoryActive ? 'true' : 'false' }}"
                                                class="position-relative bottom-12 {{ $isCategoryActive ? '' : 'collapsed' }}">
                                                <i class="fa-solid fa-plus"></i>
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                        </div>

                                        @foreach ($category->subs as $subcategory)
                                            @php $isSubcategoryActive = $isCategoryActive && Request::segment(3) === $subcategory->slug; @endphp
                                            <ul id="{{ $category->slug }}_level_2"
                                                class="accordion-collapse collapse ms-3 {{ $isCategoryActive ? 'show' : '' }}">
                                                <li>
                                                    <div class="d-flex justify-content-between align-items-lg-baseline">
                                                        <a href="{{ route('front.category', [$category->slug, $subcategory->slug]) }}"
                                                            class="{{ $isSubcategoryActive ? 'sidebar-active-color' : '' }}"
                                                            @if ($subcategory->childs->count() > 0)
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#inner{{ $subcategory->slug }}_level_2_1"
                                                                aria-controls="inner{{ $subcategory->slug }}_level_2_1"
                                                                aria-expanded="{{ $isSubcategoryActive ? 'true' : 'false' }}"
                                                            @endif>
                                                            {{ $subcategory->name }}
                                                        </a>
                                                        @if ($subcategory->childs->count() > 0)
                                                            <button type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#inner{{ $subcategory->slug }}_level_2_1"
                                                                aria-controls="inner{{ $subcategory->slug }}_level_2_1"
                                                                aria-expanded="{{ $isSubcategoryActive ? 'true' : 'false' }}"
                                                                class="position-relative bottom-12 {{ $isSubcategoryActive ? '' : 'collapsed' }}">
                                                                <i class="fa-solid fa-plus"></i>
                                                                <i class="fa-solid fa-minus"></i>
                                                            </button>
                                                        @endif
                                                    </div>

                                                    @if ($subcategory->childs->count() > 0)
                                                        <ul id="inner{{ $subcategory->slug }}_level_2_1"
                                                            class="accordion-collapse collapse ms-3 {{ $isSubcategoryActive ? 'show' : '' }}">
                                                            @foreach ($subcategory->childs as $child)
                                                                @php $isChildActive = $isSubcategoryActive && Request::segment(4) === $child->slug; @endphp
                                                                <li>
                                                                    <a href="{{ route('front.category', [$category->slug, $subcategory->slug, $child->slug]) }}"
                                                                        class="{{ $isChildActive ? 'sidebar-active-color' : '' }}">
                                                                        {{ $child->name }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            </ul>
                                        @endforeach
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ route('front.category', $category->slug) }}"
                                            class="{{ Request::segment(2) === $category->slug ? 'sidebar-active-color' : '' }}">
                                            {{ $category->name }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Bar -->
<div class="search-bar" id="searchBar">
    <div class="container">
        <div class="row">
            <div class="col">
                <form class="search-form"
                    action="{{ route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')]) }}">
                    @if (!empty(request()->input('sort')))
                        <input type="hidden" name="sort" value="{{ request()->input('sort') }}">
                    @endif
                    @if (!empty(request()->input('minprice')))
                        <input type="hidden" name="minprice" value="{{ request()->input('minprice') }}">
                    @endif
                    @if (!empty(request()->input('maxprice')))
                        <input type="hidden" name="maxprice" value="{{ request()->input('maxprice') }}">
                    @endif

                    <div class="input-group input__group">
                        <input type="text" class="form-control form__control" name="search"
                            placeholder="@lang('Search Products')">
                        <button class="btn btn-primary search-icn" type="submit" aria-label="@lang('Search')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M21 21L17.5 17.5M20 11.5C20 16.19 16.19 20 11.5 20C6.81 20 3 16.19 3 11.5C3 6.81 6.81 3 11.5 3C16.19 3 20 6.81 20 11.5Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
