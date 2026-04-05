<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->string('photo')->nullable();
                $table->string('image')->nullable();
                $table->unsignedTinyInteger('is_featured')->default(0)->nullable();
            });
        }

        if (! Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('title')->nullable();
                $table->text('details')->nullable();
                $table->string('photo')->nullable();
            });
        }

        if (! Schema::hasTable('partners')) {
            Schema::create('partners', function (Blueprint $table) {
                $table->id();
                $table->string('link')->nullable();
                $table->string('photo')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
        Schema::dropIfExists('services');
        Schema::dropIfExists('brands');
    }
};
