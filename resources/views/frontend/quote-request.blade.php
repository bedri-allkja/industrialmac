@extends('layouts.front')

@section('content')
    <section class="gs-breadcrumb-section bg-class"
        data-background="{{ $gs->breadcrumb_banner ? asset('assets/images/' . $gs->breadcrumb_banner) : asset('assets/images/noimage.png') }}">
        <div class="container">
            <div class="row justify-content-center content-wrapper">
                <div class="col-12">
                    <h2 class="breadcrumb-title">@lang('Request Quote')</h2>
                    <ul class="bread-menu">
                        <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                        <li><a href="{{ route('front.quote') }}">@lang('Request Quote')</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <div class="gs-contact-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    @include('alerts.form-success')

                    <div class="leave-reply-section">
                        @if ($product)
                            <h3>@lang('Request a quote for this product')</h3>
                            <p class="text-muted mb-4">
                                @lang('Fill in your details and our team will prepare a quote for you.')
                            </p>
                        @else
                            <h3>@lang('Request a quote')</h3>
                            <p class="text-muted mb-4">
                                @lang('Cannot find the product in our catalog? Send us the brand, category, product name, SKU/code, and an optional image. We will get back to you with availability and pricing.')
                            </p>
                        @endif

                        @include('includes.frontend.quote-form', [
                            'product' => $product,
                            'categories' => $categories,
                            'brands' => $brands,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
