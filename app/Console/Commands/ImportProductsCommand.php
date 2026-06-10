<?php

namespace App\Console\Commands;

use App\Models\ProductImport;
use App\Services\ProductImportService;
use Illuminate\Console\Command;

class ImportProductsCommand extends Command
{
    protected $signature = 'products:import {import : The product import ID}';

    protected $description = 'Run a tracked product CSV import in the background';

    public function handle(ProductImportService $importService): int
    {
        $import = ProductImport::find($this->argument('import'));

        if (! $import) {
            $this->error('Import job not found.');

            return self::FAILURE;
        }

        if ($import->status === 'completed') {
            $this->info('Import already completed.');

            return self::SUCCESS;
        }

        $importService->run($import);
        $import->refresh();

        if ($import->status === 'failed') {
            $this->error($import->message ?? 'Import failed.');

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Imported %d products (%d skipped, %d errors) in %ds.',
            $import->imported_count,
            $import->skipped_count,
            $import->error_count,
            $import->elapsedSeconds() ?? 0
        ));

        return self::SUCCESS;
    }
}
