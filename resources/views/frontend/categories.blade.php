@extends('layouts.front')

@section('content')
    <!-- Breadcrumb -->
    <section class="gs-breadcrumb-section bg-class"
        data-background="{{ $gs->breadcrumb_banner ? asset('assets/images/' . $gs->breadcrumb_banner) : '' }}">
        <div class="container">
            <div class="row justify-content-center content-wrapper">
                <div class="col-12">
                    <h2 class="breadcrumb-title">@lang('Brands')</h2>
                    <ul class="bread-menu">
                        <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                        <li><a href="javascript:;">@lang('Brands')</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <div class="gs-blog-wrapper" style="padding:40px 0;">
        <div class="container">
            <div class="product-nav-wrapper mb-4">
                <h5 class="mb-0">@lang('Brands') ({{ $categoryList->total() }})</h5>
                <form class="im-cat-search-form" action="{{ route('front.categories') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" value="{{ $search ?? '' }}"
                            placeholder="@lang('Search brands...')" autocomplete="off">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            @if ($categoryList->count() == 0)
                <div class="product-nav-wrapper d-flex justify-content-center mt-4">
                    <h5>@lang('No brands found')</h5>
                </div>
            @else
                <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-lg-3">
                    @foreach ($categoryList as $category)
                        <div class="col">
                            <a href="{{ route('front.category', $category->slug) }}" class="im-category-card">
                                <span class="im-category-card__name">{{ $category->name }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-5">
                    {{ $categoryList->links('includes.frontend.pagination') }}
                </div>
            @endif
        </div>
    </div>
@endsection
