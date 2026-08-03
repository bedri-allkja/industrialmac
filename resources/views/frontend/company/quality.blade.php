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

                    <h4 class="mt-4">{{ $content['commitment_title'] }}</h4>
                    <p>{{ $content['commitment'] }}</p>

                    <h4 class="mt-4">{{ $content['principles_title'] }}</h4>
                    @foreach ($content['principles'] as $item)
                        <h5 class="mt-3">{{ $item['title'] }}</h5>
                        <p>{{ $item['text'] }}</p>
                    @endforeach

                    <h4 class="mt-4">{{ $content['promise_title'] }}</h4>
                    <p>{{ $content['promise'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
