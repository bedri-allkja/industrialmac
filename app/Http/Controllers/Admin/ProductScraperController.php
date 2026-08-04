<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Services\ProductScraperService;
use Illuminate\Http\Request;

class ProductScraperController extends AdminBaseController
{
    public function index()
    {
        $brands = Brand::orderBy('name')->get(['id', 'name']);
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.product.scraper', compact('brands', 'categories'));
    }

    public function discover(Request $request, ProductScraperService $scraper)
    {
        $data = $request->validate([
            'listing_url' => 'required|url',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $result = $scraper->discoverProductUrls(
            $data['listing_url'],
            (int) ($data['limit'] ?? 40)
        );

        return response()->json([
            'status' => true,
            'urls' => $result['urls'],
            'errors' => $result['errors'],
            'count' => count($result['urls']),
        ]);
    }

    public function scrape(Request $request, ProductScraperService $scraper)
    {
        $data = $request->validate([
            'urls' => 'required|string',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand' => 'nullable|string|max:191',
            'category' => 'nullable|string|max:191',
            'subcategory' => 'nullable|string|max:191',
            'childcategory' => 'nullable|string|max:191',
        ]);

        $urls = preg_split('/\r\n|\r|\n/', $data['urls']) ?: [];
        $urls = array_values(array_unique(array_filter(array_map('trim', $urls))));
        $urls = array_slice($urls, 0, 40);

        if ($urls === []) {
            return response()->json([
                'status' => false,
                'message' => __('Please paste at least one product URL.'),
            ], 422);
        }

        $defaults = [
            'brand_id' => $data['brand_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'brand' => $data['brand'] ?? '',
            'category' => $data['category'] ?? '',
            'subcategory' => $data['subcategory'] ?? '',
            'childcategory' => $data['childcategory'] ?? '',
        ];

        if (! empty($defaults['brand_id'])) {
            $brand = Brand::find($defaults['brand_id']);
            if ($brand && empty($defaults['brand'])) {
                $defaults['brand'] = $brand->name;
            }
        }
        if (! empty($defaults['category_id'])) {
            $category = Category::find($defaults['category_id']);
            if ($category && empty($defaults['category'])) {
                $defaults['category'] = $category->name;
            }
        }

        $result = $scraper->scrapeUrls($urls, $defaults);

        return response()->json([
            'status' => true,
            'drafts' => $result['drafts'],
            'errors' => $result['errors'],
            'count' => count($result['drafts']),
            'note' => __('All products will be saved as Request a Quote (price = 0). Scraped prices are ignored.'),
        ]);
    }

    public function save(Request $request, ProductScraperService $scraper)
    {
        $data = $request->validate([
            'products' => 'required|array|min:1|max:40',
            'products.*.name' => 'required|string|max:255',
            'products.*.sku' => 'nullable|string|max:100',
            'products.*.brand' => 'nullable|string|max:191',
            'products.*.brand_id' => 'nullable|integer',
            'products.*.category' => 'nullable|string|max:191',
            'products.*.category_id' => 'nullable|integer',
            'products.*.subcategory' => 'nullable|string|max:191',
            'products.*.childcategory' => 'nullable|string|max:191',
            'products.*.description' => 'nullable|string',
            'products.*.meta_description' => 'nullable|string|max:500',
            'products.*.meta_tag' => 'nullable|string|max:500',
            'products.*.tags' => 'nullable|string|max:500',
            'products.*.policy' => 'nullable|string',
            'products.*.image' => 'nullable|string|max:1000',
            'products.*.gallery' => 'nullable|array',
            'products.*.gallery.*' => 'nullable|string|max:1000',
            'products.*.source_url' => 'nullable|url',
            'download_images' => 'nullable|boolean',
        ]);

        $result = $scraper->saveDrafts(
            $data['products'],
            $request->boolean('download_images', true)
        );

        return response()->json([
            'status' => count($result['created']) > 0,
            'created' => $result['created'],
            'skipped' => $result['skipped'],
            'errors' => $result['errors'],
            'message' => count($result['created'])
                . ' ' . __('product(s) saved as Request a Quote (no price).'),
        ]);
    }
}
