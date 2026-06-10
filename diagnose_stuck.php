<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$count = App\Models\Product::count();
$latest = App\Models\Product::latest('id')->first();
$import = App\Models\ProductImport::find(6);

echo "Products in DB: {$count}\n";
echo "Latest SKU: {$latest->sku}\n";
echo "Import DB says: processed={$import->processed_rows} imported={$import->imported_count}\n";

$alive = shell_exec('powershell -NoProfile -Command "(Get-CimInstance Win32_Process -Filter \"Name=\'php.exe\'\" | Where-Object { $_.CommandLine -match \'products:import\' }).Count"');
echo 'Worker running: ' . (trim($alive) > 0 ? 'YES' : 'NO (crashed)') . "\n";
