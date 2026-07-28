<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quote_request_id')->index();
            $table->string('direction', 20)->default('outbound');
            $table->string('to_email', 255);
            $table->string('from_email', 255)->nullable();
            $table->string('subject', 500);
            $table->text('body');
            $table->string('status', 20)->default('sent');
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_messages');
    }
};
