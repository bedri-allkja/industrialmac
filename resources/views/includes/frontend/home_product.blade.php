<div class="{{ isset($class) ? $class : 'col-md-6 col-lg-4 col-xl-3' }}">
    <div class="single-product">
        <div class="img-wrapper">
            <a href="{{ route('front.product', $product->slug) }}" class="img-link">
                <img class="product-img"
                    src="{{ $product->thumbnail ? asset('assets/images/thumbnails/' . $product->thumbnail) : asset('assets/images/noimage.png') }}"
                    alt="{{ $product->showName() }}"
                    onerror="this.onerror=null;this.src={{ json_encode(asset('assets/images/noimage.png')) }};">
            </a>

            <div class="add-to-cart">
                @if ($product->product_type == 'affiliate')
                    <a href="{{ $product->affiliate_link }}" class="add_to_cart_button">
                        <div class="add-cart">@lang('Add To Cart')</div>
                    </a>
                @else
                    @if ($product->emptyStock())
                        <div class="add-cart" style="background:#dc3545;">{{ __('Out of Stock') }}</div>
                    @else
                        @if ($product->type != 'Listing')
                            <a {{ $product->cross_products ? 'data-bs-target=#exampleModal' : '' }} href="javascript:;"
                                data-href="{{ route('product.cart.add', $product->id) }}"
                                data-cross-href="{{ route('front.show.cross.product', $product->id) }}"
                                class="add_cart_click {{ $product->cross_products ? 'view_cross_product' : '' }}">
                                <div class="add-cart">@lang('Add To Cart')</div>
                            </a>
                        @endif
                    @endif
                @endif

                @if ($product->type != 'Listing')
                    <a href="{{ route('front.product', $product->slug) }}">
                        <div class="details">
                            <i class="fas fa-eye" style="font-size:14px;"></i>
                        </div>
                    </a>
                @endif
            </div>
        </div>

        <div class="content-wrapper">
            @if ($product->brand?->image)
                <img class="product-brand-logo"
                    src="{{ filter_var($product->brand->image, FILTER_VALIDATE_URL) ? $product->brand->image : asset('assets/images/brands/' . $product->brand->image) }}"
                    alt="{{ $product->brand->name }}">
            @elseif ($product->brand)
                <div class="product-brand-name">{{ $product->brand->name }}</div>
            @endif

            <a href="{{ route('front.product', $product->slug) }}">
                <h6 class="product-title">{{ $product->showName() }}</h6>
            </a>
        </div>
    </div>
</div>
