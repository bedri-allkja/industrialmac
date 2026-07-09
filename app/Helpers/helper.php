<?php

/**
 * Primary site brand logo (INDUSTRIALMAC) — header, footer, invoices, favicon, etc.
 */
function site_brand_logo(): string
{
    return asset('assets/images/INDUSTRIALMAC.png');
}

/**
 * Standard "request a quote" description shown on EVERY product page.
 *
 * The product name and brand name are injected dynamically and the copy is
 * language-aware (English + Italian). Because it is generated at render time it
 * always reflects the live product/brand and never has to be stored per product.
 */
function product_quote_description($product): string
{
    $productName = trim((string) ($product->name ?? ''));
    $brandName = null;
    if (isset($product->brand) && $product->brand && !empty($product->brand->name)) {
        $brandName = trim((string) $product->brand->name);
    }

    // Locale code assigned to the Italian language for this install
    // (Admin > Languages). English is the default for every other locale.
    $isItalian = app()->getLocale() === 'industrialmac_it';

    return $isItalian
        ? product_quote_description_it($productName, $brandName)
        : product_quote_description_en($productName, $brandName);
}

function product_quote_description_en(string $productName, ?string $brandName): string
{
    $product = '<strong>"' . e($productName) . '"</strong>';
    $company = '<strong>INDUSTRIALMAC</strong>';

    $intro = $brandName
        ? 'Contact us to receive a personalized quotation for the ' . $product
            . ' manufactured by <strong>"' . e($brandName) . '"</strong>.'
        : 'Contact us to receive a personalized quotation for the ' . $product . '.';

    $disclaimerBrand = $brandName
        ? '<strong>"' . e($brandName) . '"</strong>'
        : 'the respective manufacturer';

    $p = [];
    $p[] = $intro;
    $p[] = 'The ' . $company . ' sales team is ready to provide you with up-to-date pricing, '
        . 'availability, delivery times, and suitable alternative solutions tailored to your requirements.';
    $p[] = 'We supply <strong>new</strong> and <strong>original</strong> products only, carefully sourced '
        . 'through our qualified international supplier network. Whenever applicable, products are covered by '
        . "the <strong>manufacturer's warranty</strong>.";
    $p[] = 'Thanks to our headquarters in Italy and our well-established global sourcing network, we are able '
        . 'to offer <strong>fast worldwide delivery</strong>, secure shipping, competitive pricing, and '
        . 'professional customer support throughout the entire purchasing process.';
    $p[] = 'If you are looking for additional part numbers, spare parts, or other products from the same '
        . 'manufacturer, our technical sales specialists will be happy to assist you. Upon request, we can '
        . 'also provide product catalogs, technical datasheets, and PDF documentation.';
    $p[] = '<strong>Disclaimer:</strong> INDUSTRIALMAC is an independent industrial supplier and is not '
        . 'necessarily an authorized distributor or representative of ' . $disclaimerBrand . '. All trademarks, '
        . 'logos, brand names, and product codes displayed on this website remain the property of their '
        . 'respective owners and are used for identification purposes only.';

    return '<div class="product-quote-desc"><p>' . implode('</p><p>', $p) . '</p></div>';
}

