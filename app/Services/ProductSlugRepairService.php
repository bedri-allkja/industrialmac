<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSlugRepairService
{
    /**
     * Build a URL-safe product slug (never contains / # ? or spaces).
     */
    public static function make(string $name, string $sku = ''): string
    {
        $namePart = Str::slug($name, '-');
        if ($namePart === '') {
            $namePart = 'product';
        }

        $skuPart = Str::slug($sku, '-');
        if ($skuPart === '') {
            $skuPart = Str::lower(Str::random(6));
        }

        return $namePart . '-' . $skuPart;
    }

    /**
     * Fix every product whose slug contains /, ?, # or other unsafe characters.
     *
     * @return array{fixed: int, samples: array<int, array{id:int, from:string, to:string}>}
     */
    public function repair(int $limit = 0): array
    {
        $samples = [];

        // Fast path: replace "/" in slug (main breakage from SKUs like PVC/PUR)
        $before = (int) Product::where('slug', 'like', '%/%')->count();
        if ($before > 0) {
            DB::update("UPDATE products SET slug = REPLACE(slug, '/', '-') WHERE slug LIKE '%/%'");
        }
        $afterSlash = (int) Product::where('slug', 'like', '%/%')->count();
        $fixed = max(0, $before - $afterSlash);

        // Collapse accidental double dashes introduced by REPLACE
        DB::update("UPDATE products SET slug = REPLACE(slug, '--', '-') WHERE slug LIKE '%--%'");

        // Rebuild remaining unsafe slugs carefully (spaces / ? / #)
        $query = Product::query()
            ->where(function ($q) {
                $q->where('slug', 'like', '%/%')
                    ->orWhere('slug', 'like', '%?%')
                    ->orWhere('slug', 'like', '%#%')
                    ->orWhere('slug', 'like', '% %');
            })
            ->orderBy('id');

        if ($limit > 0) {
            $query->limit($limit);
        }

        foreach ($query->get(['id', 'name', 'sku', 'slug']) as $product) {
            $from = (string) $product->slug;
            $newSlug = self::make((string) $product->name, (string) $product->sku);
            $base = $newSlug;
            $i = 1;
            while (
                Product::where('slug', $newSlug)
                    ->where('id', '!=', $product->id)
                    ->exists()
            ) {
                $newSlug = $base . '-' . $i;
                $i++;
            }

            if ($from === $newSlug) {
                continue;
            }

            DB::table('products')->where('id', $product->id)->update(['slug' => $newSlug]);
            $fixed++;

            if (count($samples) < 15) {
                $samples[] = [
                    'id' => (int) $product->id,
                    'from' => $from,
                    'to' => $newSlug,
                ];
            }
        }

        if ($fixed > 0 && $samples === []) {
            $samples[] = [
                'id' => 0,
                'from' => 'slugs containing /',
                'to' => 'slashes replaced with - (' . $fixed . ' rows)',
            ];
        }

        return compact('fixed', 'samples');
    }
}
