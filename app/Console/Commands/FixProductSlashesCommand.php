<?php

namespace App\Console\Commands;

use App\Services\ProductSlugRepairService;
use Illuminate\Console\Command;

class FixProductSlashesCommand extends Command
{
    protected $signature = 'products:fix-slash-slugs';

    protected $description = 'Repair product slugs that contain / or other unsafe URL characters';

    public function handle(ProductSlugRepairService $repair): int
    {
        $result = $repair->repair();
        $this->info('Fixed ' . $result['fixed'] . ' product slug(s).');
        foreach ($result['samples'] as $sample) {
            $this->line($sample['id'] . ': ' . $sample['from'] . ' => ' . $sample['to']);
        }

        return self::SUCCESS;
    }
}
