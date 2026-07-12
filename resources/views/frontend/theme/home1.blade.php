@extends('layouts.front')

@section('content')
    {{-- 1. Hero: background image with content + value props --}}
    <section class="ignavo-hero"
        style="--hero-bg: url('{{ asset('assets/images/Depositphotos_671304024_XL.jpg') }}');">
        <div class="container">
            @foreach ($sliders->take(1) as $slider)
                <div class="ignavo-hero__layout">
                    <div class="ignavo-hero__content">
                        @if ($slider->subtitle_text)
                            <span class="ignavo-hero__eyebrow">{{ $slider->subtitle_text }}</span>
                        @endif
                        <h1 class="ignavo-hero__title">{{ $slider->title_text }}</h1>
                        @if ($slider->details_text)
                            <p class="ignavo-hero__desc">{{ $slider->details_text }}</p>
                        @endif
                        <div class="ignavo-hero__actions">
                            <a class="ignavo-hero__btn" href="{{ $slider->link ?: route('front.category') }}">
                                @lang('Search Product')
                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                            @include('includes.frontend.product-search-form', ['class' => 'ignavo-hero-search d-none d-lg-flex'])
                        </div>
                    </div>

                    @include('includes.frontend.hero-benefits', ['class' => 'd-none d-lg-flex'])
                </div>
            @endforeach
        </div>
    </section>

    {{-- 2. Brand logos row --}}
    @if (isset($featured_brands) && $featured_brands->count() > 0)
        @php
            // Only brands with a hardcoded official logo URL are shown in this strip.
            $hardcodedBrandLogos = [
                'abb' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/ABB_logo.svg/960px-ABB_logo.svg.png?_=20181028032939',
                'allen-bradley' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1c/Allen-Bradley_logo.svg/1280px-Allen-Bradley_logo.svg.png',
                'euchner' => 'https://www.euchner.com/en-us/wp-content/themes/euchner/img/logo.png',
                'baumer' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Baumer_Logo.svg/960px-Baumer_Logo.svg.png',
                'murrelektronik' => 'https://upload.wikimedia.org/wikipedia/commons/a/a9/Murrelektronik.svg',
                'lenze' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT9WwpAXdDqkJriCVr6-JUfAeLO3ucqP3Q_bNwcBgMxew&s=10',
            ];
            $homeBrandLogo = fn ($brand) => $hardcodedBrandLogos[$brand->slug] ?? null;
        @endphp
        <section class="ignavo-brands-strip">
            <div class="container">
                <div class="ignavo-brands-strip__row d-none d-md-flex">
                    @foreach ($featured_brands as $brand)
                        @continue(! $homeBrandLogo($brand))
                        <div class="ignavo-brands-strip__item">
                            <img src="{{ $homeBrandLogo($brand) }}" alt="{{ $brand->name }}"
                                onerror="this.onerror=null;this.closest('.ignavo-brands-strip__item')?.remove();">
                        </div>
                    @endforeach
                </div>
                <div class="ignavo-brands-carousel d-md-none">
                    @foreach ($featured_brands as $brand)
                        @continue(! $homeBrandLogo($brand))
                        <div class="ignavo-brands-carousel__slide">
                            <div class="ignavo-brands-strip__item">
                                <img src="{{ $homeBrandLogo($brand) }}" alt="{{ $brand->name }}"
                                    onerror="this.onerror=null;this.closest('.ignavo-brands-carousel__slide')?.remove();">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 4. Top Selling products carousel --}}
    @if (isset($best_products) && $best_products->count() > 0)
        <section class="ignavo-products home-section">
            <div class="container">
                <div class="ignavo-section-head">
                    <h3 class="ignavo-section-title">@lang('Top Selling')</h3>
                    <a href="{{ route('front.category') }}" class="view-all-link">
                        @lang('View All') <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="product-cards-slider">
                    @foreach ($best_products as $product)
                        @include('includes.frontend.home_product')
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 5. Dual promotional banners --}}
    <section class="ignavo-dual-banners home-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <a href="{{ route('front.categories') }}" class="ignavo-promo-banner ignavo-promo-banner--dark">
                        <img src="{{ asset('assets/images/blogs/1730868192various-repair-tools-sale-hardware-store-showcase-minjpg.jpg') }}"
                            alt="@lang('Industrial components and hardware')"
                            onerror="this.onerror=null;this.src='{{ asset('assets/images/container-ship.jpg') }}';">
                        <div class="ignavo-promo-banner__content">
                            <span class="ignavo-promo-banner__tag">@lang('Industrial Components')</span>
                            <h3>@lang('Quality Parts for Your Operations')</h3>
                            <span class="ignavo-promo-banner__btn">@lang('Browse Catalog')</span>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6">
                    <a href="{{ route('front.quote') }}" class="ignavo-promo-banner ignavo-promo-banner--light">
                        <img src="{{ asset('assets/images/blogs/1730868130customer-speaks-with-consultant-auto-parts-store1-minjpg.jpg') }}"
                            alt="@lang('Expert support for industrial parts')"
                            onerror="this.onerror=null;this.src='{{ asset('assets/images/container-ship.jpg') }}';">
                        <div class="ignavo-promo-banner__content">
                            <span class="ignavo-promo-banner__tag">@lang('Expert Support')</span>
                            <h3>@lang('Need a Part? We Will Find It for You')</h3>
                            <span class="ignavo-promo-banner__btn">@lang('Request Quote')</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. Blue help CTA bar --}}
    <section class="ignavo-help-bar">
        <div class="container">
            <div class="ignavo-help-bar__inner">
                <h4>@lang('Need Help Finding the Right Product?')</h4>
                <div class="ignavo-help-bar__actions">
                    <a href="{{ route('front.quote') }}" class="ignavo-help-bar__btn ignavo-help-bar__btn--outline">
                        @lang('Request Quote')
                    </a>
                    <a href="tel:{{ $ps->phone }}" class="ignavo-help-bar__btn">
                        <i class="fas fa-phone-alt me-2"></i>{{ $ps->phone }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. Featured product grid with central promo --}}
    @if (isset($popular_products) && $popular_products->count() >= 4)
        @php $gridProducts = $popular_products->take(8); @endphp
        <section class="ignavo-featured-grid home-section">
            <div class="container">
                <div class="row g-3 align-items-stretch">
                    <div class="col-lg-3 d-none d-lg-flex flex-column gap-3">
                        @foreach ($gridProducts->take(2) as $product)
                            @include('includes.frontend.home_product', ['class' => 'col-12'])
                        @endforeach
                    </div>
                    <div class="col-lg-6">
                        <div class="ignavo-featured-grid__center">
                            <img src="{{ asset('assets/images/container-ship.jpg') }}"
                                alt="@lang('Global industrial parts supply')">
                            <div class="ignavo-featured-grid__center-content">
                                <h3>@lang('Reliable Supply for Industry Worldwide')</h3>
                                <a href="{{ route('front.category') }}" class="template-btn">@lang('Shop Now')</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 d-none d-lg-flex flex-column gap-3">
                        @foreach ($gridProducts->slice(2, 2) as $product)
                            @include('includes.frontend.home_product', ['class' => 'col-12'])
                        @endforeach
                    </div>
                </div>
                <div class="row g-3 mt-1 d-none d-lg-flex">
                    @foreach ($gridProducts->slice(4, 4) as $product)
                        @include('includes.frontend.home_product', ['class' => 'col-lg-3 col-md-6'])
                    @endforeach
                </div>
                <div class="row g-3 mt-1 d-lg-none">
                    @foreach ($gridProducts as $product)
                        @include('includes.frontend.home_product', ['class' => 'col-6 col-md-4'])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 8. Discount promo strip --}}
    <section class="ignavo-discount-strip">
        <div class="container">
            <div class="ignavo-discount-strip__inner">
                <span class="ignavo-discount-strip__badge">-35%</span>
                <p>@lang('Super discount for your first purchase')</p>
                <a href="{{ route('front.category') }}" class="ignavo-discount-strip__link">
                    @lang('Shop Now') <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- 9. Image + contact form split --}}
    @if ($ps->contact == 1)
        <section class="ignavo-contact-split home-section">
            <div class="container-fluid px-0">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="ignavo-contact-split__media">
                            <img src="{{ asset('assets/images/blogs/1730868130customer-speaks-with-consultant-auto-parts-store1-minjpg.jpg') }}"
                                alt="@lang('Performance Starts Under the Hood')"
                                onerror="this.onerror=null;this.src='{{ asset('assets/images/noimage.png') }}';">
                            <div class="ignavo-contact-split__media-overlay">
                                <h3>@lang('Performance Starts Under the Hood')</h3>
                                <a href="{{ route('front.category') }}" class="template-btn">@lang('Shop Now')</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="ignavo-contact-split__form">
                            <h3>@lang('Request a quote')</h3>
                            <p class="text-muted small mb-3">
                                @lang('Product not in our catalog? Tell us what you need and we will prepare a quote.')
                            </p>
                            @include('alerts.form-success')
                            @include('includes.frontend.quote-form', ['compact' => true])
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- 10. Best offers carousel --}}
    @php
        $offerProducts = ($sale_products ?? collect())->count() > 0
            ? $sale_products
            : (($trending_products ?? collect())->count() > 0 ? $trending_products : ($latest_products ?? collect()));
    @endphp
    @if ($offerProducts->count() > 0)
        <section class="ignavo-products home-section">
            <div class="container">
                <div class="ignavo-section-head">
                    <h3 class="ignavo-section-title">@lang('Our Best Offers')</h3>
                    <a href="{{ route('front.category') }}" class="view-all-link">
                        @lang('View All') <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="product-cards-slider">
                    @foreach ($offerProducts as $product)
                        @include('includes.frontend.home_product')
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 12. Blue help CTA bar (repeat) --}}
    <section class="ignavo-help-bar">
        <div class="container">
            <div class="ignavo-help-bar__inner">
                <h4>@lang('Need Help Finding the Right Product?')</h4>
                <div class="ignavo-help-bar__actions">
                    <a href="{{ route('front.quote') }}" class="ignavo-help-bar__btn ignavo-help-bar__btn--outline">
                        @lang('Request Quote')
                    </a>
                    <a href="tel:{{ $ps->phone }}" class="ignavo-help-bar__btn">
                        <i class="fas fa-phone-alt me-2"></i>{{ $ps->phone }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 13. FAQ section --}}
    @if ($ps->faq == 1)
        @php $faqContent = site_faq_content(); @endphp
        <section class="ignavo-faq home-section">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-5">
                        <div class="ignavo-faq__media">
                            <img src="{{ asset('assets/images/blogs/1730868130customer-speaks-with-consultant-auto-parts-store1-minjpg.jpg') }}"
                                alt="{{ $faqContent['page_title'] }}"
                                onerror="this.onerror=null;this.src='{{ asset('assets/images/noimage.png') }}';">
                            <div class="ignavo-faq__media-content">
                                <h3>{{ $faqContent['home_title'] }}</h3>
                                <a href="{{ route('front.faq') }}" class="template-btn">{{ $faqContent['view_all'] }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        @include('includes.frontend.faq-accordion', [
                            'faqs' => site_faqs(4),
                            'accordionId' => 'homeFaqList',
                            'accordionClass' => 'ignavo-faq__accordion',
                            'itemClass' => '',
                        ])
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- 14. Articles & News --}}
    @if ($ps->blog == 1 && isset($blogs) && $blogs->count() > 0)
        <section class="ignavo-blog home-section">
            <div class="container">
                <div class="ignavo-section-head">
                    <h3 class="ignavo-section-title">@lang('Articles & News')</h3>
                    <a href="{{ route('front.blog') }}" class="view-all-link">
                        @lang('View All') <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="row g-4">
                    @foreach ($blogs as $blog)
                        <div class="col-lg-4 col-md-6">
                            <article class="ignavo-blog-card">
                                <a href="{{ route('front.blogshow', $blog->slug) }}" class="ignavo-blog-card__image">
                                    <img src="{{ asset('assets/images/blogs/' . $blog->photo) }}" alt="{{ $blog->title }}">
                                </a>
                                <div class="ignavo-blog-card__body">
                                    <span class="ignavo-blog-card__date">{{ date('d M, Y', strtotime($blog->created_at)) }}</span>
                                    <h5>
                                        <a href="{{ route('front.blogshow', $blog->slug) }}">
                                            {{ mb_strlen($blog->title, 'UTF-8') > 80 ? mb_substr($blog->title, 0, 80, 'UTF-8') . '...' : $blog->title }}
                                        </a>
                                    </h5>
                                    <p>
                                        {{ mb_strlen(strip_tags($blog->details), 'UTF-8') > 120
                                            ? mb_substr(strip_tags($blog->details), 0, 120, 'UTF-8') . '...'
                                            : strip_tags($blog->details) }}
                                    </p>
                                    <a href="{{ route('front.blogshow', $blog->slug) }}" class="ignavo-blog-card__read">
                                        @lang('Read More')
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 15. Newsletter --}}
    @if ($ps->newsletter == 1)
        <section class="ignavo-newsletter">
            <div class="container">
                <div class="ignavo-newsletter__inner">
                    <div class="ignavo-newsletter__text">
                        <h4>@lang('Join the Industrialmac Club!')</h4>
                        <p>@lang('Sign up to newslatter')</p>
                    </div>
                    <form action="{{ route('front.subscribe') }}" method="POST" class="ignavo-newsletter__form">
                        @csrf
                        <input type="email" name="email" placeholder="@lang('Enter your email')" required>
                        <button type="submit" class="ignavo-newsletter__btn">@lang('Subscribe')</button>
                    </form>
                </div>
            </div>
        </section>
    @endif

    {{-- Hero benefit cards (mobile only — shown before footer) --}}
    <section class="ignavo-hero-benefits-mobile d-lg-none">
        <div class="container">
            @include('includes.frontend.hero-benefits')
        </div>
    </section>
@endsection
