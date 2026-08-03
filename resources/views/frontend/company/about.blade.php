@extends('layouts.front')

@section('content')
<section class="gs-breadcrumb-section bg-class"
    data-background="{{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner) : asset('assets/images/noimage.png') }}">
    <div class="container">
        <div class="row justify-content-center content-wrapper">
            <div class="col-12">
                <h2 class="breadcrumb-title">{{ $content['page_title'] }}</h2>
                <ul class="bread-menu">
                    <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                    <li><a href="{{ route('front.about') }}">@lang('Company')</a></li>
                    <li><a href="#">{{ $content['page_title'] }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<div class="gs-blog-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="gs-blog-card policy-page">
                    <h3 class="mb-2">{{ $content['page_title'] }}</h3>
                    <p class="lead text-muted">{{ $content['headline'] }}</p>

                    @foreach ($content['intro'] as $p)
                        <p>{{ $p }}</p>
                    @endforeach

                    <h4 class="mt-4">{{ $content['experience_title'] }}</h4>
                    @foreach ($content['experience'] as $p)
                        <p>{{ $p }}</p>
                    @endforeach

                    <h4 class="mt-4">{{ $content['what_we_do_title'] }}</h4>
                    <p>{{ $content['what_we_do_intro'] }}</p>
                    <ul>
                        @foreach ($content['what_we_do'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>

                    <h4 class="mt-4">{{ $content['why_title'] }}</h4>
                    @foreach ($content['why'] as $item)
                        <h5 class="mt-3">{{ $item['title'] }}</h5>
                        <p>{{ $item['text'] }}</p>
                    @endforeach

                    <h4 class="mt-4">{{ $content['vision_title'] }}</h4>
                    <p>{{ $content['vision'] }}</p>

                    <h4 class="mt-4">{{ $content['mission_title'] }}</h4>
                    <p>{{ $content['mission'] }}</p>

                    <h4 class="mt-4">{{ $content['values_title'] }}</h4>
                    @foreach ($content['values'] as $item)
                        <h5 class="mt-3">{{ $item['title'] }}</h5>
                        <p>{{ $item['text'] }}</p>
                    @endforeach

                    <h4 class="mt-4">{{ $content['closing_title'] }}</h4>
                    <p>{{ $content['closing'] }}</p>

                    <div class="mt-4 d-flex flex-wrap gap-2">
                        <a href="{{ route('front.quality') }}" class="template-btn">@lang('Quality Policy')</a>
                        <a href="{{ route('front.faq') }}" class="template-btn">@lang('Questions & Answers')</a>
                        <a href="{{ route('front.contact') }}" class="template-btn">@lang('Contacts')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
