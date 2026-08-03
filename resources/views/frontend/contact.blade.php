@extends('layouts.front')
@section('content')
    <section class="gs-breadcrumb-section bg-class"
        data-background="{{ $gs->breadcrumb_banner ? asset('assets/images/' . $gs->breadcrumb_banner) : asset('assets/images/noimage.png') }}">
        <div class="container">
            <div class="row justify-content-center content-wrapper">
                <div class="col-12">
                    <h2 class="breadcrumb-title">@lang('Contact')</h2>
                    <ul class="bread-menu">
                        <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                        <li><a href="{{ route('front.contact') }}">@lang('Contact')</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>


    <div class="gs-contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 wow-replaced" data-wow-delay=".1s">
                    <div class="contact-information">

                        <h3>@lang('Get in Touch')</h3>

                        <div class="common-wrapper address d-flex align-items-center">
                            <div class="address-details details-wrapper">
                                <h5>@lang('Our Office Address')</h5>
                                <h6>Via Della Volta 37, Brescia 25124, Italy</h6>
                            </div>
                        </div>

                        <div class="email-address common-wrapper d-flex align-items-center">
                            <div class="details-wrapper">
                                <h5>@lang('Email Address')</h5>
                                <h6>{{ $ps->email ?: 'info@industrialmac.it' }}</h6>
                            </div>
                        </div>

                        <div class="ratio ratio-4x3 mt-4" style="border-radius:8px;overflow:hidden;">
                            <iframe
                                title="Industrialmac map"
                                src="https://www.google.com/maps?q=Via+Della+Volta+37,+25124+Brescia,+Italy&output=embed"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                style="border:0;width:100%;height:100%;"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 wow-replaced" data-wow-delay=".1s">
                    <div class="leave-reply-section">
                        @include('alerts.form-success')

                        <h3>@lang('Feel free to message us')</h3>
                        <p class="text-muted mb-4">
                            @lang('Cannot find the product in our catalog? Send us the brand, category, product name, SKU/code, and an optional image. We will get back to you with availability and pricing.')
                        </p>

                        @include('includes.frontend.quote-form', [
                            'categories' => $categories,
                            'brands' => $brands,
                            'submitLabel' => __('Send Message'),
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
