@extends('layouts.front')

@section('css')
    <style>
            .ignavo-brands-strip__heading {
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: var(--accent, #2e86c1);
                text-align: center;
                margin: 0 0 16px;
                padding-bottom: 10px;
                border-bottom: 2px solid var(--accent, #f1c40f);
                display: inline-block;
                width: 100%;
            }

            .ignavo-brands-strip__item-link {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 100%;
                text-decoration: none;
            }

            .ignavo-how-we-work {
                background: var(--light-bg, #f4f6f9);
            }

            .ignavo-how-we-work__intro {
                text-align: center;
                max-width: 640px;
                margin: 0 auto 36px;
            }

            .ignavo-how-we-work__intro h2 {
                font-size: clamp(22px, 2.5vw, 30px);
                font-weight: 800;
                margin-bottom: 8px;
                color: var(--text, #2c3e50);
            }

            .ignavo-how-we-work__intro p {
                margin: 0;
                color: var(--text-muted, #666);
            }

            .ignavo-how-we-work__grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }

            @media (max-width: 991px) {
                .ignavo-how-we-work__grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 575px) {
                .ignavo-how-we-work__grid {
                    grid-template-columns: 1fr;
                }
            }

            .ignavo-how-we-work__step {
                position: relative;
                overflow: hidden;
                min-height: 280px;
                border-radius: 12px;
                color: #fff;
                text-align: left;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                padding: 22px 20px;
                background-color: #102a52;
                background-size: cover;
                background-position: center;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .ignavo-how-we-work__step::before {
                content: "";
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, rgba(16,42,82,0.35) 0%, rgba(16,42,82,0.88) 70%);
                transition: background 0.35s ease;
                z-index: 0;
            }

            .ignavo-how-we-work__step:hover {
                transform: translateY(-6px);
                box-shadow: 0 16px 32px rgba(16, 42, 82, 0.22);
            }

            .ignavo-how-we-work__step:hover::before {
                background: linear-gradient(180deg, rgba(16,42,82,0.15) 0%, rgba(16,42,82,0.78) 55%);
            }

            .ignavo-how-we-work__step > * {
                position: relative;
                z-index: 1;
            }

            .ignavo-how-we-work__icon {
                width: 48px;
                height: 48px;
                margin: 0 0 12px;
                border-radius: 50%;
                background: rgba(255,255,255,0.18);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                transition: transform 0.3s ease, background 0.3s ease;
            }

            .ignavo-how-we-work__step:hover .ignavo-how-we-work__icon {
                transform: scale(1.08);
                background: var(--accent, #2e86c1);
            }

            .ignavo-how-we-work__num {
                display: block;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.08em;
                color: #f1c40f;
                margin-bottom: 6px;
            }

            .ignavo-how-we-work__step h4 {
                font-size: 17px;
                font-weight: 700;
                margin-bottom: 8px;
                color: #fff;
            }

            .ignavo-how-we-work__step p {
                font-size: 13px;
                line-height: 1.5;
                color: rgba(255,255,255,0.9);
                margin: 0;
            }

            .ignavo-how-we-work__closing {
                text-align: center;
                margin-top: 32px;
                font-size: 15px;
                font-weight: 600;
                color: var(--text, #2c3e50);
            }

            .ignavo-partner__intro {
                max-width: 760px;
                margin-bottom: 32px;
            }

            .ignavo-partner__intro h2 {
                font-size: clamp(22px, 2.5vw, 30px);
                font-weight: 800;
                margin-bottom: 10px;
                color: var(--text, #2c3e50);
            }

            .ignavo-partner__intro h3 {
                font-size: clamp(18px, 2vw, 24px);
                font-weight: 700;
                margin-bottom: 12px;
                color: var(--text, #2c3e50);
            }

            .ignavo-partner__intro p {
                margin: 0;
                color: var(--text-muted, #666);
                line-height: 1.6;
            }

            .ignavo-partner__cards {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }

            @media (max-width: 991px) {
                .ignavo-partner__cards {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 575px) {
                .ignavo-partner__cards {
                    grid-template-columns: 1fr;
                }
            }

            .ignavo-partner__card {
                background: #fff;
                border: 1px solid var(--border-light, #ecf0f1);
                border-radius: 12px;
                padding: 24px 20px;
                transition: transform 0.25s ease, box-shadow 0.25s ease;
            }

            .ignavo-partner__card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 24px rgba(16, 42, 82, 0.08);
            }

            .ignavo-partner__card-icon {
                font-size: 28px;
                color: var(--accent, #2e86c1);
                margin-bottom: 12px;
            }

            .ignavo-partner__card h4 {
                font-size: 16px;
                font-weight: 700;
                margin-bottom: 8px;
                color: var(--text, #2c3e50);
            }

            .ignavo-partner__card p {
                font-size: 14px;
                color: var(--text-muted, #666);
                margin: 0;
                line-height: 1.5;
            }

            .ignavo-trusted-stats {
                background: var(--primary, #1a5276);
                color: #fff;
            }

            .ignavo-trusted-stats .ignavo-section-title {
                color: #fff;
            }

            .ignavo-trusted-stats__grid {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                gap: 20px;
            }

            @media (max-width: 991px) {
                .ignavo-trusted-stats__grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 575px) {
                .ignavo-trusted-stats__grid {
                    grid-template-columns: 1fr;
                }
            }

            .ignavo-trusted-stats__item {
                text-align: center;
                padding: 20px 12px;
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 10px;
                transition: background 0.25s ease, transform 0.25s ease;
            }

            .ignavo-trusted-stats__item:hover {
                background: rgba(255, 255, 255, 0.08);
                transform: scale(1.03);
            }

            .ignavo-trusted-stats__value {
                display: block;
                font-size: clamp(22px, 2.5vw, 28px);
                font-weight: 800;
                margin-bottom: 6px;
            }

            .ignavo-trusted-stats__label {
                font-size: 13px;
                line-height: 1.4;
                opacity: 0.9;
            }
    </style>
@endsection

@section('content')
    {{-- 1. Hero: hardcoded industrial messaging + search --}}
    <section class="ignavo-hero"
        style="--hero-bg: url('{{ asset('assets/images/Depositphotos_671304024_XL.jpg') }}');">
        <div class="container">
            <div class="ignavo-hero__layout">
                <div class="ignavo-hero__content">
                    <span class="ignavo-hero__eyebrow">Your Global Partner for Industrial Parts &amp; Automation</span>
                    <h1 class="ignavo-hero__title">Keeping Industry Moving</h1>
                    <p class="ignavo-hero__desc">
                        Reliable sourcing of industrial spare parts, automation components, MRO supplies and industrial equipment from trusted global manufacturers. All through a single sourcing partner — delivered worldwide.
                    </p>
                    <div class="ignavo-hero__actions">
                        <a class="ignavo-hero__btn" href="{{ route('front.category') }}">
                            @lang('Search Product')
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                        @include('includes.frontend.product-search-form', ['class' => 'ignavo-hero-search d-none d-lg-flex'])
                    </div>
                </div>

                @include('includes.frontend.hero-benefits', ['class' => 'd-none d-lg-flex'])
            </div>
        </div>
    </section>

    {{-- 2. Brand logos row (ranked by most-viewed / clicked products) --}}
    @if (isset($featured_brands) && $featured_brands->count() > 0)
        @php
            $brandPageUrl = fn ($brand) => ! empty($brand->slug)
                ? route('front.category', $brand->slug)
                : route('front.category') . '?brand=' . $brand->id;
        @endphp
        <section class="ignavo-brands-strip">
            <div class="container">
                <p class="ignavo-brands-strip__heading">INDUSTRIALMAC – GLOBAL BRAND NETWORK</p>
                <div class="ignavo-brands-strip__row d-none d-md-flex">
                    @foreach ($featured_brands as $brand)
                        @php $logoUrl = brand_logo_url($brand); @endphp
                        @continue(!$logoUrl)
                        <div class="ignavo-brands-strip__item">
                            <a href="{{ $brandPageUrl($brand) }}" class="ignavo-brands-strip__item-link" title="{{ $brand->name }}">
                                <img src="{{ $logoUrl }}" alt="{{ $brand->name }}"
                                    onerror="this.onerror=null;this.closest('.ignavo-brands-strip__item')?.remove();">
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="ignavo-brands-carousel d-md-none">
                    @foreach ($featured_brands as $brand)
                        @php $logoUrl = brand_logo_url($brand); @endphp
                        @continue(!$logoUrl)
                        <div class="ignavo-brands-carousel__slide">
                            <div class="ignavo-brands-strip__item">
                                <a href="{{ $brandPageUrl($brand) }}" class="ignavo-brands-strip__item-link" title="{{ $brand->name }}">
                                    <img src="{{ $logoUrl }}" alt="{{ $brand->name }}"
                                        onerror="this.onerror=null;this.closest('.ignavo-brands-carousel__slide')?.remove();">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 3. Top Selling products carousel --}}
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

    {{-- 4. Dual promotional banners --}}
    <section class="ignavo-dual-banners home-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <a href="{{ route('front.categories') }}" class="ignavo-promo-banner ignavo-promo-banner--dark">
                        <img src="{{ asset('assets/images/container-ship.jpg') }}"
                            alt="@lang('Global industrial logistics and supply chain')"
                            onerror="this.onerror=null;this.src='{{ asset('assets/images/Depositphotos_671304024_XL.jpg') }}';">
                        <div class="ignavo-promo-banner__content">
                            <span class="ignavo-promo-banner__tag">@lang('Industrial Components')</span>
                            <h3>@lang('Quality Parts for Your Operations')</h3>
                            <span class="ignavo-promo-banner__btn">@lang('Browse Catalog')</span>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6">
                    <a href="{{ route('front.quote') }}" class="ignavo-promo-banner ignavo-promo-banner--light">
                        <img src="{{ asset('assets/images/blogs/1730868152organized-automotive-workshop-interior-minjpg.jpg') }}"
                            alt="@lang('Industrial facility and automation equipment')"
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

    {{-- 5. How We Work (Word checklist copy + hover process images) --}}
    @php
        $howWeWorkSteps = [
            [
                'num' => '01',
                'icon' => 'fa-search',
                'title' => 'Tell Us What You Need',
                'text' => 'Send us a part number, product description, technical drawing, photo, or simply describe your requirement. We carefully review every request to fully understand your application.',
                'image' => 'how-we-work/step-01.jpg',
                'fallback' => 'Depositphotos_671304024_XL.jpg',
            ],
            [
                'num' => '02',
                'icon' => 'fa-globe',
                'title' => 'We Find the Right Solution',
                'text' => 'Our specialists search our international supplier network to identify the most suitable product, alternative, or equivalent. Our goal is always the best combination of quality, availability, and price.',
                'image' => 'how-we-work/step-02.jpg',
                'fallback' => 'container-ship.jpg',
            ],
            [
                'num' => '03',
                'icon' => 'fa-file-alt',
                'title' => 'Receive Your Quotation',
                'text' => 'You receive a clear and detailed quotation including pricing, availability, delivery time, and technical information. No hidden surprises.',
                'image' => 'how-we-work/step-03.jpg',
                'fallback' => 'blogs/1730868152organized-automotive-workshop-interior-minjpg.jpg',
            ],
            [
                'num' => '04',
                'icon' => 'fa-shield-alt',
                'title' => 'Order & Quality Verification',
                'text' => 'Once your order is confirmed, we coordinate sourcing, verify product specifications, and manage every stage of the procurement process. Quality comes before delivery.',
                'image' => 'how-we-work/step-04.jpg',
                'fallback' => 'blogs/1730868175selecting-new-equipment-man-is-hardware-shop-minjpg.jpg',
            ],
            [
                'num' => '05',
                'icon' => 'fa-truck',
                'title' => 'Worldwide Delivery',
                'text' => 'Your products are carefully prepared and shipped through reliable logistics partners to your facility, wherever your business operates. Fast, secure and fully tracked.',
                'image' => 'how-we-work/step-05.jpg',
                'fallback' => 'container-ship.jpg',
            ],
            [
                'num' => '06',
                'icon' => 'fa-headset',
                'title' => 'Ongoing Technical Support',
                'text' => 'Our relationship doesn\'t end with delivery. Whether you need technical assistance, replacement parts, alternative solutions, or support for future projects, our team is always available.',
                'image' => 'how-we-work/step-06.jpg',
                'fallback' => 'Depositphotos_671304024_XL.jpg',
            ],
        ];
    @endphp
    <section class="ignavo-how-we-work home-section">
        <div class="container">
            <div class="ignavo-how-we-work__intro">
                <h2>HOW WE WORK</h2>
                <p><strong>A Simple Process. Reliable Results.</strong></p>
                <p class="mt-2">From your request to the final delivery, we simplify every step of your industrial sourcing journey. Our team manages the entire process, so you can stay focused on your business.</p>
            </div>
            <div class="ignavo-how-we-work__grid">
                @foreach ($howWeWorkSteps as $step)
                    @php
                        $stepImg = public_path('assets/images/' . $step['image']);
                        $bg = asset('assets/images/' . (is_file($stepImg) ? $step['image'] : $step['fallback']));
                    @endphp
                    <div class="ignavo-how-we-work__step" style="background-image: url('{{ $bg }}');">
                        <div class="ignavo-how-we-work__icon"><i class="fas {{ $step['icon'] }}" aria-hidden="true"></i></div>
                        <span class="ignavo-how-we-work__num">{{ $step['num'] }}</span>
                        <h4>{{ $step['title'] }}</h4>
                        <p>{{ $step['text'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="ignavo-how-we-work__closing">
                <strong>Your Success Is Our Process</strong>
                <p class="mt-2 mb-0 text-muted">From a single spare part to complete industrial sourcing projects, Industrialmac delivers reliable solutions with speed, expertise, and a customer-first approach.</p>
            </div>
        </div>
    </section>

    {{-- 6. Your Global Industrial Partner --}}
    <section class="ignavo-partner home-section">
        <div class="container">
            <div class="ignavo-partner__intro">
                <h2>YOUR GLOBAL INDUSTRIAL PARTNER</h2>
                <h3>Industrial Components, Automation &amp; Spare Parts for Every Industry</h3>
                <p>
                    Industrialmac connects manufacturers, maintenance teams and automation engineers with hard-to-find industrial parts from leading global brands. From PLC modules and sensors to MRO supplies and complete automation systems, we deliver professional B2B sourcing with technical expertise and worldwide logistics.
                </p>
            </div>
            <div class="ignavo-partner__cards">
                <div class="ignavo-partner__card">
                    <div class="ignavo-partner__card-icon"><i class="fas fa-globe" aria-hidden="true"></i></div>
                    <h4>Global Sourcing</h4>
                    <p>Access thousands of industrial brands through one trusted partner with international supplier relationships.</p>
                </div>
                <div class="ignavo-partner__card">
                    <div class="ignavo-partner__card-icon"><i class="fas fa-cogs" aria-hidden="true"></i></div>
                    <h4>Technical Expertise</h4>
                    <p>Experienced team that understands part numbers, cross-references and application requirements.</p>
                </div>
                <div class="ignavo-partner__card">
                    <div class="ignavo-partner__card-icon"><i class="fas fa-bolt" aria-hidden="true"></i></div>
                    <h4>Fast Quotations</h4>
                    <p>Quick turnaround on RFQs so your production lines and maintenance schedules stay on track.</p>
                </div>
                <div class="ignavo-partner__card">
                    <div class="ignavo-partner__card-icon"><i class="fas fa-truck" aria-hidden="true"></i></div>
                    <h4>Worldwide Delivery</h4>
                    <p>Reliable shipping to industrial facilities across 50+ countries with tracked, secure logistics.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. Blue help CTA bar --}}
    <section class="ignavo-help-bar">
        <div class="container">
            <div class="ignavo-help-bar__inner">
                <h4>@lang('Need Help Finding the Right Product?')</h4>
                <div class="ignavo-help-bar__actions">
                    <a href="{{ route('front.quote') }}" class="ignavo-help-bar__btn ignavo-help-bar__btn--outline">
                        @lang('Request Quote')
                    </a>
                    <a href="{{ route('front.contact') }}" class="ignavo-help-bar__btn">
                        @lang('Contact')
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 8. Featured product grid with central promo --}}
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

    {{-- 9. Image + contact form split --}}
    @if ($ps->contact == 1)
        <section class="ignavo-contact-split home-section">
            <div class="container-fluid px-0">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="ignavo-contact-split__media">
                            <img src="{{ asset('assets/images/blogs/1730868175selecting-new-equipment-man-is-hardware-shop-minjpg.jpg') }}"
                                alt="@lang('Industrial facility with automation and manufacturing equipment')"
                                onerror="this.onerror=null;this.src='{{ asset('assets/images/container-ship.jpg') }}';">
                            <div class="ignavo-contact-split__media-overlay">
                                <h3>@lang('Supporting Industry Worldwide')</h3>
                                <a href="{{ route('front.quote') }}" class="template-btn">@lang('Request Quote')</a>
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

    {{-- 10. Blue help CTA bar (repeat) --}}
    <section class="ignavo-help-bar">
        <div class="container">
            <div class="ignavo-help-bar__inner">
                <h4>@lang('Need Help Finding the Right Product?')</h4>
                <div class="ignavo-help-bar__actions">
                    <a href="{{ route('front.quote') }}" class="ignavo-help-bar__btn ignavo-help-bar__btn--outline">
                        @lang('Request Quote')
                    </a>
                    <a href="{{ route('front.contact') }}" class="ignavo-help-bar__btn">
                        @lang('Contact')
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 11. FAQ section --}}
    @if ($ps->faq == 1)
        @php $faqContent = site_faq_content(); @endphp
        <section class="ignavo-faq home-section">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-5">
                        <div class="ignavo-faq__media">
                            <img src="{{ asset('assets/images/blogs/1730868152organized-automotive-workshop-interior-minjpg.jpg') }}"
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

    {{-- 12. Trusted by stats (replaces Articles & News) --}}
    <section class="ignavo-trusted-stats home-section">
        <div class="container">
            <div class="ignavo-section-head justify-content-center mb-4">
                <h3 class="ignavo-section-title text-center">Trusted by Industrial Companies Worldwide</h3>
            </div>
            <div class="ignavo-trusted-stats__grid">
                <div class="ignavo-trusted-stats__item">
                    <span class="ignavo-trusted-stats__value">1,000+</span>
                    <span class="ignavo-trusted-stats__label">Industrial Brands Available</span>
                </div>
                <div class="ignavo-trusted-stats__item">
                    <span class="ignavo-trusted-stats__value">1,000,000+</span>
                    <span class="ignavo-trusted-stats__label">Products Sourced</span>
                </div>
                <div class="ignavo-trusted-stats__item">
                    <span class="ignavo-trusted-stats__value">50+</span>
                    <span class="ignavo-trusted-stats__label">Countries Served</span>
                </div>
                <div class="ignavo-trusted-stats__item">
                    <span class="ignavo-trusted-stats__value">24 Hours</span>
                    <span class="ignavo-trusted-stats__label">Average Quotation Time</span>
                </div>
                <div class="ignavo-trusted-stats__item">
                    <span class="ignavo-trusted-stats__value">B2B Only</span>
                    <span class="ignavo-trusted-stats__label">Professional Industrial Supply</span>
                </div>
            </div>
        </div>
    </section>

    {{-- 13. Newsletter --}}
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
