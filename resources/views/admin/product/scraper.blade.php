@extends('layouts.admin')

@section('styles')
<style>
.scraper-wrap { max-width: 1200px; }
.scraper-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:22px; margin-bottom:20px; }
.scraper-hint { color:#6b7280; font-size:13px; margin-top:6px; }
.scraper-badge { display:inline-block; background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:600; }
.draft-card { border:1px solid #e5e7eb; border-radius:10px; padding:18px; margin-bottom:16px; background:#fafafa; position:relative; }
.draft-card.removed { opacity:.45; }
.draft-card .draft-remove { position:absolute; top:12px; right:12px; }
.draft-thumb { width:110px; height:110px; object-fit:contain; background:#fff; border:1px solid #e5e7eb; border-radius:8px; }
.draft-gallery { display:flex; flex-wrap:wrap; gap:8px; margin-top:8px; }
.draft-gallery label { display:block; width:64px; cursor:pointer; }
.draft-gallery img { width:64px; height:64px; object-fit:contain; border:1px solid #ddd; border-radius:6px; background:#fff; }
.draft-gallery input { display:block; margin:4px auto 0; }
#scraper-results { display:none; }
#scraper-results.active { display:block; }
.scraper-log { background:#111827; color:#e5e7eb; border-radius:8px; padding:12px; max-height:180px; overflow:auto; font-family:Consolas,monospace; font-size:12px; white-space:pre-wrap; margin-top:12px; display:none; }
.scraper-log.active { display:block; }
.gocover-status { position:absolute; top:58%; left:50%; transform:translateX(-50%); color:#fff; font-size:16px; font-weight:600; text-align:center; min-width:280px; }
</style>
@endsection

@section('content')
<div class="content-area">
    <div class="mr-breadcrumb">
        <div class="row">
            <div class="col-lg-12">
                <h4 class="heading">{{ __('Product Scraper') }}</h4>
                <ul class="links">
                    <li><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li><a href="javascript:;">{{ __('Products') }}</a></li>
                    <li><a href="{{ route('admin-prod-scraper') }}">{{ __('Product Scraper') }}</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="add-product-content scraper-wrap p-4">
        <div class="gocover" style="background: url({{ asset('assets/images/'.$gs->admin_loader) }}) no-repeat scroll center center rgba(45,45,45,.5);">
            <div id="gocover-status" class="gocover-status">{{ __('Working...') }}</div>
        </div>

        <div class="scraper-card">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="heading mb-1">{{ __('Scrape products from any website') }}</h4>
                    <p class="scraper-hint mb-0">
                        {{ __('Paste product page URLs (one per line). Review and edit every field before saving. Prices are never imported — products are Request a Quote only.') }}
                    </p>
                </div>
                <span class="scraper-badge">{{ __('Quote only · price = 0') }}</span>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <label>{{ __('Default brand') }}</label>
                    <select id="default-brand-id" class="form-control">
                        <option value="">{{ __('— Auto-detect / type below —') }}</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" id="default-brand" class="form-control mt-2" placeholder="{{ __('Or type a brand name (created if missing)') }}">
                </div>
                <div class="col-lg-6">
                    <label>{{ __('Default category') }}</label>
                    <select id="default-category-id" class="form-control">
                        <option value="">{{ __('— Auto / General —') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" id="default-category" class="form-control mt-2" placeholder="{{ __('Or type a category name') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-lg-6">
                    <input type="text" id="default-subcategory" class="form-control" placeholder="{{ __('Optional subcategory') }}">
                </div>
                <div class="col-lg-6">
                    <input type="text" id="default-childcategory" class="form-control" placeholder="{{ __('Optional child category') }}">
                </div>
            </div>

            <hr>

            <label>{{ __('Discover product links from a listing/category page (optional)') }}</label>
            <div class="input-group mb-2">
                <input type="url" id="listing-url" class="form-control" placeholder="https://supplier.com/category/...">
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary" id="btn-discover">{{ __('Find product URLs') }}</button>
                </div>
            </div>
            <p class="scraper-hint">{{ __('Only use websites you are allowed to copy catalog data from.') }}</p>

            <label class="mt-3">{{ __('Product page URLs') }} *</label>
            <textarea id="product-urls" class="form-control" rows="8" placeholder="https://example.com/product/abc&#10;https://example.com/product/xyz"></textarea>

            <div class="mt-3 d-flex flex-wrap align-items-center">
                <button type="button" class="mybtn1 mr-3" id="btn-scrape">{{ __('Scrape & Preview') }}</button>
                <label class="mb-0 d-flex align-items-center">
                    <input type="checkbox" id="download-images" value="1" checked class="mr-2">
                    <span>{{ __('Download images when saving') }}</span>
                </label>
            </div>

            <div id="scraper-log" class="scraper-log"></div>
        </div>

        <div id="scraper-results">
            <div class="scraper-card">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                    <h4 class="heading mb-0">{{ __('Review & edit before import') }} <span id="draft-count" class="text-muted"></span></h4>
                    <button type="button" class="mybtn1" id="btn-save">{{ __('Save selected products') }}</button>
                </div>
                <p class="scraper-hint">{{ __('Edit name, SKU, brand, description, SEO and images. Description tab on the storefront will show your text; Request a Quote stays without price.') }}</p>
                <div id="drafts-container"></div>
                <div class="text-right mt-3">
                    <button type="button" class="mybtn1" id="btn-save-bottom">{{ __('Save selected products') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var csrf = '{{ csrf_token() }}';
    var scrapeUrl = '{{ route('admin-prod-scraper-scrape') }}';
    var discoverUrl = '{{ route('admin-prod-scraper-discover') }}';
    var saveUrl = '{{ route('admin-prod-scraper-save') }}';
    var drafts = [];

    function showLoader(msg) {
        $('#gocover-status').text(msg || '{{ __("Working...") }}');
        $('.gocover').fadeIn();
    }
    function hideLoader() { $('.gocover').fadeOut(); }

    function logLines(lines) {
        var $log = $('#scraper-log');
        if (!lines || !lines.length) {
            $log.removeClass('active').text('');
            return;
        }
        $log.addClass('active').text(lines.join('\n'));
    }

    function escapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderDrafts() {
        var $box = $('#drafts-container').empty();
        if (!drafts.length) {
            $('#scraper-results').removeClass('active');
            return;
        }
        $('#scraper-results').addClass('active');
        $('#draft-count').text('(' + drafts.length + ')');

        drafts.forEach(function (d, idx) {
            var galleryHtml = '';
            (d.gallery || []).forEach(function (g, gi) {
                galleryHtml += '<label><img src="' + escapeHtml(g) + '" onerror="this.style.opacity=.2"><input type="checkbox" class="gal-check" data-i="' + idx + '" data-g="' + gi + '" checked></label>';
            });

            var html = ''
                + '<div class="draft-card" data-index="' + idx + '">'
                + '  <button type="button" class="btn btn-sm btn-danger draft-remove" data-i="' + idx + '">{{ __("Remove") }}</button>'
                + '  <div class="row">'
                + '    <div class="col-md-2 text-center mb-3">'
                + '      <img class="draft-thumb" src="' + escapeHtml(d.image || '') + '" onerror="this.src=\'{{ asset('assets/images/noimage.png') }}\'">'
                + '      <div class="small text-muted mt-2">{{ __("Main image") }}</div>'
                + '      <input type="url" class="form-control form-control-sm mt-1 fld" data-i="' + idx + '" data-f="image" value="' + escapeHtml(d.image || '') + '">'
                + '      <div class="draft-gallery">' + galleryHtml + '</div>'
                + '    </div>'
                + '    <div class="col-md-10">'
                + '      <div class="row">'
                + '        <div class="col-md-8 mb-2"><label>{{ __("Name") }}</label><input class="form-control fld" data-i="' + idx + '" data-f="name" value="' + escapeHtml(d.name || '') + '"></div>'
                + '        <div class="col-md-4 mb-2"><label>{{ __("SKU") }}</label><input class="form-control fld" data-i="' + idx + '" data-f="sku" value="' + escapeHtml(d.sku || '') + '" placeholder="{{ __("Auto if empty") }}"></div>'
                + '        <div class="col-md-4 mb-2"><label>{{ __("Brand") }}</label><input class="form-control fld" data-i="' + idx + '" data-f="brand" value="' + escapeHtml(d.brand || '') + '"></div>'
                + '        <div class="col-md-4 mb-2"><label>{{ __("Category") }}</label><input class="form-control fld" data-i="' + idx + '" data-f="category" value="' + escapeHtml(d.category || '') + '"></div>'
                + '        <div class="col-md-4 mb-2"><label>{{ __("Subcategory") }}</label><input class="form-control fld" data-i="' + idx + '" data-f="subcategory" value="' + escapeHtml(d.subcategory || '') + '"></div>'
                + '        <div class="col-md-12 mb-2"><label>{{ __("Description (editable)") }}</label><textarea class="form-control fld" rows="5" data-i="' + idx + '" data-f="description">' + escapeHtml(d.description || '') + '</textarea></div>'
                + '        <div class="col-md-6 mb-2"><label>{{ __("Meta description") }}</label><input class="form-control fld" data-i="' + idx + '" data-f="meta_description" value="' + escapeHtml(d.meta_description || '') + '"></div>'
                + '        <div class="col-md-6 mb-2"><label>{{ __("Meta tags") }}</label><input class="form-control fld" data-i="' + idx + '" data-f="meta_tag" value="' + escapeHtml(d.meta_tag || '') + '"></div>'
                + '        <div class="col-md-12 mb-2"><label>{{ __("Source URL") }}</label><input class="form-control fld" data-i="' + idx + '" data-f="source_url" value="' + escapeHtml(d.source_url || '') + '" readonly></div>'
                + '      </div>'
                + '      <div class="small text-success">{{ __("Will save as Request a Quote — price forced to 0.") }}</div>'
                + '    </div>'
                + '  </div>'
                + '</div>';
            $box.append(html);
        });
    }

    function collectProducts() {
        var out = [];
        drafts.forEach(function (d, idx) {
            if (d._removed) return;
            var card = $('.draft-card[data-index="' + idx + '"]');
            var gallery = [];
            card.find('.gal-check:checked').each(function () {
                var gi = parseInt($(this).data('g'), 10);
                if (d.gallery && d.gallery[gi]) gallery.push(d.gallery[gi]);
            });
            out.push({
                name: d.name,
                sku: d.sku,
                brand: d.brand,
                brand_id: d.brand_id || $('#default-brand-id').val() || null,
                category: d.category,
                category_id: d.category_id || $('#default-category-id').val() || null,
                subcategory: d.subcategory,
                childcategory: d.childcategory,
                description: d.description,
                meta_description: d.meta_description,
                meta_tag: d.meta_tag,
                tags: d.tags || '',
                policy: d.policy || '',
                image: d.image,
                gallery: gallery,
                source_url: d.source_url
            });
        });
        return out;
    }

    $(document).on('input change', '.fld', function () {
        var i = parseInt($(this).data('i'), 10);
        var f = $(this).data('f');
        if (drafts[i]) drafts[i][f] = $(this).val();
        if (f === 'image') {
            $(this).closest('.draft-card').find('.draft-thumb').attr('src', $(this).val());
        }
    });

    $(document).on('click', '.draft-remove', function () {
        var i = parseInt($(this).data('i'), 10);
        if (drafts[i]) drafts[i]._removed = true;
        $(this).closest('.draft-card').addClass('removed').slideUp(200);
    });

    $('#btn-discover').on('click', function () {
        var listing = $('#listing-url').val().trim();
        if (!listing) { alert('{{ __("Enter a listing URL first.") }}'); return; }
        showLoader('{{ __("Discovering product URLs...") }}');
        $.ajax({
            url: discoverUrl,
            method: 'POST',
            data: { _token: csrf, listing_url: listing, limit: 40 },
            success: function (res) {
                hideLoader();
                logLines(res.errors || []);
                if (res.urls && res.urls.length) {
                    var current = $('#product-urls').val().trim();
                    var merged = (current ? current + '\n' : '') + res.urls.join('\n');
                    $('#product-urls').val(merged);
                    alert(res.count + ' {{ __("URL(s) found and added.") }}');
                } else {
                    alert('{{ __("No product URLs found.") }}');
                }
            },
            error: function (xhr) {
                hideLoader();
                alert((xhr.responseJSON && xhr.responseJSON.message) || '{{ __("Discover failed.") }}');
            }
        });
    });

    $('#btn-scrape').on('click', function () {
        var urls = $('#product-urls').val().trim();
        if (!urls) { alert('{{ __("Paste at least one product URL.") }}'); return; }
        showLoader('{{ __("Scraping product pages...") }}');
        $.ajax({
            url: scrapeUrl,
            method: 'POST',
            data: {
                _token: csrf,
                urls: urls,
                brand_id: $('#default-brand-id').val(),
                category_id: $('#default-category-id').val(),
                brand: $('#default-brand').val(),
                category: $('#default-category').val(),
                subcategory: $('#default-subcategory').val(),
                childcategory: $('#default-childcategory').val()
            },
            success: function (res) {
                hideLoader();
                logLines(res.errors || []);
                drafts = (res.drafts || []).map(function (d) { d._removed = false; return d; });
                renderDrafts();
                if (!drafts.length) alert('{{ __("No products could be scraped.") }}');
            },
            error: function (xhr) {
                hideLoader();
                alert((xhr.responseJSON && (xhr.responseJSON.message || (xhr.responseJSON.errors && JSON.stringify(xhr.responseJSON.errors)))) || '{{ __("Scrape failed.") }}');
            }
        });
    });

    function saveProducts() {
        var products = collectProducts();
        if (!products.length) { alert('{{ __("No products to save.") }}'); return; }
        showLoader('{{ __("Saving products & downloading images...") }}');
        $.ajax({
            url: saveUrl,
            method: 'POST',
            data: {
                _token: csrf,
                products: products,
                download_images: $('#download-images').is(':checked') ? 1 : 0
            },
            success: function (res) {
                hideLoader();
                var lines = [];
                (res.created || []).forEach(function (p) { lines.push('OK #' + p.id + ' ' + p.sku + ' — ' + p.name); });
                (res.skipped || []).forEach(function (s) { lines.push('SKIP ' + s); });
                (res.errors || []).forEach(function (e) { lines.push('ERR ' + e); });
                logLines(lines);
                alert(res.message || '{{ __("Done.") }}');
                if (res.created && res.created.length) {
                    // drop saved drafts
                    var savedSkus = {};
                    res.created.forEach(function (p) { savedSkus[p.sku] = true; });
                    drafts = drafts.filter(function (d) { return d._removed || !savedSkus[d.sku]; });
                    renderDrafts();
                }
            },
            error: function (xhr) {
                hideLoader();
                alert((xhr.responseJSON && xhr.responseJSON.message) || '{{ __("Save failed.") }}');
            }
        });
    }

    $('#btn-save, #btn-save-bottom').on('click', saveProducts);
})();
</script>
@endsection
