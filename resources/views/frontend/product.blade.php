@extends('layouts.front')

@section('content')
    <!-- Breadcrumb -->
    <section class="gs-breadcrumb-section bg-class"
        data-background="{{ $gs->breadcrumb_banner ? asset('assets/images/' . $gs->breadcrumb_banner) : '' }}">
        <div class="container">
            <div class="row justify-content-center content-wrapper">
                <div class="col-12">
                    <h2 class="breadcrumb-title">@lang('Product Details')</h2>
                    <ul class="bread-menu">
                        <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                        <li><a href="#">{{ $productt->name }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Detail -->
    <div class="single-product-details-content-wrapper">
        <div class="container">
            <div class="row gy-4">
                <div class="col-12">
                    <ul class="product-breadcrumb">
                        <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                        <li><a href="{{ route('front.category', $productt->category->slug) }}">{{ $productt->category->name }}</a></li>
                        @if ($productt->subcategory_id)
                            <li><a href="{{ route('front.category', [$productt->category->slug, $productt->subcategory->slug]) }}">{{ $productt->subcategory->name }}</a></li>
                        @endif
                        @if ($productt->childcategory_id)
                            <li><a href="{{ route('front.category', [$productt->category->slug, $productt->subcategory->slug, $productt->childcategory->slug]) }}">{{ $productt->childcategory->name }}</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Gallery -->
                <div class="col-lg-6">
                    @php
                        $noImage = asset('assets/images/noimage.png');
                        // The "box" fallback: the product's thumbnail (which usually exists
                        // even when the full-size photo file is missing on disk).
                        $thumbPhoto = !empty($productt->thumbnail)
                            ? asset('assets/images/thumbnails/' . $productt->thumbnail)
                            : $noImage;
                        if (!empty($productt->photo)) {
                            $mainPhoto = filter_var($productt->photo, FILTER_VALIDATE_URL)
                                ? $productt->photo
                                : asset('assets/images/products/' . $productt->photo);
                        } elseif (!empty($productt->thumbnail)) {
                            $mainPhoto = $thumbPhoto;
                        } else {
                            $mainPhoto = $noImage;
                        }
                    @endphp
                    <div class="gs-product-details-gallery-wrapper">
                        <div class="product-main-slider">
                            <img src="{{ $mainPhoto }}"
                                alt="{{ $productt->name }}"
                                data-zoom-image="{{ $mainPhoto }}"
                                onerror="if(!this.dataset.fb){this.dataset.fb='1';this.src='{{ $thumbPhoto }}';this.setAttribute('data-zoom-image','{{ $thumbPhoto }}');}else{this.onerror=null;this.src='{{ $noImage }}';}"
                                class="main-img">
                            @foreach ($productt->galleries as $gal)
                                <img src="{{ asset('assets/images/galleries/' . $gal->photo) }}"
                                    data-image="{{ asset('assets/images/galleries/' . $gal->photo) }}"
                                    onerror="this.onerror=null;this.src='{{ $thumbPhoto }}';" class="main-img">
                            @endforeach
                        </div>
                        <div class="product-nav-slider">
                            <img src="{{ $mainPhoto }}"
                                alt="{{ $productt->name }}"
                                onerror="if(!this.dataset.fb){this.dataset.fb='1';this.src='{{ $thumbPhoto }}';}else{this.onerror=null;this.src='{{ $noImage }}';}"
                                class="nav-img">
                            @foreach ($productt->galleries as $gal)
                                <img src="{{ asset('assets/images/galleries/' . $gal->photo) }}"
                                    onerror="this.onerror=null;this.src='{{ $thumbPhoto }}';" class="nav-img">
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div>
                        <div class="product-info-wrapper {{ $productt->type != 'Physical' ? 'mb-3' : '' }}">
                            <h3>{{ $productt->name }}</h3>
                        </div>

                        @if (
                            $productt->ship != null ||
                            $productt->sku != null ||
                            $productt->platform != null ||
                            $productt->region != null ||
                            $productt->licence_type != null)
                            <hr>
                            <div class="product-stocks-wraper">
                                <ul>
                                    @if ($productt->ship != null)
                                        <li>
                                            <span><b>@lang('Estimated Shipping Time:') </b></span>
                                            <span>{{ $productt->ship }}</span>
                                        </li>
                                    @endif
                                    @if ($productt->brand_id != null)
                                        <li>
                                            <span><b>@lang('Manufacturer:') </b></span>
                                            <span>{{ $brand->name }}</span>
                                        </li>
                                    @endif
                                    @if ($productt->sku != null)
                                        <li>
                                            <span><b>@lang('Product SKU:') </b></span>
                                            <span>{{ $productt->sku }}</span>
                                        </li>
                                    @endif
                                    @if ($productt->type == 'License')
                                        @if ($productt->platform != null)
                                            <li>
                                                <span><b>@lang('Platform:') </b></span>
                                                <span>{{ $productt->platform }}</span>
                                            </li>
                                        @endif
                                        @if ($productt->region != null)
                                            <li>
                                                <span><b>@lang('Region:') </b></span>
                                                <span>{{ $productt->region }}</span>
                                            </li>
                                        @endif
                                        @if ($productt->licence_type != null)
                                            <li>
                                                <span><b>@lang('License Type:') </b></span>
                                                <span>{{ $productt->licence_type }}</span>
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            </div>
                        @endif

                        @if (!empty($productt->attributes))
                            @php $attrArr = json_decode($productt->attributes, true); @endphp
                        @endif

                        @if (!empty($attrArr))
                            <hr>
                            <div class="row gy-4">
                                @foreach ($attrArr as $attrKey => $attrVal)
                                    @if (array_key_exists('details_status', $attrVal) && $attrVal['details_status'] == 1)
                                        <div class="col-lg-6">
                                            <div class="attribute-wrapper">
                                                <span class="attribute-title">{{ str_replace('_', ' ', $attrKey) }}:</span>
                                                <ul>
                                                    @foreach ($attrVal['values'] as $optionKey => $optionVal)
                                                        <li class="gs-radio-wrapper">
                                                            <input type="radio"
                                                                id="{{ $attrKey }}{{ $optionKey }}"
                                                                data-key="{{ $attrKey }}"
                                                                data-price="{{ $attrVal['prices'][$optionKey] * $curr->value }}"
                                                                value="{{ $optionVal }}" name="{{ $attrKey }}"
                                                                {{ $loop->first ? 'checked' : '' }} class="cart_attr">
                                                            <label class="icon-label" for="w1">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                                    <rect x="0.5" y="0.5" width="19" height="19" rx="9.5" fill="#FDFDFD" />
                                                                    <rect x="0.5" y="0.5" width="19" height="19" rx="9.5" stroke="#004a93" />
                                                                    <circle cx="10" cy="10" r="4" fill="#004a93" />
                                                                </svg>
                                                            </label>
                                                            <label for="{{ $attrKey }}{{ $optionKey }}">
                                                                {{ $optionVal }}
                                                            </label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <hr>
                        @endif

                        @if ($productt->stock_check == 1)
                            @if (!empty($productt->size))
                                <div class="variation-wrapper variation-sizes">
                                    <span class="varition-title">@lang('Size:')</span>
                                    <ul>
                                        @foreach (array_unique($productt->size) as $key => $data1)
                                            <li class="{{ $loop->first ? 'active' : '' }} cart_size"
                                                data-price="{{ $productt->size_price[$key] * $curr->value }}">
                                                <input {{ $loop->first ? 'checked' : '' }} type="radio"
                                                    id="size_{{ $key }}" data-value="{{ $key }}"
                                                    data-key="{{ str_replace(' ', '', $data1) }}"
                                                    data-price="{{ $productt->size_price[$key] * $curr->value }}"
                                                    data-qty="{{ $productt->size_qty[$key] }}" value="{{ $key }}"
                                                    name="size">
                                                <label for="size_{{ $key }}">{{ $data1 }}</label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (!empty($productt->color_all))
                                <div class="variation-wrapper variation-colors">
                                    <span class="varition-title">@lang('Color:')</span>
                                    <ul>
                                        @foreach ($productt->color_all as $ckey => $color1)
                                            <li class="{{ $loop->first ? 'active' : '' }} cart_color">
                                                <input {{ $loop->first ? 'checked' : '' }} type="radio" data-price="0"
                                                    data-color="{{ $color1 }}" id="color_{{ $ckey }}"
                                                    name="colors" value="{{ $ckey }}">
                                                <label for="color_{{ $ckey }}"
                                                    data-color-code="{{ $color1 }}"
                                                    data-outline-color-code="{{ $color1 }}"></label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endif

                        <hr id="quote-request">

                        @include('alerts.form-success')

                        <div class="quote-request-section">
                            <h4 class="mb-3">@lang('Request Quote')</h4>
                            <p class="text-muted small mb-3">
                                @lang('Prices are provided on request. Submit your details and we will contact you with a quote.')
                            </p>
                            @include('includes.frontend.quote-form', [
                                'product' => $productt,
                                'compact' => true,
                            ])
                        </div>

                        <hr>

                        <!-- Description Tabs -->
                        <div class="tab-product-des-wrapper">
                            <div class="container p-0">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                            data-bs-target="#description-tab-pane" type="button" role="tab"
                                            aria-controls="description-tab-pane" aria-selected="true">
                                            @lang('Description')
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="buy-return-policy-tab" data-bs-toggle="tab"
                                            data-bs-target="#buy-return-policy-tab-pane" type="button" role="tab"
                                            aria-controls="buy-return-policy-tab-pane" aria-selected="false">
                                            @lang('Return Policy')
                                        </button>
                                    </li>
                                    @if ($productt->whole_sell_qty != null && $productt->whole_sell_qty != '')
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="whole-sell-tab" data-bs-toggle="tab"
                                                data-bs-target="#whole-sell-tab-pane" type="button" role="tab"
                                                aria-controls="whole-sell-tab-pane" aria-selected="false">
                                                @lang('Wholesale')
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane show active" id="description-tab-pane" role="tabpanel"
                                        aria-labelledby="description-tab" tabindex="0">
                                        {!! product_quote_description($productt) !!}
                                    </div>
                                    <div class="tab-pane fade" id="buy-return-policy-tab-pane" role="tabpanel"
                                        aria-labelledby="buy-return-policy-tab" tabindex="0">
                                        {!! clean($productt->policy, ['Attr.EnableID' => true]) !!}
                                    </div>
                                    @if ($productt->whole_sell_qty != null && $productt->whole_sell_qty != '')
                                        <div class="tab-pane fade" id="whole-sell-tab-pane" role="tabpanel"
                                            aria-labelledby="whole-sell-tab" tabindex="0">
                                            <div class="row sholesell-tab-content-wrapper">
                                                <div class="col-12 col-lg-8 col-xl-9 col-xxl-8">
                                                    <div class="pro-summary">
                                                        <div class="price-summary">
                                                            <div class="price-summary-content">
                                                                <p class="title text-center text-lg">@lang('Wholesale')</p>
                                                                <ul class="price-summary-list">
                                                                    <li class="regular-price">
                                                                        <p class="fw-medium">@lang('Quantity')</p>
                                                                        <p class="fw-medium">@lang('Discount')</p>
                                                                    </li>
                                                                    @foreach ($productt->whole_sell_qty as $key => $data1)
                                                                        <li class="selling-price">
                                                                            <label>{{ $productt->whole_sell_qty[$key] }}+</label>
                                                                            <span>{{ $productt->whole_sell_discount[$key] }}% @lang('Off')</span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="gs-product-cards-slider-area" style="padding:50px 0;">
        <div class="container">
            <h3 class="esporim-section-title text-center d-block">@lang('Related Products')</h3>
            <div class="product-cards-slider">
                @foreach (App\Models\Product::where('type', $productt->type)->where('product_type', $productt->product_type)->withCount('ratings')->withAvg('ratings', 'rating')->take(12)->get() as $product)
                    @include('includes.frontend.home_product', ['class' => 'not'])
                @endforeach
            </div>
        </div>
    </div>

    @if ($productt->user_id != 0 && $vendor_products->count() > 0)
        <div class="gs-product-cards-slider-section more-products-by-seller">
            <div class="gs-product-cards-slider-area" style="padding:50px 0;background:var(--esporim-light-bg);">
                <div class="container">
                    <h3 class="esporim-section-title text-center d-block">@lang('More Products From This Seller')</h3>
                    <div class="product-cards-slider">
                        @foreach ($vendor_products as $product)
                            @include('includes.frontend.home_product', ['class' => 'not'])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Report Modal -->
    @if (auth()->check())
        <div class="modal gs-modal fade" id="report-modal" tabindex="-1" aria-hidden="true">
            <form action="{{ route('product.report') }}" method="POST"
                class="modal-dialog assign-rider-modal-dialog modal-dialog-centered">
                {{ csrf_field() }}
                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                <input type="hidden" name="product_id" value="{{ $productt->id }}">
                <div class="modal-content assign-rider-modal-content form-group">
                    <div class="modal-header w-100">
                        <h4 class="title">{{ __('SEGNALA PRODOTTO') }}</h4>
                        <button type="button" data-bs-dismiss="modal">
                            <i class="fa-regular fa-circle-xmark gs-modal-close-btn"></i>
                        </button>
                    </div>
                    <div class="input-label-wrapper w-100">
                        <label>{{ __('Fornisci i seguenti dettagli') }}</label>
                        <input type="text" name="title" class="form-control mb-3" placeholder="{{ __('Titolo segnalazione') }}" required="">
                        <textarea name="note" class="form-control border p-3" placeholder="{{ __('Note segnalazione') }}" required=""></textarea>
                    </div>
                    <button class="template-btn" data-bs-dismiss="modal" type="submit">{{ __('INVIA') }}</button>
                </div>
            </form>
        </div>
    @endif

    <!-- Vendor Message Modal -->
    <div class="modal gs-modal fade" id="vendorform" tabindex="-1" aria-modal="true" role="dialog">
        <form action="{{ route('user-send-message') }}" id="emailreply" method="POST"
            class="modal-dialog assign-rider-modal-dialog modal-dialog-centered emailreply">
            {{ csrf_field() }}
            <div class="modal-content assign-rider-modal-content form-group">
                <div class="modal-header w-100">
                    <h4 class="title">@lang('Send Message')</h4>
                    <button type="button" data-bs-dismiss="modal">
                        <i class="fa-regular fa-circle-xmark gs-modal-close-btn"></i>
                    </button>
                </div>
                <div class="input-label-wrapper w-100">
                    <input type="text" class="form-control border px-3 mb-4" id="eml" name="email" readonly
                        placeholder="@lang('Email')" value="{{ auth()->user() ? auth()->user()->email : '' }}">
                    <input type="text" class="form-control border px-3 mb-4" name="subject" placeholder="@lang('Subject')" required="">
                    <textarea class="form-control border px-3 mb-4" name="message" placeholder="{{ __('Il tuo messaggio') }}" required=""></textarea>
                    <input type="hidden" name="name" value="{{ Auth::user() ? Auth::user()->name : '' }}">
                    <input type="hidden" name="user_id" value="{{ Auth::user() ? Auth::user()->id : '' }}">
                    <input type="hidden" name="vendor_id" value="{{ $productt->user_id }}">
                </div>
                <button class="template-btn" data-bs-dismiss="modal" type="submit">@lang('Send Message')</button>
            </div>
        </form>
    </div>

    <div class="modal gs-modal fade" id="sendMessage" tabindex="-1" aria-modal="true" role="dialog">
        <form action="{{ route('user-send-message') }}" method="POST"
            class="modal-dialog assign-rider-modal-dialog modal-dialog-centered emailreply">
            {{ csrf_field() }}
            <div class="modal-content assign-rider-modal-content form-group">
                <div class="modal-header w-100">
                    <h4 class="title">@lang('Send Message')</h4>
                    <button type="button" data-bs-dismiss="modal">
                        <i class="fa-regular fa-circle-xmark gs-modal-close-btn"></i>
                    </button>
                </div>
                <div class="input-label-wrapper w-100">
                    <input type="text" class="form-control border px-3 mb-4" name="subject" placeholder="@lang('Subject')" required="">
                    <textarea class="form-control border px-3 mb-4" name="message" placeholder="{{ __('Il tuo messaggio') }}" required=""></textarea>
                </div>
                <button class="template-btn" data-bs-dismiss="modal" type="submit">@lang('Send Message')</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/front/js/jquery.elevatezoom.js') }}"></script>
    <script type="text/javascript">
        (function($) {
            "use strict";

            $("#single-image-zoom").elevateZoom({
                gallery: 'gallery_09',
                zoomType: "inner",
                cursor: "crosshair",
                galleryActiveClass: 'active',
                imageCrossfade: true,
                loadingIcon: 'http://www.elevateweb.co.uk/spinner.gif'
            });
            $("#single-image-zoom").bind("click", function(e) {
                var ez = $('#single-image-zoom').data('elevateZoom');
                $.fancybox(ez.getGalleryList());
                return false;
            });

            $(document).on("submit", "#emailreply", function() {
                var token = $(this).find('input[name=_token]').val();
                var subject = $(this).find('input[name=subject]').val();
                var message = $(this).find('textarea[name=message]').val();
                var email = $(this).find('input[name=email]').val();
                var name = $(this).find('input[name=name]').val();
                var user_id = $(this).find('input[name=user_id]').val();
                $('#eml').prop('disabled', true);
                $('#subj').prop('disabled', true);
                $('#msg').prop('disabled', true);
                $('#emlsub').prop('disabled', true);
                $.ajax({
                    type: 'post',
                    url: "{{ URL::to('/user/user/contact') }}",
                    data: {
                        '_token': token,
                        'subject': subject,
                        'message': message,
                        'email': email,
                        'name': name,
                        'user_id': user_id
                    },
                    success: function(data) {
                        $('#eml').prop('disabled', false);
                        $('#subj').prop('disabled', false);
                        $('#msg').prop('disabled', false);
                        $('#subj').val('');
                        $('#msg').val('');
                        $('#emlsub').prop('disabled', false);
                        if (data == 0)
                            toastr.error("Email Not Found");
                        else
                            toastr.success("Message Sent");
                        $('#vendorform').modal('hide');
                    }
                });
                return false;
            });
        })(jQuery);

        $('.add-to-affilate').on('click', function() {
            var value = $(this).data('href');
            var tempInput = document.createElement("input");
            tempInput.style = "position: absolute; left: -1000px; top: -1000px";
            tempInput.value = value;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            toastr.success('Affiliate Link Copied');
        });
    </script>
@endsection
