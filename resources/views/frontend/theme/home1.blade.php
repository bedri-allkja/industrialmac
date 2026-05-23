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
                        <a class="ignavo-hero__btn" href="{{ $slider->link ?: route('front.category') }}">
                            @lang('Search Product')
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>

                    <div class="ignavo-hero__benefits row g-3">
                        @if (\Illuminate\Support\Facades\Schema::hasTable('services'))
                            @foreach (\App\Models\Service::take(4)->get() as $service)
                                <div class="col-6 col-lg-3">
                                    <div class="ignavo-hero__benefit h-100">
                                        <div class="ignavo-hero__benefit-icon">
                                            <img src="{{ asset('assets/images/services/' . $service->photo) }}"
                                                alt="{{ $service->title }}">
                                        </div>
                                        <div>
                                            <h6>{{ $service->title }}</h6>
                                            <p>{{ $service->details }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-6 col-lg-3">
                                <div class="ignavo-hero__benefit h-100">
                                    <div class="ignavo-hero__benefit-icon"><i class="fas fa-shield-alt"></i></div>
                                    <div>
                                        <h6>@lang('Manufacturer warranty')</h6>
                                        <p>@lang('Genuine parts with manufacturer coverage')</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="ignavo-hero__benefit h-100">
                                    <div class="ignavo-hero__benefit-icon"><i class="fas fa-shipping-fast"></i></div>
                                    <div>
                                        <h6>@lang('Express delivery')</h6>
                                        <p>@lang('Always and everywhere')</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="ignavo-hero__benefit h-100">
                                    <div class="ignavo-hero__benefit-icon"><i class="fas fa-boxes"></i></div>
                                    <div>
                                        <h6>+1.000.000 @lang('Managed products')</h6>
                                        <p>@lang('Extensive industrial catalog')</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="ignavo-hero__benefit h-100">
                                    <div class="ignavo-hero__benefit-icon"><i class="fas fa-headset"></i></div>
                                    <div>
                                        <h6>@lang('Expert support')</h6>
                                        <p>@lang('We help you find the right part')</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 2. Brand logos row --}}
    <section class="ignavo-brands-strip">
        <div class="container">
            @php
                $hardcodedBrands = [
                    ['name' => 'Bosch', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/16/Bosch-logo.svg/960px-Bosch-logo.svg.png'],
                    ['name' => 'Denso', 'logo' => 'https://logowik.com/content/uploads/images/denso9853.jpg'],
                    ['name' => 'Brembo', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Brembo_logo_%282022%29.svg/500px-Brembo_logo_%282022%29.svg.png'],
                    ['name' => 'Hella', 'logo' => 'https://upload.wikimedia.org/wikipedia/en/thumb/8/86/Hella_logo.svg/250px-Hella_logo.svg.png'],
                    ['name' => 'Valeo', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Valeo_Logo.svg/500px-Valeo_Logo.svg.png'],
                    ['name' => 'Continental', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Continental_logo.svg/500px-Continental_logo.svg.png'],
                    ['name' => 'SKF', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/SKF-Logo.svg/500px-SKF-Logo.svg.png'],
                    ['name' => 'Philips', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/Philips_logo_new.svg/640px-Philips_logo_new.svg.png'],
                ];
            @endphp
            <div class="ignavo-brands-strip__row d-none d-md-flex">
                @foreach ($hardcodedBrands as $brand)
                    <div class="ignavo-brands-strip__item">
                        <img src="{{ $brand['logo'] }}" alt="{{ $brand['name'] }}">
                    </div>
                @endforeach
            </div>
            <div class="ignavo-brands-carousel d-md-none">
                @foreach ($hardcodedBrands as $brand)
                    <div class="ignavo-brands-carousel__slide">
                        <div class="ignavo-brands-strip__item">
                            <img src="{{ $brand['logo'] }}" alt="{{ $brand['name'] }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 3. Category quick links --}}
    @if ($featured_categories->count() > 0)
        <section class="ignavo-categories">
            <div class="container">
                <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-lg-6">
                    @foreach ($featured_categories->take(6) as $fcategory)
                        <div class="col">
                            <a href="{{ route('front.category', $fcategory->slug) }}" class="ignavo-categories__card">
                                <div class="ignavo-categories__icon">
                                    <img src="{{ asset('assets/images/categories/' . $fcategory->image) }}"
                                        alt="{{ $fcategory->name }}"
                                        onerror="this.onerror=null;this.src='{{ asset('assets/images/noimage.png') }}';">
                                </div>
                                <span>{{ $fcategory->name }}</span>
                            </a>
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
    @if ($ps->arrival_section == 1 && count($arrivals) >= 2)
        <section class="ignavo-dual-banners home-section">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <a href="{{ $arrivals[0]['url'] }}" class="ignavo-promo-banner ignavo-promo-banner--dark">
                            <img src="{{ asset('assets/images/arrival/' . $arrivals[0]['photo']) }}" alt="{{ $arrivals[0]['title'] }}">
                            <div class="ignavo-promo-banner__content">
                                <span class="ignavo-promo-banner__tag">{{ $arrivals[0]['up_sale'] }}</span>
                                <h3>@lang('Your Car Deserves the Best Parts')</h3>
                                <span class="ignavo-promo-banner__btn">@lang('Search Product')</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ $arrivals[1]['url'] }}" class="ignavo-promo-banner ignavo-promo-banner--light">
                            <img src="{{ asset('assets/images/arrival/' . $arrivals[1]['photo']) }}" alt="{{ $arrivals[1]['title'] }}">
                            <div class="ignavo-promo-banner__content">
                                <span class="ignavo-promo-banner__tag">{{ $arrivals[1]['up_sale'] }}</span>
                                <h3>@lang('Shop Smarter, Drive Stronger')</h3>
                                <span class="ignavo-promo-banner__btn">@lang('Shop Now')</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

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
                            @if ($ps->arrival_section == 1 && isset($arrivals[2]))
                                <img src="{{ asset('assets/images/arrival/' . $arrivals[2]['photo']) }}"
                                    alt="@lang('Featured promotion')">
                            @else
                                <img src="{{ asset('assets/images/container-ship.jpg') }}" alt="@lang('Featured promotion')">
                            @endif
                            <div class="ignavo-featured-grid__center-content">
                                <h3>@lang('Performance Starts Under the Hood')</h3>
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

    {{-- 11. Three feature cards --}}
    <section class="ignavo-info-cards home-section">
        <div class="container">
            <div class="row g-4">
                @php
                    $infoCards = [
                        [
                            'title' => __('Search by Make, Model & More'),
                            'image' => $featured_categories->first()->image ?? null,
                            'link' => route('front.category'),
                        ],
                        [
                            'title' => __('Quality Parts You Can Trust'),
                            'image' => $featured_categories->skip(1)->first()->image ?? null,
                            'link' => route('front.category'),
                        ],
                        [
                            'title' => __('Fast Delivery Worldwide'),
                            'image' => $featured_categories->skip(2)->first()->image ?? null,
                            'link' => route('front.contact'),
                        ],
                    ];
                @endphp
                @foreach ($infoCards as $card)
                    <div class="col-lg-4 col-md-6">
                        <a href="{{ $card['link'] }}" class="ignavo-info-card">
                            <img src="{{ $card['image'] ? asset('assets/images/categories/' . $card['image']) : asset('assets/images/noimage.png') }}"
                                alt="{{ $card['title'] }}">
                            <div class="ignavo-info-card__content">
                                <h4>{{ $card['title'] }}</h4>
                                <span>@lang('Shop Now')</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

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
    @if ($ps->faq == 1 && isset($faqs) && $faqs->count() > 0)
        <section class="ignavo-faq home-section">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-5">
                        <div class="ignavo-faq__media">
                            <img src="{{ asset('assets/images/blogs/1730868130customer-speaks-with-consultant-auto-parts-store1-minjpg.jpg') }}"
                                alt="@lang('FAQ')"
                                onerror="this.onerror=null;this.src='{{ asset('assets/images/noimage.png') }}';">
                            <div class="ignavo-faq__media-content">
                                <h3>@lang('Questions you may be curious about')</h3>
                                <a href="{{ route('front.faq') }}" class="template-btn">@lang('View All FAQs')</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="accordion ignavo-faq__accordion" id="homeFaqList">
                            @foreach ($faqs as $key => $faq)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#home-faq-{{ $key }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                            {{ $faq->title }}
                                        </button>
                                    </h2>
                                    <div id="home-faq-{{ $key }}"
                                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                        data-bs-parent="#homeFaqList">
                                        <div class="accordion-body">
                                            {!! clean($faq->details, ['Attr.EnableID' => true]) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
@endsection
