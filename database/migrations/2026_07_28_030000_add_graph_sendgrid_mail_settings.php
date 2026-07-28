<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generalsettings', function (Blueprint $table) {
            if (!Schema::hasColumn('generalsettings', 'ms_tenant_id')) {
                $table->string('ms_tenant_id', 100)->nullable()->after('quote_notify_email');
            }
            if (!Schema::hasColumn('generalsettings', 'ms_client_id')) {
                $table->string('ms_client_id', 100)->nullable()->after('ms_tenant_id');
            }
            if (!Schema::hasColumn('generalsettings', 'ms_client_secret')) {
                $table->string('ms_client_secret', 255)->nullable()->after('ms_client_id');
            }
            if (!Schema::hasColumn('generalsettings', 'sendgrid_api_key')) {
                $table->string('sendgrid_api_key', 255)->nullable()->after('ms_client_secret');
            }
        });
    }

    public function down(): void
    {
        Schema::table('generalsettings', function (Blueprint $table) {
            foreach (['ms_tenant_id', 'ms_client_id', 'ms_client_secret', 'sendgrid_api_key'] as $col) {
                if (Schema::hasColumn('generalsettings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