function product_quote_description_it(string $productName, ?string $brandName): string
{
    $product = '<strong>"' . e($productName) . '"</strong>';
    $company = '<strong>INDUSTRIALMAC</strong>';

    $intro = $brandName
        ? 'Contattaci per ricevere un\'offerta personalizzata sul prodotto ' . $product
            . ' del marchio <strong>"' . e($brandName) . '"</strong>.'
        : 'Contattaci per ricevere un\'offerta personalizzata sul prodotto ' . $product . '.';

    $disclaimerBrand = $brandName
        ? 'del marchio <strong>"' . e($brandName) . '"</strong>'
        : 'del rispettivo produttore';

    $p = [];
    $p[] = $intro;
    $p[] = 'Il team di ' . $company . ' è a tua disposizione per fornirti rapidamente informazioni su prezzi '
        . 'aggiornati, disponibilità, tempi di consegna e soluzioni alternative compatibili con le tue esigenze.';
    $p[] = 'Forniamo esclusivamente <strong>prodotti nuovi e originali</strong>, accuratamente selezionati dai '
        . 'nostri fornitori qualificati. Quando previsto dal produttore, i prodotti sono coperti dalla '
        . '<strong>garanzia ufficiale</strong>.';
    $p[] = 'Grazie alla nostra consolidata rete di approvvigionamento internazionale e alla sede operativa in '
        . 'Italia, siamo in grado di offrire <strong>consegne rapide</strong>, spedizioni sicure e un supporto '
        . 'commerciale altamente professionale, seguendo il cliente in ogni fase dell\'acquisto.';
    $p[] = 'Se hai bisogno di altri codici, ricambi o prodotti dello stesso marchio, il nostro team '
        . 'tecnico-commerciale sarà lieto di aiutarti a individuare la soluzione più adatta e, su richiesta, '
        . 'potrà fornirti cataloghi, schede tecniche e documentazione in formato PDF.';
    $p[] = '<strong>Nota:</strong> INDUSTRIALMAC è un fornitore indipendente di componenti industriali e non è '
        . 'necessariamente un distributore o rappresentante autorizzato ' . $disclaimerBrand . '. Tutti i marchi, '
        . 'loghi, nomi commerciali e codici prodotto presenti sul sito appartengono ai rispettivi proprietari e '
        . 'sono utilizzati esclusivamente a scopo identificativo.';

    return '<div class="product-quote-desc"><p>' . implode('</p><p>', $p) . '</p></div>';
}

function brand_logo_url($brand): ?string
{
    if (!$brand) {
        return null;
    }

    $image = $brand->image ?: $brand->photo;

    if (!$image) {
        return null;
    }

    if (filter_var($image, FILTER_VALIDATE_URL)) {
        return $image;
    }

    return asset('assets/images/brands/' . $image);
}

function front_top_brands(int $limit = 8)
{
    return \Illuminate\Support\Facades\Cache::remember(
        'front.top_brands.' . $limit,
        3600,
        function () use ($limit) {
            // 1) Admin-curated: brands the admin marked as "featured" (with a logo)
            //    control this homepage strip. Managed from Admin > Brands.
            $hasImage = function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('image')->where('image', '!=', '')
                        ->orWhere(function ($q2) {
                            $q2->whereNotNull('photo')->where('photo', '!=', '');
                        });
                });
            };

            $featured = \App\Models\Brand::where('is_featured', 1)
                ->where($hasImage)
                ->orderBy('name')
                ->take($limit)
                ->get(['id', 'name', 'slug', 'image', 'photo']);

            if ($featured->isNotEmpty()) {
                return $featured->each(function ($brand) {
                    $brand->products_count = 0;
                });
            }

            // 2) Fallback (no featured brands chosen yet): auto-pick the brands
            //    that have the most products so the section is never empty.
            $rows = \Illuminate\Support\Facades\DB::table('products')
                ->select('brand_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as products_count'))
                ->where('status', 1)
                ->whereNotNull('brand_id')
                ->groupBy('brand_id')
                ->orderByDesc('products_count')
                ->limit(50)
                ->get();

            if ($rows->isEmpty()) {
                return collect();
            }

            $counts = $rows->pluck('products_count', 'brand_id');

            return \App\Models\Brand::whereIn('id', $rows->pluck('brand_id'))
                ->where(function ($query) {
                    $query->whereNotNull('image')->where('image', '!=', '')
                        ->orWhere(function ($query) {
                            $query->whereNotNull('photo')->where('photo', '!=', '');
                        });
                })
                ->get(['id', 'name', 'slug', 'image', 'photo'])
                ->sortByDesc(fn ($brand) => $counts[$brand->id] ?? 0)
                ->take($limit)
                ->values()
                ->each(function ($brand) use ($counts) {
                    $brand->products_count = $counts[$brand->id] ?? 0;
                });
        }
    );
}

