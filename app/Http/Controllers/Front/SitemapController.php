<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    private const PRODUCTS_PER_SITEMAP = 4000;

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /user',
            'Disallow: /vendor',
            'Disallow: /checkout',
            'Disallow: /cart',
            'Disallow: /compare',
            'Disallow: /wishlist',
            'Disallow: /request-quote',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function index(): Response
    {
        $productCount = Cache::remember('sitemap.product_count', 3600, function () {
            return Product::where('status', 1)->count();
        });

        $pages = max(1, (int) ceil($productCount / self::PRODUCTS_PER_SITEMAP));
        $now = now()->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $xml .= $this->sitemapEntry(url('/sitemap-static.xml'), $now);
        $xml .= $this->sitemapEntry(url('/sitemap-categories.xml'), $now);

        for ($i = 1; $i <= $pages; $i++) {
            $xml .= $this->sitemapEntry(url('/sitemap-products-' . $i . '.xml'), $now);
        }

        $xml .= '</sitemapindex>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function staticPages(): Response
    {
        $urls = [
            ['loc' => url('/'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('front.categories'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('front.category'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('front.about'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('front.quality'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('front.faq'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('front.qa'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('front.contact'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('front.privacy'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('front.cookie'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('front.legal'), 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        return $this->urlset($urls);
    }

    public function categories(): Response
    {
        $categories = Category::where('status', 1)
            ->orderBy('id')
            ->get(['slug']);

        $urls = [];
        foreach ($categories as $category) {
            if (empty($category->slug)) {
                continue;
            }
            $urls[] = [
                'loc' => route('front.category', $category->slug),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        return $this->urlset($urls);
    }

    public function products(int $page = 1): Response
    {
        $page = max(1, $page);

        $products = Product::where('status', 1)
            ->orderBy('id')
            ->forPage($page, self::PRODUCTS_PER_SITEMAP)
            ->get(['slug']);

        $urls = [];
        foreach ($products as $product) {
            if (empty($product->slug)) {
                continue;
            }
            $urls[] = [
                'loc' => route('front.product', $product->slug),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        return $this->urlset($urls);
    }

    private function urlset(array $urls): Response
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . "</loc>\n";
            if (!empty($url['lastmod'])) {
                $xml .= '    <lastmod>' . $url['lastmod'] . "</lastmod>\n";
            }
            if (!empty($url['changefreq'])) {
                $xml .= '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            }
            if (!empty($url['priority'])) {
                $xml .= '    <priority>' . $url['priority'] . "</priority>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function sitemapEntry(string $loc, string $lastmod): string
    {
        return "  <sitemap>\n"
            . '    <loc>' . htmlspecialchars($loc, ENT_XML1) . "</loc>\n"
            . '    <lastmod>' . $lastmod . "</lastmod>\n"
            . "  </sitemap>\n";
    }
}
