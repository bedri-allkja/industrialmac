<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$import = App\Models\ProductImport::find(6);

if (! $import) {
    echo "Import #6 not found.\n";
    exit(1);
}

$latest = App\Models\Product::latest('id')->first();
$lastRow = (int) $import->last_row;

if ($latest) {
    $path = $import->file_path;
    $handle = fopen($path, 'rb');
    $row = 0;

    while (($line = fgetcsv($handle)) !== false) {
        $row++;
        if ($row === 1) {
            continue;
        }

        if (trim($line[0] ?? '') === $latest->sku) {
            $lastRow = max($lastRow, $row);
            break;
        }
    }

    fclose($handle);
}

if ($lastRow === 0) {
    $lastRow = App\Models\Product::count() + 1;
}

$import->update([
    'status' => 'pending',
    'last_row' => $lastRow,
    'processed_rows' => max((int) $import->processed_rows, $lastRow - 1),
    'message' => 'Resuming with auto-restart supervisor...',
    'finished_at' => null,
]);

app(App\Services\ProductImportService::class)->startBackgroundProcess($import);

echo "Resumed import id={$import->id} from row " . ($lastRow + 1) . PHP_EOL;
echo "Latest SKU in DB: " . ($latest->sku ?? 'none') . PHP_EOL;
echo "Products in DB: " . App\Models\Product::count() . PHP_EOL;
echo "Supervisor started — will auto-resume if it crashes.\n";
