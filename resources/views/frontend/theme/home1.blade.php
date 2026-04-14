@extends('layouts.front')

@section('content')
    <!-- Hero Section -->
    <section class="hero-slider-wrapper">
        @foreach ($sliders as $slider)
            <div class="gs-hero-section" data-background="{{ asset('assets/images/sliders/' . $slider->photo) }}">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-8 col-lg-7">
                            <div class="hero-content">
                                @if ($slider->subtitle_text)
                                    <h6 class="subtitle">{{ $slider->subtitle_text }}</h6>
                                @endif
                                <h1 class="title">{{ $slider->title_text }}</h1>
                                @if ($slider->details_text)
                                    <p class="des">{{ $slider->details_text }}</p>
                                @endif
                                <a class="template-btn hero-shop-now-btn mt-3" href="{{ $slider->link }}">
                                    @lang('Shop Now')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    <!-- Feature Boxes -->
    <section class="esporim-features">
        <div class="container">
            <div class="row">
                @if (\Illuminate\Support\Facades\Schema::hasTable('services'))
                    @foreach (\App\Models\Service::all() as $service)
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="esporim-feature-box">
                                <div class="feature-icon">
                                    <img src="{{ asset('assets/images/services/' . $service->photo) }}" alt="{{ $service->title }}" style="width:42px;height:42px;object-fit:contain;">
                                </div>
                                <div>
                                    <h6>{{ $service->title }}</h6>
                                    <p>{{ $service->details }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- Brands Section -->
    <section class="esporim-brands">
        <div class="container">
            <h3 class="esporim-section-title">@lang('Our Brands') ›</h3>
            @php
                $hardcodedBrands = [
                    ['name' => 'Bosch',        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/16/Bosch-logo.svg/960px-Bosch-logo.svg.png'],
                    ['name' => 'Denso',        'logo' => 'https://logowik.com/content/uploads/images/denso9853.jpg'],
                    ['name' => 'Brembo',       'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Brembo_logo_%282022%29.svg/500px-Brembo_logo_%282022%29.svg.png'],
                    ['name' => 'Hella',        'logo' => 'https://upload.wikimedia.org/wikipedia/en/thumb/8/86/Hella_logo.svg/250px-Hella_logo.svg.png?_=20180427103904'],
                    ['name' => 'Valeo',        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Valeo_Logo.svg/500px-Valeo_Logo.svg.png'],
                    ['name' => 'Continental',  'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Continental_logo.svg/500px-Continental_logo.svg.png'],
                    ['name' => 'SKF',          'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/SKF-Logo.svg/1920px-SKF-Logo.svg.png?_=20150525024258'],
                    ['name' => 'Philips',      'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/Philips_logo_new.svg/640px-Philips_logo_new.svg.png'],
                    ['name' => 'Magneti Marelli', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/ad/Magneti_Marelli_logo.png?_=20160110162309'],
                    ['name' => 'Delphi',       'logo' => 'https://upload.wikimedia.org/wikipedia/en/thumb/1/17/Logo_of_company_Delphi_Technologies%2C_LLC_as_of_Nov_2018.svg/500px-Logo_of_company_Delphi_Technologies%2C_LLC_as_of_Nov_2018.svg.png'],
                    ['name' => 'ZF',           'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/94/ZF_logo_STD_Blue_3CC.svg/640px-ZF_logo_STD_Blue_3CC.svg.png'],
                    ['name' => 'Aisin',        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/AISIN_CORPORATION_LOGO.svg/500px-AISIN_CORPORATION_LOGO.svg.png'],
                ];
            @endphp
            <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6">
                @foreach ($hardcodedBrands as $brand)
                    <div class="col">
                        <div class="brand-item">
                            <img src="{{ $brand['logo'] }}" alt="{{ $brand['name'] }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Categories + Products Section -->
    <section class="gs-explore-product-section" style="background:var(--esporim-light-bg);">
        <div class="container">
            <div class="row g-4">
                <!-- Category Sidebar -->
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="home-cat-sidebar">
                        <h5 class="home-cat-sidebar__title">
                            <i class="fas fa-th-list me-2"></i>@lang('Categories')
                        </h5>
                        @php
                            $sidebarCategories = App\Models\Category::where('status', 1)->get();
                        @endphp
                        <ul class="home-cat-sidebar__list">
                            @foreach ($sidebarCategories as $cat)
                                <li>
                                    <a href="{{ route('front.category', $cat->slug) }}">
                                        <span>{{ $cat->name }}</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="col-lg-9">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="esporim-section-title mb-0" style="border:none;padding:0;">@lang('Latest Products')</h3>
                        <a href="{{ route('front.category') }}" class="view-all-link">@lang('View All') <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                    <div class="row g-3">
                        @foreach ($latest_products as $product)
                            @include('includes.frontend.home_product', ['class' => 'col-6 col-md-4 col-xl-3'])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Banner -->
    <section class="esporim-cta">
        <div class="container">
            <h5>@lang('Your reliable supply partner')</h5>
            <h2>@lang('more than 2,500,000 products available and support worldwide')</h2>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
            </div>
        </div>
    </section>

    <!-- Trending Products -->
    @if (isset($trending_products) && $trending_products->count() > 0)
    <section class="gs-explore-product-section">
        <div class="container">
            <h3 class="esporim-section-title">@lang('Trending Products')</h3>
            <div class="row gy-4">
                @foreach ($trending_products as $product)
                    @include('includes.frontend.home_product', ['class' => 'col-6 col-md-4 col-lg-3 col-xl-2'])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Promotional Banners -->
    @if ($ps->arrival_section == 1)
    <section class="gs-offer-section">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6">
                    <a href="{{ $arrivals[0]['url'] }}">
                        <div class="single-offer-product verticle h-100">
                            <img class="promo-img" src="{{ asset('assets/images/arrival/' . $arrivals[0]['photo']) }}" alt="offer">
                        </div>
                    </a>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="product-wrapper">
                        <a href="{{ $arrivals[1]['url'] }}">
                            <div class="single-offer-product">
                                <img class="promo-img" src="{{ asset('assets/images/arrival/' . $arrivals[1]['photo']) }}" alt="offer">
                            </div>
                        </a>
                        <a href="{{ $arrivals[2]['url'] }}">
                            <div class="single-offer-product">
                                <img class="promo-img" src="{{ asset('assets/images/arrival/' . $arrivals[2]['photo']) }}" alt="offer">
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 d-lg-none">
                    <a href="{{ $arrivals[1]['url'] }}">
                        <div class="single-offer-product">
                            <img class="promo-img" src="{{ asset('assets/images/arrival/' . $arrivals[1]['photo']) }}" alt="offer">
                        </div>
                    </a>
                </div>
                <div class="col-md-6 d-lg-none">
                    <a href="{{ $arrivals[2]['url'] }}">
                        <div class="single-offer-product">
                            <img class="promo-img" src="{{ asset('assets/images/arrival/' . $arrivals[2]['photo']) }}" alt="offer">
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif
@endsection
