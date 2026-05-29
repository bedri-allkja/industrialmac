<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pages')
            ->where('slug', 'about')
            ->update([
                'header' => 1,
                'title' => 'About Us',
            ]);

        DB::table('languages')->where('language', 'Hindi')->delete();

        if (!DB::table('languages')->where('file', 'industrialmac_it.json')->exists()) {
            DB::table('languages')->insert([
                'is_default' => 0,
                'language' => 'Italian',
                'file' => 'industrialmac_it.json',
                'name' => 'industrialmac_it',
                'rtl' => 0,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('pages')
            ->where('slug', 'about')
            ->update(['header' => 0]);

        DB::table('languages')->where('file', 'industrialmac_it.json')->delete();
    }
};
