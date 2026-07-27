<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'quote_request_id')) {
                $table->unsignedBigInteger('quote_request_id')->nullable()->after('order_id');
                $table->index('quote_request_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'quote_request_id')) {
                $table->dropIndex(['quote_request_id']);
                $table->dropColumn('quote_request_id');
            }
        });
    }
};
