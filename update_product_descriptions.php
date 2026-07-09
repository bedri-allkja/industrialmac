<?php

/**
 * Set the details (description) field for all products.
 *
 * Usage:
 *   php update_product_descriptions.php           # dry run (count only)
 *   php update_product_descriptions.php --run   # apply update
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$description = 'INDUSTRIALMAC ALL RIGHTS RESERVED';
$run = in_array('--run', $argv ?? [], true);

$total = DB::table('products')->count();
$already = DB::table('products')->where('details', $description)->count();

echo "Products in database: {$total}\n";
echo "Already set to target text: {$already}\n";
echo "Target description: {$description}\n\n";

if (! $run) {
    echo "Dry run only. Re-run with --run to update all products.\n";
    exit(0);
}

$start = microtime(true);

$updated = DB::table('products')->update(['details' => $description]);

$seconds = round(microtime(true) - $start, 2);

echo "Updated {$updated} products in {$seconds}s.\n";
