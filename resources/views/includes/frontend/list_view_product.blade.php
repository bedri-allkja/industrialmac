<div class="col-sm-6 col-md-6 col-lg-12">
    <div class="single-product-list-view" style="border:1px solid var(--esporim-border,#e0e4e8);border-radius:6px;overflow:hidden;">
        <div class="img-wrapper">
            <a href="{{ route('front.product', $product->slug) }}">
                <img class="product-img"
                    src="{{ $product->thumbnail ? asset('assets/images/thumbnails/' . $product->thumbnail) : asset('assets/images/noimage.png') }}"
                    alt="{{ $product->showName() }}">
            </a>
        </div>
        <div class="content-wrapper">
            @if ($product->brand)
                <div class="product-brand-name" style="font-size:11px;color:#666;text-transform:uppercase;font-weight:600;margin-bottom:4px;">
                    {{ $product->brand->name }}
                </div>
            @endif

            <h4 class="product-title">
                <a href="{{ route('front.product', $product->slug) }}">{{ $product->showName() }}</a>
            </h4>

            <div class="add-to-cart mt-2">
                @if ($product->type != 'Listing')
                    <a href="{{ route('front.product', $product->slug) }}">
                        <div class="details">
                            <i class="fas fa-eye"></i>
                        </div>
                    </a>
                @endif

                @if ($product->product_type == 'affiliate')
                    <a href="{{ $product->affiliate_link }}" class="add_to_cart_button">
                        <div class="add-cart">@lang('Aggiungi')</div>
                    </a>
                @else
                    @if ($product->emptyStock())
                        <div class="add-cart" style="background:#dc3545;color:#fff;padding:6px 12px;border-radius:4px;font-size:12px;">
                            {{ __('Esaurito') }}
                        </div>
                    @else
                        @if ($product->type != 'Listing')
                            <a {{ $product->cross_products ? 'data-bs-target=#exampleModal' : '' }} href="javascript:;"
                                data-href="{{ route('product.cart.add', $product->id) }}"
                                data-cross-href="{{ route('front.show.cross.product', $product->id) }}"
                                class="add_cart_click {{ $product->cross_products ? 'view_cross_product' : '' }}">
                                <div class="add-cart">@lang('Aggiungi al carrello')</div>
                            </a>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
