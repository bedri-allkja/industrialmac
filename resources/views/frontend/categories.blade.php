@extends('layouts.front')

@section('content')
    <!-- Breadcrumb -->
    <section class="gs-breadcrumb-section bg-class"
        data-background="{{ $gs->breadcrumb_banner ? asset('assets/images/' . $gs->breadcrumb_banner) : '' }}">
        <div class="container">
            <div class="row justify-content-center content-wrapper">
                <div class="col-12">
                    <h2 class="breadcrumb-title">INDUSTRIALMAC – GLOBAL BRAND NETWORK</h2>
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
                <h5 class="mb-0">@lang('Brands') <span id="imBrandCount">({{ $categoryList->count() }})</span></h5>
                <div class="im-cat-search-form">
                    <div class="input-group">
                        <input type="text" id="imBrandFilter" class="form-control" value="{{ $search ?? '' }}"
                            placeholder="@lang('Search brands...')" autocomplete="off">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>

            @if ($categoryList->count() == 0)
                <div class="product-nav-wrapper d-flex justify-content-center mt-4">
                    <h5>@lang('No brands found')</h5>
                </div>
            @else
                <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-lg-3" id="imBrandGrid">
                    @foreach ($categoryList as $category)
                        <div class="col im-brand-col" data-name="{{ strtolower($category->name) }}">
                            <a href="{{ route('front.category', $category->slug) }}" class="im-category-card">
                                <span class="im-category-card__name">{{ $category->name }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div id="imBrandEmpty" class="product-nav-wrapper d-none justify-content-center mt-4">
                    <h5>@lang('No brands found')</h5>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('imBrandFilter');
    var cols = document.querySelectorAll('.im-brand-col');
    var empty = document.getElementById('imBrandEmpty');
    var countEl = document.getElementById('imBrandCount');
    if (!input || !cols.length) return;

    input.addEventListener('input', function () {
        var q = (input.value || '').trim().toLowerCase();
        var visible = 0;
        cols.forEach(function (col) {
            var match = !q || (col.getAttribute('data-name') || '').indexOf(q) !== -1;
            col.classList.toggle('d-none', !match);
            if (match) visible++;
        });
        if (countEl) countEl.textContent = '(' + visible + ')';
        if (empty) {
            empty.classList.toggle('d-none', visible > 0);
            empty.classList.toggle('d-flex', visible === 0);
        }
    });
});
</script>
@endpush
