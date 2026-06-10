<?php

/**
 * Primary site brand logo (INDUSTRIALMAC) — header, footer, invoices, favicon, etc.
 */
function site_brand_logo(): string
{
    return asset('assets/images/INDUSTRIALMAC.png');
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
