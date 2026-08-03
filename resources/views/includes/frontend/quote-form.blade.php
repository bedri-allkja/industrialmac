@php
    $isCompact = !empty($compact);
    $prefillProduct = $product ?? null;
    if (!isset($categories)) {
        $categories = \App\Models\Category::where('status', 1)->orderBy('name')->get();
    }
    if (!isset($brands)) {
        $brands = \App\Models\Brand::orderBy('name')->get();
    }
@endphp

<form class="form-area quote-request-form {{ $isCompact ? 'quote-request-form--compact' : '' }}"
    action="{{ route('front.quote.submit') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @if ($prefillProduct)
        <input type="hidden" name="product_id" value="{{ $prefillProduct->id }}">
        <div class="alert alert-light border mb-3">
            <strong>@lang('Product'):</strong> {{ $prefillProduct->showName() }}
            @if ($prefillProduct->sku)
                <span class="d-block small text-muted">@lang('SKU'): {{ $prefillProduct->sku }}</span>
            @endif
        </div>
    @endif

    <div class="row gy-3 form-row">
        <div class="col-md-6">
            <div class="form-group">
                <input type="text" name="customer_name" class="form-control"
                    placeholder="@lang('Your Name') *" value="{{ old('customer_name') }}" required>
            </div>
            @error('customer_name')
                <p class="my-1 text-danger small">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <input type="text" name="company_name" class="form-control"
                    placeholder="@lang('Company Name') *" value="{{ old('company_name') }}" required>
            </div>
            @error('company_name')
                <p class="my-1 text-danger small">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <input type="email" name="customer_email" class="form-control"
                    placeholder="@lang('Your Email') *" value="{{ old('customer_email') }}" required>
            </div>
            @error('customer_email')
                <p class="my-1 text-danger small">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <input type="text" name="customer_phone" class="form-control"
                    placeholder="@lang('Your Phone Number') *" value="{{ old('customer_phone') }}" required>
            </div>
            @error('customer_phone')
                <p class="my-1 text-danger small">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <select name="country" class="form-control quote-country-select" required
                    data-placeholder="@lang('Select Country') *">
                    <option value=""></option>
                    @foreach (App\Models\Country::where('status', 1)->orderBy('country_name')->get() as $countryOption)
                        <option value="{{ $countryOption->country_name }}" @selected(old('country') == $countryOption->country_name)>
                            {{ $countryOption->country_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('country')
                <p class="my-1 text-danger small">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <input type="number" name="quantity" class="form-control" min="1"
                    placeholder="@lang('Quantity')" value="{{ old('quantity', 1) }}">
            </div>
            @error('quantity')
                <p class="my-1 text-danger small">{{ $message }}</p>
            @enderror
        </div>

        @if (!$prefillProduct)
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" name="product_name" class="form-control"
                        placeholder="@lang('Product Name') *" value="{{ old('product_name', request('product_name')) }}" required>
                </div>
                @error('product_name')
                    <p class="my-1 text-danger small">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" name="product_sku" class="form-control"
                        placeholder="@lang('SKU / Product Code')" value="{{ old('product_sku', request('product_sku')) }}">
                </div>
                @error('product_sku')
                    <p class="my-1 text-danger small">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <select name="brand_id" class="form-control">
                        <option value="">@lang('Select Brand')</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('brand_id')
                    <p class="my-1 text-danger small">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <input type="text" name="brand_name" class="form-control"
                        placeholder="@lang('Brand name (if not listed above)')" value="{{ old('brand_name') }}">
                </div>
                @error('brand_name')
                    <p class="my-1 text-danger small">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label small mb-1">@lang('Product Image') (@lang('optional'))</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                @error('image')
                    <p class="my-1 text-danger small">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="col-md-12">
            <div class="form-group">
                <textarea name="message" class="form-control" rows="{{ $isCompact ? 3 : 4 }}"
                    placeholder="@lang('Additional details or requirements')">{{ old('message') }}</textarea>
            </div>
            @error('message')
                <p class="my-1 text-danger small">{{ $message }}</p>
            @enderror
        </div>

        @if ($gs->is_capcha == 1)
            <div class="col-md-12">
                {!! NoCaptcha::display() !!}
                {!! NoCaptcha::renderJs() !!}
                @error('g-recaptcha-response')
                    <p class="my-1 text-danger small">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="col-md-12">
            <button type="submit" class="template-btn btn-forms">
                {{ $submitLabel ?? __('Request Quote') }}
            </button>
        </div>
    </div>
</form>

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" crossorigin="anonymous">
        <style>
            .quote-request-form .select2-container { width: 100% !important; }
            .quote-request-form .select2-container .select2-selection--single {
                height: 48px;
                border: 1px solid #ced4da;
                border-radius: .375rem;
                padding: 8px 12px;
            }
            .quote-request-form .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 30px;
                padding-left: 0;
                color: #495057;
            }
            .quote-request-form .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 46px;
            }
            .quote-request-form .select2-container--default .select2-selection--single .select2-selection__placeholder {
                color: #6c757d;
            }
        </style>
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" crossorigin="anonymous"></script>
        <script>
            (function () {
                function initQuoteCountrySelect() {
                    if (typeof jQuery === 'undefined' || !jQuery.fn.select2) {
                        return;
                    }
                    jQuery('.quote-country-select').each(function () {
                        var $el = jQuery(this);
                        if ($el.hasClass('select2-hidden-accessible')) {
                            return;
                        }
                        $el.select2({
                            width: '100%',
                            placeholder: $el.data('placeholder') || 'Select Country *',
                            allowClear: false,
                            dropdownParent: $el.closest('.form-group')
                        });
                    });
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initQuoteCountrySelect);
                } else {
                    initQuoteCountrySelect();
                }
                // jQuery ready as fallback if scripts load later
                if (typeof jQuery !== 'undefined') {
                    jQuery(initQuoteCountrySelect);
                }
            })();
        </script>
    @endpush
@endonce
