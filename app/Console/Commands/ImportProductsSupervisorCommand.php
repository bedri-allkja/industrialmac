<?php

namespace App\Console\Commands;

use App\Models\ProductImport;
use Illuminate\Console\Command;

class ImportProductsSupervisorCommand extends Command
{
    protected $signature = 'products:import-supervisor {import : The product import ID} {--delay=10 : Seconds to wait before auto-resume}';

    protected $description = 'Supervise a product import and auto-resume after crashes or failures';

    public function handle(): int
    {
        $importId = (int) $this->argument('import');
        $delay = max(3, (int) $this->option('delay'));
        $php = $this->resolvePhpBinary();
        $artisan = base_path('artisan');

        while (true) {
            $import = ProductImport::find($importId);

            if (! $import) {
                $this->error('Import job not found.');

                return self::FAILURE;
            }

            if ($import->status === 'completed') {
                $this->info('Import completed.');

                return self::SUCCESS;
            }

            if ($import->status === 'cancelled') {
                $this->info('Import cancelled.');

                return self::SUCCESS;
            }

            if ($import->status === 'failed') {
                $import->update([
                    'status' => 'pending',
                    'message' => __('Preparing auto-resume from row :row...', ['row' => $import->last_row + 1]),
                    'finished_at' => null,
                ]);
            }

            $command = escapeshellarg($php) . ' ' . escapeshellarg($artisan) . ' products:import ' . $importId;
            $this->line('Starting import worker...');

            exec($command, $output, $exitCode);
            $import->refresh();

            if ($import->status === 'completed') {
                $this->info(sprintf(
                    'Import finished: %d imported, %d skipped, %d errors.',
                    $import->imported_count,
                    $import->skipped_count,
                    $import->error_count
                ));

                return self::SUCCESS;
            }

            $import->increment('retry_count');
            $import->update([
                'status' => 'pending',
                'message' => __('Worker stopped. Auto-resuming in :seconds seconds (attempt :count)...', [
                    'seconds' => $delay,
                    'count' => $import->retry_count,
                ]),
                'finished_at' => null,
            ]);

            $this->warn($import->message);
            sleep($delay);
        }
    }

    private function resolvePhpBinary(): string
    {
        $configured = env('IMPORT_PHP_BINARY');

        if ($configured && is_file($configured)) {
            return $configured;
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $laragonRoot = getenv('LARAGON_ROOT') ?: 'C:\\laragon';
            $candidates = glob($laragonRoot . '\\bin\\php\\php-*\\php.exe') ?: [];
            rsort($candidates);

            foreach ($candidates as $candidate) {
                if (is_file($candidate)) {
                    return $candidate;
                }
            }
        }

        return PHP_BINARY;
    }
}
