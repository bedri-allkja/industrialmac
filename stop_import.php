<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$running = App\Models\ProductImport::whereIn('status', ['pending', 'running'])->get();

foreach ($running as $import) {
    $import->update([
        'status' => 'cancelled',
        'message' => 'Stopped by user.',
        'finished_at' => now(),
    ]);
    echo 'Cancelled import id=' . $import->id
        . ' (processed=' . $import->processed_rows
        . ', imported=' . $import->imported_count . ')' . PHP_EOL;
}

if ($running->isEmpty()) {
    echo "No running imports in database.\n";
}

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $script = __DIR__ . '/kill_import_workers.ps1';
    if (is_file($script)) {
        passthru('powershell -NoProfile -ExecutionPolicy Bypass -File ' . escapeshellarg($script));
    }
} else {
    exec("pkill -f 'products:import' 2>/dev/null");
}

echo "Import workers stopped. php artisan serve was not touched.\n";
