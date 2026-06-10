<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\ProductImport::whereIn('status', ['pending', 'running'])->get() as $active) {
    $active->update([
        'status' => 'failed',
        'message' => 'Cancelled — restarting with image SSL fix.',
        'finished_at' => now(),
    ]);
    echo 'Cancelled import id=' . $active->id . PHP_EOL;
}

$deleted = App\Models\Product::query()->delete();
echo "Deleted {$deleted} products\n";

$path = storage_path('app/product-imports/tedersan_data_all_updated.csv');

$import = App\Models\ProductImport::create([
    'admin_id' => 1,
    'original_name' => 'tedersan_data_all_updated.csv',
    'file_path' => $path,
    'status' => 'pending',
    'skip_images' => false,
    'background' => true,
    'message' => 'Import queued with images.',
]);

app(App\Services\ProductImportService::class)->startBackgroundProcess($import);

echo 'Started import id=' . $import->id . PHP_EOL;
