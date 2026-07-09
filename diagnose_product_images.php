<?php

/**
 * Diagnose why product images may not show on the server.
 *
 * Usage: php diagnose_product_images.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "=== Product image diagnostic ===\n\n";

echo 'APP_URL: ' . config('app.url') . "\n";
echo 'Public path: ' . public_path() . "\n\n";

$thumbDir = public_path('assets/images/thumbnails');
$productDir = public_path('assets/images/products');

echo 'Thumbnails folder exists: ' . (is_dir($thumbDir) ? 'yes' : 'NO') . "\n";
echo 'Products folder exists: ' . (is_dir($productDir) ? 'yes' : 'NO') . "\n";

if (is_dir($thumbDir)) {
    $thumbCount = count(glob($thumbDir . '/*') ?: []);
    echo "Thumbnail files on disk: {$thumbCount}\n";
}

if (is_dir($productDir)) {
    $productCount = count(glob($productDir . '/*') ?: []);
    echo "Product image files on disk: {$productCount}\n";
}

$dbTotal = DB::table('products')->count();
$dbWithThumb = DB::table('products')->whereNotNull('thumbnail')->where('thumbnail', '!=', '')->where('thumbnail', '!=', 'noimage.png')->count();
$dbNoimage = DB::table('products')->where('thumbnail', 'noimage.png')->count();

echo "\nDB products: {$dbTotal}\n";
echo "DB with real thumbnail name: {$dbWithThumb}\n";
echo "DB with noimage.png: {$dbNoimage}\n\n";

$samples = Product::whereNotNull('thumbnail')
    ->where('thumbnail', '!=', 'noimage.png')
    ->orderByDesc('id')
    ->limit(5)
    ->get(['id', 'sku', 'thumbnail', 'photo']);

echo "=== Sample products ===\n";

foreach ($samples as $product) {
    $thumbPath = public_path('assets/images/thumbnails/' . $product->thumbnail);
    $photoPath = public_path('assets/images/products/' . $product->photo);
    $url = asset('assets/images/thumbnails/' . $product->thumbnail);

    echo "\nSKU: {$product->sku}\n";
    echo "  thumbnail DB: {$product->thumbnail}\n";
    echo "  URL: {$url}\n";
    echo '  thumbnail file exists: ' . (is_file($thumbPath) ? 'YES' : 'MISSING') . "\n";
    echo '  full photo file exists: ' . (is_file($photoPath) ? 'YES' : 'MISSING') . "\n";
}

echo "\n=== Expected on GoDaddy (document root = public_html) ===\n";
echo "public_html/assets/images/thumbnails/{filename}.jpg\n";
echo "public_html/assets/images/products/{filename}.png|jpg\n\n";

echo "Common mistakes:\n";
echo "  - Files in public_html/images/... instead of public_html/assets/images/...\n";
echo "  - Files in public_html/public/assets/... (extra public folder)\n";
echo "  - Only products/ uploaded, not thumbnails/ (cards use thumbnails)\n";
echo "  - APP_URL in .env still set to http://127.0.0.1:8000\n";
echo "  - Linux is case-sensitive: Thumbnails != thumbnails\n\n";

$wrongPaths = [
    public_path('images/thumbnails'),
    base_path('public_html/assets/images/thumbnails'),
    base_path('assets/images/thumbnails'),
];

echo "=== Checking alternate paths ===\n";
foreach ($wrongPaths as $path) {
    if (is_dir($path)) {
        $count = count(glob($path . '/*') ?: []);
        echo "FOUND {$count} files at: {$path}\n";
    }
}

echo "\nDone.\n";
