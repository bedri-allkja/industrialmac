@extends('layouts.front')

@php
    $aboutLocale = app()->getLocale() === 'industrialmac_it' ? 'it' : 'en';
    $about = config("about.{$aboutLocale}");
@endphp

@section('content')
    <section class="gs-breadcrumb-section bg-class"
        data-background="{{ $gs->breadcrumb_banner ? asset('assets/images/' . $gs->breadcrumb_banner) : asset('assets/images/noimage.png') }}">
        <div class="container">
            <div class="row justify-content-center content-wrapper">
                <div class="col-12">
                    <h2 class="breadcrumb-title">{{ $about['page_title'] }}</h2>
                    <ul class="bread-menu">
                        <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                        <li><a href="{{ route('front.vendor', 'about') }}">{{ $about['page_title'] }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <div class="ignavo-about home-section">
        <div class="container">
            <div class="ignavo-about__hero text-center">
                <p class="ignavo-about__eyebrow">Industrialmac</p>
                <h1 class="ignavo-about__title">{{ $about['main_heading'] }}</h1>
                <p class="ignavo-about__subtitle">{{ $about['subtitle'] }}</p>
                <p class="ignavo-about__intro">{{ $about['intro'] }}</p>
            </div>

            <div class="row g-4 ignavo-about__pillars">
                @foreach ($about['pillars'] as $pillar)
                    <div class="col-lg-4 col-md-6">
                        <div class="ignavo-about__card h-100">
                            <h3>{{ $pillar['title'] }}</h3>
                            <p>{{ $pillar['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="ignavo-about__closing text-center">{{ $about['closing'] }}</p>

            <div class="row g-4 ignavo-about__sections">
                <div class="col-lg-6">
                    <div class="ignavo-about__section h-100">
                        <h2>{{ $about['vision_title'] }}</h2>
                        <p>{{ $about['vision'] }}</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ignavo-about__section h-100">
                        <h2>{{ $about['mission_title'] }}</h2>
                        <p>{{ $about['mission'] }}</p>
                    </div>
                </div>
            </div>

            <div class="ignavo-about__quality">
                <h2>{{ $about['quality_title'] }}</h2>
                <p class="ignavo-about__quality-intro">{{ $about['quality_intro'] }}</p>
                <ul class="ignavo-about__quality-list">
                    @foreach ($about['quality_pillars'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
