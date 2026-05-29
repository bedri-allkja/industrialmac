@extends('layouts.front')

@php
    $faqContent = site_faq_content();
    $faqs = site_faqs();
@endphp

@section('content')
    <section class="gs-breadcrumb-section bg-class"
        data-background="{{ $gs->breadcrumb_banner ? asset('assets/images/' . $gs->breadcrumb_banner) : asset('assets/images/noimage.png') }}">
        <div class="container">
            <div class="row justify-content-center content-wrapper">
                <div class="col-12">
                    <h2 class="breadcrumb-title">{{ $faqContent['page_title'] }}</h2>
                    <ul class="bread-menu">
                        <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                        <li><a href="{{ route('front.faq') }}">{{ $faqContent['page_title'] }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <div class="gs-faq-section ignavo-faq-page">
        <div class="container">
            <div class="ignavo-faq-page__intro text-center">
                <h3>{{ $faqContent['section_title'] }}</h3>
            </div>
            <div class="faq-box">
                @include('includes.frontend.faq-accordion', [
                    'faqs' => $faqs,
                    'accordionId' => 'faqlist',
                    'accordionClass' => 'hyp-accordians accordion-flush ignavo-faq__accordion',
                    'itemClass' => 'wow-replaced',
                ])
            </div>
        </div>
    </div>
@endsection
