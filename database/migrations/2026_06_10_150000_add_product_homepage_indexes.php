<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! $this->indexExists('products', 'products_status_category_id_index')) {
                $table->index(['status', 'category_id'], 'products_status_category_id_index');
            }

            if (! $this->indexExists('products', 'products_status_brand_id_index')) {
                $table->index(['status', 'brand_id'], 'products_status_brand_id_index');
            }

            if (! $this->indexExists('products', 'products_status_latest_id_index')) {
                $table->index(['status', 'latest', 'id'], 'products_status_latest_id_index');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if ($this->indexExists('products', 'products_status_category_id_index')) {
                $table->dropIndex('products_status_category_id_index');
            }

            if ($this->indexExists('products', 'products_status_brand_id_index')) {
                $table->dropIndex('products_status_brand_id_index');
            }

            if ($this->indexExists('products', 'products_status_latest_id_index')) {
                $table->dropIndex('products_status_latest_id_index');
            }
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = $connection->select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$database, $table, $index]
        );

        return ! empty($result);
    }
};
