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
                        <div class="add-cart">@lang('Request Quote')</div>
                    </a>
                @else
                    <a href="{{ route('front.product', $product->slug) }}#quote-request">
                        <div class="add-cart">@lang('Request Quote')</div>
                    </a>
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
            @if (brand_logo_url($product->brand))
                <img class="product-brand-logo"
                    src="{{ brand_logo_url($product->brand) }}"
                    alt="{{ $product->brand->name }}"
                    onerror="this.onerror=null;this.style.display='none';this.nextElementSibling?.classList.remove('d-none');">
                @if ($product->brand)
                    <div class="product-brand-name d-none">{{ $product->brand->name }}</div>
                @endif
            @elseif ($product->brand)
                <div class="product-brand-name">{{ $product->brand->name }}</div>
            @endif

            @if ($product->category?->name)
                <div class="product-category-name">{{ $product->category->name }}</div>
            @endif

            <a href="{{ route('front.product', $product->slug) }}">
                <h6 class="product-title">{{ $product->showName() }}</h6>
            </a>

            @if ($product->sku)
                <div class="product-sku text-muted small">@lang('SKU'): {{ $product->sku }}</div>
            @endif
        </div>
    </div>
</div>