function front_top_categories(int $limit = 6, ?bool $featuredOnly = null)
{
    $cacheKey = 'front.top_categories.' . $limit . '.' . ($featuredOnly ? '1' : '0');

    return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($limit, $featuredOnly) {
        $categoryQuery = \App\Models\Category::query()->where('status', 1);

        if ($featuredOnly) {
            $categoryQuery->where('is_featured', 1);
        }

        $eligibleIds = $categoryQuery->pluck('id');

        if ($eligibleIds->isEmpty()) {
            return collect();
        }

        $rows = \Illuminate\Support\Facades\DB::table('products')
            ->select('category_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as products_count'))
            ->where('status', 1)
            ->whereIn('category_id', $eligibleIds)
            ->groupBy('category_id')
            ->orderByDesc('products_count')
            ->limit($limit)
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $counts = $rows->pluck('products_count', 'category_id');

        return \App\Models\Category::whereIn('id', $rows->pluck('category_id'))
            ->get()
            ->sortByDesc(fn ($category) => $counts[$category->id] ?? 0)
            ->values()
            ->each(function ($category) use ($counts) {
                $category->products_count = $counts[$category->id] ?? 0;
            });
    });
}

function front_nav_categories(int $limit = 12)
{
    return \Illuminate\Support\Facades\Cache::remember(
        'front.nav_categories.' . $limit,
        3600,
        function () use ($limit) {
            $rows = \Illuminate\Support\Facades\DB::table('products')
                ->select('category_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as products_count'))
                ->where('status', 1)
                ->whereNotNull('category_id')
                ->groupBy('category_id')
                ->orderByDesc('products_count')
                ->limit($limit)
                ->get();

            if ($rows->isEmpty()) {
                return collect();
            }

            $counts = $rows->pluck('products_count', 'category_id');

            return \App\Models\Category::with('subs')
                ->whereIn('id', $rows->pluck('category_id'))
                ->get()
                ->sortByDesc(fn ($category) => $counts[$category->id] ?? 0)
                ->values()
                ->each(function ($category) use ($counts) {
                    $category->products_count = $counts[$category->id] ?? 0;
                });
        }
    );
}

function front_menu_categories()
{
    return \Illuminate\Support\Facades\Cache::remember(
        'front.menu_categories',
        3600,
        fn () => \App\Models\Category::with(['subs.childs'])
            ->where('status', 1)
            ->orderBy('name')
            ->get()
    );
}

function front_product_flags(): array
{
    return \Illuminate\Support\Facades\Cache::remember('front.product_flags', 3600, function () {
        $row = \Illuminate\Support\Facades\DB::table('products')
            ->where('status', 1)
            ->selectRaw('MAX(best) as best, MAX(featured) as featured, MAX(sale) as sale, MAX(trending) as trending, MAX(hot) as hot, MAX(top) as top, MAX(big) as big')
            ->first();

        return [
            'best' => (bool) ($row->best ?? 0),
            'featured' => (bool) ($row->featured ?? 0),
            'sale' => (bool) ($row->sale ?? 0),
            'trending' => (bool) ($row->trending ?? 0),
            'hot' => (bool) ($row->hot ?? 0),
            'top' => (bool) ($row->top ?? 0),
            'big' => (bool) ($row->big ?? 0),
        ];
    });
}

function front_has_flagged_products(string $flag): bool
{
    $flags = front_product_flags();

    return $flags[$flag] ?? false;
}

function site_faq_locale(): string
{
    return app()->getLocale() === 'industrialmac_it' ? 'it' : 'en';
}

function site_faqs(?int $limit = null): array
{
    $faqs = config('faq.' . site_faq_locale());

    $items = collect($faqs['items'] ?? [])->map(function ($item) {
        return (object) [
            'title' => $item['title'],
            'details' => $item['answer'],
        ];
    });

    if ($limit !== null) {
        $items = $items->take($limit);
    }

    return $items->values()->all();
}

function site_faq_content(): array
{
    return config('faq.' . site_faq_locale(), config('faq.en'));
}

function wishlistCheck($product_id)
{
    $wishlist = \App\Models\Wishlist::where('product_id', $product_id)->where('user_id', auth()->id())->first();
    if ($wishlist) {
        return true;
    } else {
        return false;
    }

}

function addon($name)
{

    if ($name == "otp") {
        $otp = file_exists(base_path("/vendor/markury/src/Adapter/addon/otp.txt"));
        if ($otp) {
            $data = file_get_contents(base_path("/vendor/markury/src/Adapter/addon/otp.txt"));

            if ($data) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    return false;
}
