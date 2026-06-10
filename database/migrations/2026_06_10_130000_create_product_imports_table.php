<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_imports')) {
            Schema::create('product_imports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->string('original_name')->nullable();
                $table->string('file_path');
                $table->string('status', 20)->default('pending');
                $table->unsignedBigInteger('total_rows')->default(0);
                $table->unsignedBigInteger('processed_rows')->default(0);
                $table->unsignedBigInteger('imported_count')->default(0);
                $table->unsignedBigInteger('skipped_count')->default(0);
                $table->unsignedBigInteger('error_count')->default(0);
                $table->boolean('skip_images')->default(true);
                $table->boolean('background')->default(false);
                $table->string('message')->nullable();
                $table->longText('log')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_imports');
    }
};
