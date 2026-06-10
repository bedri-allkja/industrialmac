<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$import = App\Models\ProductImport::latest('id')->first();
$total = App\Models\Product::count();
$withImage = App\Models\Product::where('photo', '!=', 'noimage.png')->count();

echo json_encode([
    'import' => $import ? $import->toStatusArray() + ['skip_images' => $import->skip_images] : null,
    'products_total' => $total,
    'products_with_images' => $withImage,
    'products_noimage' => $total - $withImage,
], JSON_PRETTY_PRINT) . PHP_EOL;
