<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $langs = [
            [
                'is_default' => 0,
                'language' => 'Spanish',
                'file' => 'industrialmac_es.json',
                'name' => 'industrialmac_es',
                'rtl' => 0,
            ],
            [
                'is_default' => 0,
                'language' => 'Russian',
                'file' => 'industrialmac_ru.json',
                'name' => 'industrialmac_ru',
                'rtl' => 0,
            ],
        ];

        foreach ($langs as $lang) {
            if (!DB::table('languages')->where('file', $lang['file'])->exists()) {
                DB::table('languages')->insert($lang);
            }
        }
    }

    public function down(): void
    {
        DB::table('languages')->whereIn('file', [
            'industrialmac_es.json',
            'industrialmac_ru.json',
        ])->delete();
    }
};
