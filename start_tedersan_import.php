<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$path = storage_path('app/product-imports/tedersan_data_all_updated.csv');

if (! is_file($path)) {
    echo "File not found: {$path}\n";
    exit(1);
}

$running = App\Models\ProductImport::whereIn('status', ['pending', 'running'])->get();

foreach ($running as $active) {
    $active->update([
        'status' => 'failed',
        'message' => 'Cancelled — restarted with streaming fix.',
        'finished_at' => now(),
    ]);
    echo 'Cancelled stuck import id=' . $active->id . "\n";
}

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

echo 'Started import id=' . $import->id . ' (images enabled)' . PHP_EOL;
echo 'Track at: /admin/products/import' . PHP_EOL;
