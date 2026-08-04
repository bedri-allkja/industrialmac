<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Childcategory;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class ProductScraperService
{
    private const USER_AGENT = 'Mozilla/5.0 (compatible; IndustrialMacCatalogBot/1.0; +https://industrialmac.com)';

    /**
     * Scrape one or more product page URLs into editable draft arrays (no DB writes).
     *
     * @param  array<int, string>  $urls
     * @return array{drafts: array<int, array>, errors: array<int, string>}
     */
    public function scrapeUrls(array $urls, array $defaults = []): array
    {
        $drafts = [];
        $errors = [];

        foreach ($urls as $index => $url) {
            $url = trim((string) $url);
            if ($url === '') {
                continue;
            }
            if (! filter_var($url, FILTER_VALIDATE_URL) || ! preg_match('#^https?://#i', $url)) {
                $errors[] = "Invalid URL: {$url}";
                continue;
            }

            try {
                $html = $this->fetchHtml($url);
                $draft = $this->parseProductPage($html, $url, $defaults);
                if ($draft['name'] === '' && $draft['sku'] === '') {
                    $errors[] = "Could not detect a product on: {$url}";
                    continue;
                }
                $drafts[] = $draft;
            } catch (Throwable $e) {
                $errors[] = "Failed {$url}: " . $e->getMessage();
            }
        }

        return compact('drafts', 'errors');
    }

    /**
     * From a category/listing page, collect product-looking links (capped).
     *
     * @return array{urls: array<int, string>, errors: array<int, string>}
     */
    public function discoverProductUrls(string $listingUrl, int $limit = 40): array
    {
        $errors = [];
        $urls = [];

        $listingUrl = trim($listingUrl);
        if (! filter_var($listingUrl, FILTER_VALIDATE_URL)) {
            return ['urls' => [], 'errors' => ['Invalid listing URL.']];
        }

        try {
            $html = $this->fetchHtml($listingUrl);
            $base = $this->baseUrl($listingUrl);
            $candidates = [];

            if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $html, $matches)) {
                foreach ($matches[1] as $href) {
                    $absolute = $this->absolutizeUrl($href, $base);
                    if (! $absolute) {
                        continue;
                    }
                    if ($this->looksLikeProductUrl($absolute, $listingUrl)) {
                        $candidates[$absolute] = true;
                    }
                }
            }

            $urls = array_slice(array_keys($candidates), 0, max(1, min(100, $limit)));
            if ($urls === []) {
                $errors[] = 'No product-like links found on that page. Paste product URLs directly instead.';
            }
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }

        return compact('urls', 'errors');
    }

    /**
     * Persist admin-edited drafts as quote-only Physical products (price = 0).
     *
     * @param  array<int, array>  $drafts
     * @return array{created: array<int, array{id:int,name:string,sku:string}>, skipped: array<int, string>, errors: array<int, string>}
     */
    public function saveDrafts(array $drafts, bool $downloadImages = true): array
    {
        $created = [];
        $skipped = [];
        $errors = [];

        foreach ($drafts as $i => $draft) {
            try {
                $name = trim((string) ($draft['name'] ?? ''));
                $sku = trim((string) ($draft['sku'] ?? ''));

                if ($name === '') {
                    $errors[] = 'Row ' . ($i + 1) . ': name is required.';
                    continue;
                }

                if ($sku === '') {
                    $sku = $this->generateSku($name, (string) ($draft['source_url'] ?? ''));
                }
                $sku = Str::limit($sku, 100, '');

                if (Product::where('sku', $sku)->exists()) {
                    $skipped[] = "SKU already exists, skipped: {$sku}";
                    continue;
                }

                $brandId = $this->resolveBrandId(
                    trim((string) ($draft['brand'] ?? '')),
                    isset($draft['brand_id']) ? (int) $draft['brand_id'] : null
                );

                if (! $brandId) {
                    $errors[] = "Row " . ($i + 1) . " ({$name}): brand is required.";
                    continue;
                }

                $categoryId = $this->resolveCategoryId(
                    trim((string) ($draft['category'] ?? '')),
                    isset($draft['category_id']) ? (int) $draft['category_id'] : null
                );

                $subcategoryId = null;
                $childcategoryId = null;
                if (! empty($draft['subcategory'])) {
                    $subcategoryId = $this->resolveSubcategoryId($categoryId, trim((string) $draft['subcategory']));
                }
                if ($subcategoryId && ! empty($draft['childcategory'])) {
                    $childcategoryId = $this->resolveChildcategoryId($subcategoryId, trim((string) $draft['childcategory']));
                }

                $imageUrl = trim((string) ($draft['image'] ?? ''));
                $galleryUrls = array_values(array_filter(array_map('trim', (array) ($draft['gallery'] ?? []))));

                $photos = ['photo' => 'noimage.png', 'thumbnail' => 'noimage.png'];
                if ($downloadImages && $imageUrl !== '') {
                    $photos = $this->downloadImage($imageUrl);
                }

                $details = (string) ($draft['description'] ?? '');
                $metaDescription = trim((string) ($draft['meta_description'] ?? ''));
                if ($metaDescription === '') {
                    $metaDescription = Str::limit(trim(strip_tags($details !== '' ? $details : $name)), 160, '');
                }

                $metaTag = trim((string) ($draft['meta_tag'] ?? ''));
                if ($metaTag === '') {
                    $metaTag = implode(',', array_filter([
                        $name,
                        $sku,
                        trim((string) ($draft['brand'] ?? '')),
                        'industrial parts',
                        'Industrialmac',
                    ]));
                }

                $slugBase = Str::slug($name, '-') ?: 'product';
                $slug = $slugBase . '-' . Str::slug($sku, '-');
                if (Product::where('slug', $slug)->exists()) {
                    $slug .= '-' . Str::lower(Str::random(4));
                }

                $product = new Product();
                $product->fill([
                    'type' => 'Physical',
                    'product_type' => 'normal',
                    'sku' => $sku,
                    'user_id' => 0,
                    'brand_id' => $brandId,
                    'category_id' => $categoryId,
                    'subcategory_id' => $subcategoryId,
                    'childcategory_id' => $childcategoryId,
                    'photo' => $photos['photo'],
                    'thumbnail' => $photos['thumbnail'],
                    'name' => $name,
                    'details' => $details,
                    'price' => 0,
                    'previous_price' => null,
                    'stock' => 0,
                    'policy' => (string) ($draft['policy'] ?? ''),
                    'meta_tag' => $metaTag,
                    'meta_description' => $metaDescription,
                    'tags' => trim((string) ($draft['tags'] ?? '')),
                    'affiliate_link' => trim((string) ($draft['source_url'] ?? '')),
                    'latest' => 1,
                    'status' => 1,
                    'slug' => $slug,
                ]);
                $product->save();

                if ($downloadImages) {
                    foreach (array_slice($galleryUrls, 0, 8) as $gUrl) {
                        if ($gUrl === '' || $gUrl === $imageUrl) {
                            continue;
                        }
                        $gPhoto = $this->downloadGalleryImage($gUrl);
                        if ($gPhoto) {
                            Gallery::create([
                                'product_id' => $product->id,
                                'photo' => $gPhoto,
                            ]);
                        }
                    }
                }

                $created[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'slug' => $product->slug,
                ];
            } catch (Throwable $e) {
                $errors[] = 'Row ' . ($i + 1) . ': ' . $e->getMessage();
            }
        }

        return compact('created', 'skipped', 'errors');
    }

    private function fetchHtml(string $url): string
    {
        $request = Http::timeout(25)
            ->withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html,application/xhtml+xml',
                'Accept-Language' => 'en-US,en;q=0.9',
            ]);

        if (app()->environment('local')) {
            $request = $request->withoutVerifying();
        }

        $response = $request->get($url);
        if (! $response->successful()) {
            throw new \RuntimeException('HTTP ' . $response->status());
        }

        $body = $response->body();
        if (strlen($body) < 40) {
            throw new \RuntimeException('Empty response body');
        }

        return $body;
    }

    private function parseProductPage(string $html, string $url, array $defaults = []): array
    {
        $jsonLd = $this->extractJsonLdProduct($html);
        $og = $this->extractOpenGraph($html);
        $meta = $this->extractMetaTags($html);

        $name = $this->firstNonEmpty([
            $jsonLd['name'] ?? null,
            $og['og:title'] ?? null,
            $meta['twitter:title'] ?? null,
            $this->matchContent($html, '/<h1[^>]*>(.*?)<\/h1>/is'),
            $this->matchContent($html, '/<title[^>]*>(.*?)<\/title>/is'),
        ]);
        $name = html_entity_decode(strip_tags($name), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $name = trim(preg_replace('/\s+/u', ' ', $name) ?? $name);

        $description = $this->firstNonEmpty([
            $jsonLd['description'] ?? null,
            $og['og:description'] ?? null,
            $meta['description'] ?? null,
            $meta['twitter:description'] ?? null,
        ]);
        $description = html_entity_decode((string) $description, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $sku = $this->firstNonEmpty([
            $jsonLd['sku'] ?? null,
            $jsonLd['mpn'] ?? null,
            $jsonLd['productID'] ?? null,
            $this->metaItemprop($html, 'sku'),
            $this->matchContent($html, '/(?:sku|article|part\s*number|codice|cod\.?)\s*[:#]?\s*([A-Z0-9][A-Z0-9\-_\/\.]{3,})/i'),
        ]);
        $sku = trim((string) $sku);

        $brand = $this->firstNonEmpty([
            is_array($jsonLd['brand'] ?? null) ? ($jsonLd['brand']['name'] ?? null) : ($jsonLd['brand'] ?? null),
            $og['product:brand'] ?? null,
            $this->metaItemprop($html, 'brand'),
            $defaults['brand'] ?? null,
        ]);
        $brand = trim(html_entity_decode(strip_tags((string) $brand), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        $images = [];
        foreach ((array) ($jsonLd['image'] ?? []) as $img) {
            if (is_array($img)) {
                $img = $img['url'] ?? $img[0] ?? null;
            }
            if (is_string($img) && $img !== '') {
                $images[] = $img;
            }
        }
        if (! empty($og['og:image'])) {
            $images[] = $og['og:image'];
        }
        if (! empty($meta['twitter:image'])) {
            $images[] = $meta['twitter:image'];
        }
        foreach ($this->extractImgSrcs($html, $this->baseUrl($url)) as $src) {
            $images[] = $src;
        }

        $images = $this->normalizeImageList($images, $url);
        $mainImage = $images[0] ?? '';
        $gallery = array_values(array_slice($images, 1, 8));

        $category = trim((string) ($defaults['category'] ?? ''));
        if ($category === '' && ! empty($jsonLd['category'])) {
            $category = is_array($jsonLd['category'])
                ? (string) ($jsonLd['category']['name'] ?? reset($jsonLd['category']) ?: '')
                : (string) $jsonLd['category'];
        }

        return [
            'source_url' => $url,
            'name' => $name,
            'sku' => $sku,
            'brand' => $brand,
            'brand_id' => $defaults['brand_id'] ?? null,
            'category' => $category,
            'category_id' => $defaults['category_id'] ?? null,
            'subcategory' => trim((string) ($defaults['subcategory'] ?? '')),
            'childcategory' => trim((string) ($defaults['childcategory'] ?? '')),
            'description' => $description,
            'meta_description' => Str::limit(trim(strip_tags($description)), 160, ''),
            'meta_tag' => '',
            'tags' => '',
            'policy' => '',
            'image' => $mainImage,
            'gallery' => $gallery,
            'price_ignored' => true,
            'quote_only' => true,
        ];
    }

    private function extractJsonLdProduct(string $html): array
    {
        if (! preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $blocks)) {
            return [];
        }

        foreach ($blocks[1] as $raw) {
            $raw = html_entity_decode(trim($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $decoded = json_decode($raw, true);
            if (! is_array($decoded)) {
                continue;
            }

            $candidates = [];
            if (isset($decoded['@graph']) && is_array($decoded['@graph'])) {
                $candidates = $decoded['@graph'];
            } elseif (array_is_list($decoded)) {
                $candidates = $decoded;
            } else {
                $candidates = [$decoded];
            }

            foreach ($candidates as $node) {
                if (! is_array($node)) {
                    continue;
                }
                $type = $node['@type'] ?? '';
                if (is_array($type)) {
                    $type = implode(',', $type);
                }
                if (stripos((string) $type, 'Product') !== false) {
                    return $node;
                }
            }
        }

        return [];
    }

    private function extractOpenGraph(string $html): array
    {
        $data = [];
        if (preg_match_all('/<meta[^>]+property=["\']([^"\']+)["\'][^>]+content=["\']([^"\']*)["\']/i', $html, $m, PREG_SET_ORDER)) {
            foreach ($m as $row) {
                $data[$row[1]] = html_entity_decode($row[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
        if (preg_match_all('/<meta[^>]+content=["\']([^"\']*)["\'][^>]+property=["\']([^"\']+)["\']/i', $html, $m, PREG_SET_ORDER)) {
            foreach ($m as $row) {
                $data[$row[2]] = html_entity_decode($row[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        return $data;
    }

    private function extractMetaTags(string $html): array
    {
        $data = [];
        if (preg_match_all('/<meta[^>]+name=["\']([^"\']+)["\'][^>]+content=["\']([^"\']*)["\']/i', $html, $m, PREG_SET_ORDER)) {
            foreach ($m as $row) {
                $data[strtolower($row[1])] = html_entity_decode($row[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
        if (preg_match_all('/<meta[^>]+content=["\']([^"\']*)["\'][^>]+name=["\']([^"\']+)["\']/i', $html, $m, PREG_SET_ORDER)) {
            foreach ($m as $row) {
                $data[strtolower($row[2])] = html_entity_decode($row[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        return $data;
    }

    private function metaItemprop(string $html, string $prop): string
    {
        $patterns = [
            '/itemprop=["\']' . preg_quote($prop, '/') . '["\'][^>]*content=["\']([^"\']+)["\']/i',
            '/content=["\']([^"\']+)["\'][^>]*itemprop=["\']' . preg_quote($prop, '/') . '["\']/i',
            '/itemprop=["\']' . preg_quote($prop, '/') . '["\'][^>]*>\s*([^<]{1,120})</i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                return trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            }
        }

        return '';
    }

    private function extractImgSrcs(string $html, string $base): array
    {
        $out = [];
        if (! preg_match_all('/<img[^>]+(?:src|data-src|data-zoom-image)=["\']([^"\']+)["\']/i', $html, $m)) {
            return $out;
        }
        foreach ($m[1] as $src) {
            $absolute = $this->absolutizeUrl($src, $base);
            if ($absolute) {
                $out[] = $absolute;
            }
        }

        return $out;
    }

    private function normalizeImageList(array $images, string $pageUrl): array
    {
        $base = $this->baseUrl($pageUrl);
        $unique = [];
        foreach ($images as $img) {
            if (! is_string($img) || $img === '') {
                continue;
            }
            $absolute = $this->absolutizeUrl($img, $base);
            if (! $absolute || ! preg_match('#^https?://#i', $absolute)) {
                continue;
            }
            $lower = strtolower($absolute);
            if (str_contains($lower, 'data:image')) {
                continue;
            }
            if (preg_match('/(sprite|logo|icon|favicon|1x1|pixel|placeholder|blank\.(gif|png)|loading)/i', $lower)) {
                continue;
            }
            $unique[$absolute] = true;
        }

        return array_keys($unique);
    }

    private function looksLikeProductUrl(string $url, string $listingUrl): bool
    {
        $parts = parse_url($url);
        $listing = parse_url($listingUrl);
        if (empty($parts['host']) || empty($listing['host'])) {
            return false;
        }
        if (strcasecmp($parts['host'], $listing['host']) !== 0) {
            return false;
        }

        $path = $parts['path'] ?? '/';
        if ($path === '/' || $path === ($listing['path'] ?? '')) {
            return false;
        }

        if (preg_match('#/(cart|checkout|login|account|wishlist|search|tag|blog|news|privacy|cookie|contact)(/|$)#i', $path)) {
            return false;
        }

        return (bool) preg_match('#/(product|products|item|p|dp|sku|catalog|shop)/|/p[-_/]|[-_/]p\d|/pd/|/goods/#i', $path)
            || preg_match('#/[a-z0-9\-]{8,}\.html?$#i', $path);
    }

    private function downloadImage(string $imageUrl): array
    {
        $placeholder = ['photo' => 'noimage.png', 'thumbnail' => 'noimage.png'];

        try {
            $request = Http::timeout(20)->withHeaders(['User-Agent' => self::USER_AGENT]);
            if (app()->environment('local')) {
                $request = $request->withoutVerifying();
            }
            $response = $request->get($imageUrl);
            if (! $response->successful()) {
                return $placeholder;
            }

            $contentType = strtolower((string) $response->header('Content-Type'));
            if ($contentType !== '' && ! str_contains($contentType, 'image') && ! str_contains($contentType, 'octet-stream')) {
                return $placeholder;
            }

            $extension = 'jpg';
            if (str_contains($contentType, 'png') || str_ends_with(strtolower(parse_url($imageUrl, PHP_URL_PATH) ?? ''), '.png')) {
                $extension = 'png';
            } elseif (str_contains($contentType, 'webp')) {
                $extension = 'webp';
            }

            $fphoto = time() . Str::random(8) . '.' . $extension;
            $thumbnail = time() . Str::random(8) . '.jpg';
            $productPath = public_path('assets/images/products/' . $fphoto);
            $thumbnailPath = public_path('assets/images/thumbnails/' . $thumbnail);

            file_put_contents($productPath, $response->body());

            if (class_exists(\Intervention\Image\Facades\Image::class) && extension_loaded('gd')) {
                try {
                    $image = \Intervention\Image\Facades\Image::make($productPath);
                    $image->resize(1000, 1000, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->encode($extension === 'png' ? 'png' : 'jpg', 90)->save($productPath);
                    $image->destroy();

                    $thumb = \Intervention\Image\Facades\Image::make($productPath);
                    $thumb->fit(700, 700)->encode('jpg', 90)->save($thumbnailPath);
                    $thumb->destroy();
                } catch (Throwable $e) {
                    @copy($productPath, $thumbnailPath);
                }
            } else {
                @copy($productPath, $thumbnailPath);
            }

            return ['photo' => $fphoto, 'thumbnail' => $thumbnail];
        } catch (Throwable $e) {
            return $placeholder;
        }
    }

    private function downloadGalleryImage(string $imageUrl): ?string
    {
        try {
            $request = Http::timeout(20)->withHeaders(['User-Agent' => self::USER_AGENT]);
            if (app()->environment('local')) {
                $request = $request->withoutVerifying();
            }
            $response = $request->get($imageUrl);
            if (! $response->successful()) {
                return null;
            }
            $contentType = strtolower((string) $response->header('Content-Type'));
            if ($contentType !== '' && ! str_contains($contentType, 'image') && ! str_contains($contentType, 'octet-stream')) {
                return null;
            }
            $extension = str_contains($contentType, 'png') ? 'png' : 'jpg';
            $filename = time() . Str::random(8) . '.' . $extension;
            file_put_contents(public_path('assets/images/galleries/' . $filename), $response->body());

            return $filename;
        } catch (Throwable $e) {
            return null;
        }
    }

    private function resolveBrandId(string $brandName, ?int $brandId): ?int
    {
        if ($brandId) {
            $exists = Brand::where('id', $brandId)->exists();
            if ($exists) {
                return $brandId;
            }
        }

        if ($brandName === '') {
            return null;
        }

        $slug = $this->slug($brandName);
        $brand = Brand::where(function ($query) use ($brandName, $slug) {
            $query->where(DB::raw('lower(name)'), strtolower($brandName))
                ->orWhere(DB::raw('lower(slug)'), strtolower($slug));
        })->first();

        if ($brand) {
            return $brand->id;
        }

        return Brand::create([
            'name' => $brandName,
            'slug' => $slug,
        ])->id;
    }

    private function resolveCategoryId(string $categoryName, ?int $categoryId): int
    {
        if ($categoryId) {
            $cat = Category::find($categoryId);
            if ($cat) {
                return $cat->id;
            }
        }

        if ($categoryName === '') {
            $categoryName = 'General';
        }

        $category = Category::where(DB::raw('lower(name)'), strtolower($categoryName))->first();
        if ($category) {
            return $category->id;
        }

        $payload = [
            'name' => $categoryName,
            'slug' => $this->slug($categoryName),
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('categories', 'status')) {
            $payload['status'] = 1;
        }

        return Category::create($payload)->id;
    }

    private function resolveSubcategoryId(int $categoryId, string $name): int
    {
        $sub = Subcategory::where('category_id', $categoryId)
            ->where(DB::raw('lower(name)'), strtolower($name))
            ->first();
        if ($sub) {
            return $sub->id;
        }

        $payload = [
            'category_id' => $categoryId,
            'name' => $name,
            'slug' => $this->slug($name),
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('subcategories', 'status')) {
            $payload['status'] = 1;
        }

        return Subcategory::create($payload)->id;
    }

    private function resolveChildcategoryId(int $subcategoryId, string $name): int
    {
        $child = Childcategory::where('subcategory_id', $subcategoryId)
            ->where(DB::raw('lower(name)'), strtolower($name))
            ->first();
        if ($child) {
            return $child->id;
        }

        $payload = [
            'subcategory_id' => $subcategoryId,
            'name' => $name,
            'slug' => $this->slug($name),
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('childcategories', 'status')) {
            $payload['status'] = 1;
        }

        return Childcategory::create($payload)->id;
    }

    private function generateSku(string $name, string $url): string
    {
        $base = strtoupper(preg_replace('/[^A-Z0-9]/i', '', Str::slug($name, '')));
        $base = substr($base !== '' ? $base : 'PROD', 0, 10);
        $suffix = strtoupper(substr(sha1($url . '|' . $name), 0, 6));

        return $base . '-' . $suffix;
    }

    private function slug(string $value): string
    {
        $slug = Str::slug($value, '-');

        return $slug !== '' ? $slug : 'item-' . Str::lower(Str::random(6));
    }

    private function baseUrl(string $url): string
    {
        $parts = parse_url($url);

        return ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '');
    }

    private function absolutizeUrl(string $href, string $base): ?string
    {
        $href = trim(html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($href === '' || str_starts_with($href, '#') || str_starts_with(strtolower($href), 'javascript:')) {
            return null;
        }
        if (str_starts_with($href, '//')) {
            return 'https:' . $href;
        }
        if (preg_match('#^https?://#i', $href)) {
            return $href;
        }
        if (str_starts_with($href, '/')) {
            return rtrim($base, '/') . $href;
        }

        return rtrim($base, '/') . '/' . ltrim($href, '/');
    }

    private function matchContent(string $html, string $pattern): string
    {
        if (preg_match($pattern, $html, $m)) {
            return trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        return '';
    }

    private function firstNonEmpty(array $values): string
    {
        foreach ($values as $value) {
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return '';
    }
}
