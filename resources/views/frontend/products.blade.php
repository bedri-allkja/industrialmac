@extends('layouts.front')

@section('content')
    <!-- Breadcrumb -->
    <section class="gs-breadcrumb-section bg-class"
        data-background="{{ $gs->breadcrumb_banner ? asset('assets/images/' . $gs->breadcrumb_banner) : '' }}">
        <div class="container">
            <div class="row justify-content-center content-wrapper">
                <div class="col-12">
                    <h2 class="breadcrumb-title">@lang('Categories')</h2>
                    <ul class="bread-menu">
                        <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                        <li><a href="javascript:;">@lang('Categories')</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <div class="gs-blog-wrapper" style="padding:40px 0;">
        <div class="container">
            <div class="row flex-column-reverse flex-lg-row">
                <!-- Sidebar -->
                <div class="col-12 col-lg-3 mt-40 mt-lg-0">
                    <div class="gs-product-sidebar-wrapper">
                        <!-- Categories -->
                        <div class="single-product-widget">
                            <h5 class="widget-title">@lang('Categories')</h5>
                            <div class="product-cat-widget">
                                <ul class="accordion">
                                    @foreach ($categories as $category)
                                        @if ($category->subs->count() > 0)
                                            <li>
                                                @php
                                                    $isCategoryActive = Request::segment(2) === $category->slug;
                                                @endphp
                                                <div class="d-flex justify-content-between align-items-lg-baseline">
                                                    <a href="{{ route('front.category', $category->slug) }}"
                                                        class="{{ $isCategoryActive ? 'sidebar-active-color' : '' }}">
                                                        {{ $category->name }}
                                                    </a>
                                                    <button data-bs-toggle="collapse"
                                                        data-bs-target="#{{ $category->slug }}_level_2"
                                                        aria-controls="{{ $category->slug }}_level_2"
                                                        aria-expanded="{{ $isCategoryActive ? 'true' : 'false' }}"
                                                        class="{{ $isCategoryActive ? '' : 'collapsed' }}">
                                                        <i class="fa-solid fa-plus"></i>
                                                        <i class="fa-solid fa-minus"></i>
                                                    </button>
                                                </div>

                                                @foreach ($category->subs as $subcategory)
                                                    @php
                                                        $isSubcategoryActive = $isCategoryActive && Request::segment(3) === $subcategory->slug;
                                                    @endphp
                                                    <ul id="{{ $category->slug }}_level_2"
                                                        class="accordion-collapse collapse ms-3 {{ $isCategoryActive ? 'show' : '' }}">
                                                        <li>
                                                            <div class="d-flex justify-content-between align-items-lg-baseline">
                                                                <a href="{{ route('front.category', [$category->slug, $subcategory->slug]) }}"
                                                                    class="{{ $isSubcategoryActive ? 'sidebar-active-color' : '' }}"
                                                                    @if ($subcategory->childs->count() > 0)
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#inner{{ $subcategory->slug }}_level_2_1"
                                                                        aria-controls="inner{{ $subcategory->slug }}_level_2_1"
                                                                        aria-expanded="{{ $isSubcategoryActive ? 'true' : 'false' }}"
                                                                    @endif>
                                                                    {{ $subcategory->name }}
                                                                </a>
                                                                @if ($subcategory->childs->count() > 0)
                                                                    <button data-bs-toggle="collapse"
                                                                        data-bs-target="#inner{{ $subcategory->slug }}_level_2_1"
                                                                        aria-controls="inner{{ $subcategory->slug }}_level_2_1"
                                                                        aria-expanded="{{ $isSubcategoryActive ? 'true' : 'false' }}"
                                                                        class="{{ $isSubcategoryActive ? '' : 'collapsed' }}">
                                                                        <i class="fa-solid fa-plus"></i>
                                                                        <i class="fa-solid fa-minus"></i>
                                                                    </button>
                                                                @endif
                                                            </div>

                                                            @if ($subcategory->childs->count() > 0)
                                                                <ul id="inner{{ $subcategory->slug }}_level_2_1"
                                                                    class="accordion-collapse collapse ms-3 {{ $isSubcategoryActive ? 'show' : '' }}">
                                                                    @foreach ($subcategory->childs as $child)
                                                                        @php
                                                                            $isChildActive = $isSubcategoryActive && Request::segment(4) === $child->slug;
                                                                        @endphp
                                                                        <li>
                                                                            <a href="{{ route('front.category', [$category->slug, $subcategory->slug, $child->slug]) }}"
                                                                                class="{{ $isChildActive ? 'sidebar-active-color' : '' }}">
                                                                                {{ $child->name }}
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    </ul>
                                                @endforeach
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ route('front.category', $category->slug) }}"
                                                    class="{{ Request::segment(2) === $category->slug ? 'sidebar-active-color' : '' }}">
                                                    {{ $category->name }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>


                        @if (
                            (!empty($cat) && !empty(json_decode($cat->attributes, true))) ||
                            (!empty($subcat) && !empty(json_decode($subcat->attributes, true))) ||
                            (!empty($childcat) && !empty(json_decode($childcat->attributes, true)))
                        )
                            @if (!empty($cat) && !empty(json_decode($cat->attributes, true)))
                                @foreach ($cat->attributes as $key => $attr)
                                    <div class="single-product-widget">
                                        <h5 class="widget-title">{{ $attr->name }}</h5>
                                        <div class="warranty-type">
                                            @if (!empty($attr->attribute_options))
                                                <ul>
                                                    @foreach ($attr->attribute_options as $key => $option)
                                                        <li class="gs-checkbox-wrapper">
                                                            <input type="checkbox" class="attribute-input"
                                                                name="{{ $attr->input_name }}[]"
                                                                {{ isset($_GET[$attr->input_name]) && in_array($option->name, $_GET[$attr->input_name]) ? 'checked' : '' }}
                                                                id="{{ $attr->input_name }}{{ $option->id }}"
                                                                value="{{ $option->name }}">
                                                            <label class="icon-label" for="{{ $attr->input_name }}{{ $option->id }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                                                    <path d="M10 3L4.5 8.5L2 6" stroke="#004a93" stroke-width="1.6666" stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                            </label>
                                                            <label for="{{ $attr->input_name }}{{ $option->id }}">{{ $option->name }}</label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if (!empty($subcat) && !empty(json_decode($subcat->attributes, true)))
                                @foreach ($subcat->attributes as $key => $attr)
                                    <div class="single-product-widget">
                                        <h5 class="widget-title">{{ $attr->name }}</h5>
                                        <div class="warranty-type">
                                            @if (!empty($attr->attribute_options))
                                                <ul>
                                                    @foreach ($attr->attribute_options as $key => $option)
                                                        <li class="gs-checkbox-wrapper">
                                                            <input type="checkbox" class="attribute-input"
                                                                name="{{ $attr->input_name }}[]"
                                                                id="{{ $attr->input_name }}{{ $option->id }}"
                                                                value="{{ $option->name }}">
                                                            <label class="icon-label" for="{{ $attr->input_name }}{{ $option->id }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                                                    <path d="M10 3L4.5 8.5L2 6" stroke="#004a93" stroke-width="1.6666" stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                            </label>
                                                            <label for="{{ $attr->input_name }}{{ $option->id }}">{{ $option->name }}</label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if (!empty($childcat) && !empty(json_decode($childcat->attributes, true)))
                                @foreach ($childcat->attributes as $key => $attr)
                                    <div class="single-product-widget">
                                        <h5 class="widget-title">{{ $attr->name }}</h5>
                                        <div class="warranty-type">
                                            @if (!empty($attr->attribute_options))
                                                <ul>
                                                    @foreach ($attr->attribute_options as $key => $option)
                                                        <li class="gs-checkbox-wrapper">
                                                            <input type="checkbox" class="attribute-input"
                                                                name="{{ $attr->input_name }}[]"
                                                                id="{{ $attr->input_name }}{{ $option->id }}"
                                                                value="{{ $option->name }}">
                                                            <label class="icon-label" for="{{ $attr->input_name }}{{ $option->id }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                                                    <path d="M10 3L4.5 8.5L2 6" stroke="#004a93" stroke-width="1.6666" stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                            </label>
                                                            <label for="{{ $attr->input_name }}{{ $option->id }}">{{ $option->name }}</label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="col-12 col-lg-9 gs-main-blog-wrapper">
                    @php
                        if (request()->input('view_check') == null || request()->input('view_check') == 'grid-view') {
                            $view = 'grid-view';
                        } else {
                            $view = 'list-view';
                        }
                    @endphp

                    <div class="product-nav-wrapper">
                        <h5 class="mb-0">@lang('Products Found:') {{ $prods->count() }}</h5>
                        <div class="filter-wrapper">
                            <div class="sort-wrapper d-flex align-items-center gap-2">
                                <h5 class="mb-0">@lang('Ordina per:')</h5>
                                <select class="nice-select" id="sortby" name="sort">
                                    <option value="date_desc">{{ __('Più recente') }}</option>
                                    <option value="date_asc">{{ __('Meno recente') }}</option>
                                </select>
                            </div>
                            <div class="btn-wrapper nav d-none d-lg-inline-block" role="tablist">
                                <button class="grid-btn check_view {{ $view == 'list-view' ? 'active' : '' }}"
                                    data-shopview="list-view" type="button" data-bs-toggle="tab"
                                    data-bs-target="#layout-list-pane" role="tab" aria-controls="layout-list-pane"
                                    aria-selected="{{ $view == 'list-view' ? 'true' : 'false' }}">
                                    <i class="fas fa-list"></i>
                                </button>
                                <button class="grid-btn check_view {{ $view == 'grid-view' ? 'active' : '' }}"
                                    type="button" data-shopview="grid-view" data-bs-toggle="tab"
                                    data-bs-target="#layout-grid-pane" role="tab" aria-controls="layout-grid-pane"
                                    aria-selected="{{ $view == 'grid-view' ? 'true' : 'false' }}">
                                    <i class="fas fa-th"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    @if ($prods->count() == 0)
                        <div class="product-nav-wrapper d-flex justify-content-center mt-4">
                            <h5>@lang('No products found')</h5>
                        </div>
                    @else
                        <div class="tab-content mt-3" id="myTabContent">
                            <div class="tab-pane fade {{ $view == 'list-view' ? 'show active' : '' }}"
                                id="layout-list-pane" role="tabpanel" tabindex="0">
                                <div class="row gy-4">
                                    @foreach ($prods as $product)
                                        @include('includes.frontend.list_view_product')
                                    @endforeach
                                </div>
                            </div>

                            <div class="tab-pane fade {{ $view == 'grid-view' ? 'show active' : '' }}"
                                id="layout-grid-pane" role="tabpanel" tabindex="0">
                                <div class="row gy-4">
                                    @foreach ($prods as $product)
                                        @include('includes.frontend.home_product', ['class' => 'col-sm-6 col-md-6 col-xl-4'])
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        {{ $prods->links('includes.frontend.pagination') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(".attribute-input, #sortby, #pageby").on('change', function() {
            $(".ajax-loader").show();
            filter();
        });

        function filter() {
            let filterlink = '{{ route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')]) }}';
            let params = new URLSearchParams();

            $(".attribute-input").each(function() {
                if ($(this).is(':checked')) {
                    params.append($(this).attr('name'), $(this).val());
                }
            });

            if ($("#sortby").val() != '') {
                params.append($("#sortby").attr('name'), $("#sortby").val());
            }

            let check_view = $('.check_view.active').data('shopview');
            if (check_view) {
                params.append('view_check', check_view);
            }

            filterlink += '?' + params.toString();
            location.href = filterlink;
        }

        function addToPagination() {
            $('ul.pagination li a').each(function() {
                let url = $(this).attr('href');
                let queryString = '?' + url.split('?')[1];
                let urlParams = new URLSearchParams(queryString);
                let page = urlParams.get('page');

                let fullUrl = '{{ route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')]) }}';
                let params = new URLSearchParams();

                $(".attribute-input").each(function() {
                    if ($(this).is(':checked')) {
                        params.append($(this).attr('name'), $(this).val());
                    }
                });

                if ($("#sortby").val() != '') {
                    params.append('sort', $("#sortby").val());
                }
                if ($("#pageby").val() != '') {
                    params.append('pageby', $("#pageby").val());
                }
                params.append('page', page);
                $(this).attr('href', fullUrl + '?' + params.toString());
            });
        }
    </script>
@endsection
