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
                                    <h6 class="subtitle" style="color:#fff">{{ $slider->subtitle_text }}</h6>
                                @endif
                                <h1 class="title" style="color:#fff">{{ $slider->title_text }}</h1>
                                @if ($slider->details_text)
                                    <p class="des" style="color:rgba(255,255,255,0.9)">{{ $slider->details_text }}</p>
                                @endif
                                <a class="template-btn hero-shop-now-btn mt-3" href="{{ $slider->link }}">
                                    @lang('Learn More')
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
                @else
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="esporim-feature-box">
                            <div class="feature-icon"><i class="fas fa-boxes"></i></div>
                            <div>
                                <h6>+2.500.000</h6>
                                <p>@lang('Products Managed')</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="esporim-feature-box">
                            <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                            <div>
                                <h6>@lang('Warranty')</h6>
                                <p>@lang('Manufacturer warranty')</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="esporim-feature-box">
                            <div class="feature-icon"><i class="fas fa-shipping-fast"></i></div>
                            <div>
                                <h6>@lang('Fast Delivery')</h6>
                                <p>@lang('Anytime, anywhere')</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="esporim-feature-box">
                            <div class="feature-icon"><i class="fas fa-lock"></i></div>
                            <div>
                                <h6>@lang('Safe & Fast')</h6>
                                <p>@lang('Delivery from anywhere in the world')</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Brands Section -->
    <section class="esporim-brands">
        <div class="container">
            <h3 class="esporim-section-title">@lang('Our Brands') ›</h3>
            <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6">
                @if (\Illuminate\Support\Facades\Schema::hasTable('brands'))
                    @foreach (\App\Models\Brand::all() as $data)
                        <div class="col">
                            <a href="{{ route('front.category') }}?brand={{ $data->id }}">
                                <div class="brand-item">
                                    <img src="{{ $data->image ? asset($data->image) : asset('assets/images/noimage.png') }}" alt="{{ $data->name }}">
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- Categories + Products Section -->
    <section class="gs-explore-product-section" style="background:var(--esporim-light-bg);">
        <div class="container">
            <h3 class="esporim-section-title">@lang('Categories')</h3>
            <div class="row">
                <!-- Category Sidebar -->
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="gs-product-sidebar-wrapper">
                        <div class="single-product-widget">
                            <div class="product-cat-widget">
                                <ul class="accordion" style="list-style:none;padding:0;">
                                    @foreach ($featured_categories as $fcategory)
                                        <li style="padding:6px 0;border-bottom:1px solid var(--esporim-border);">
                                            <a href="{{ route('front.category', $fcategory->slug) }}" style="color:var(--esporim-text);font-size:14px;text-decoration:none;">
                                                {{ $fcategory->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="col-lg-9">
                    <div class="row gy-4">
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
            <h3 class="esporim-section-title">@lang('Featured Products')</h3>
            <div class="row gy-4">
                @foreach ($trending_products as $product)
                    @include('includes.frontend.home_product', ['class' => 'col-6 col-md-4 col-lg-3 col-xl-2'])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Best Selling Products -->
    @if (isset($best_products) && $best_products->count() > 0)
    <section class="gs-explore-product-section" style="background:var(--esporim-light-bg);">
        <div class="container">
            <h3 class="esporim-section-title">@lang('Best Selling')</h3>
            <div class="product-cards-slider">
                @foreach ($best_products as $product)
                    @include('includes.frontend.home_product')
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Featured Products -->
    @if (isset($popular_products) && $popular_products->count() > 0)
    <section class="gs-explore-product-section">
        <div class="container">
            <h3 class="esporim-section-title">@lang('Recommended Products')</h3>
            <div class="product-cards-slider">
                @foreach ($popular_products as $product)
                    @include('includes.frontend.home_product')
                @endforeach
            </div>
        </div>
    </section>
    @endif

@endsection
