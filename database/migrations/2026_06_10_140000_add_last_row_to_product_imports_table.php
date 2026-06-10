<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_imports', function (Blueprint $table) {
            if (! Schema::hasColumn('product_imports', 'last_row')) {
                $table->unsignedBigInteger('last_row')->default(0)->after('processed_rows');
            }
            if (! Schema::hasColumn('product_imports', 'retry_count')) {
                $table->unsignedInteger('retry_count')->default(0)->after('error_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_imports', function (Blueprint $table) {
            if (Schema::hasColumn('product_imports', 'last_row')) {
                $table->dropColumn('last_row');
            }
            if (Schema::hasColumn('product_imports', 'retry_count')) {
                $table->dropColumn('retry_count');
            }
        });
    }
};
