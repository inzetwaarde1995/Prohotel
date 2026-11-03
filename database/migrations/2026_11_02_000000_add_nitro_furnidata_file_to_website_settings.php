<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('website_settings')->insertOrIgnore([
            'key' => 'nitro_furnidata_file',
            'value' => '/var/www/gamedata/config/FurnitureData.json',
            'comment' => 'Path (or URL) to the Nitro FurnitureData.json, used to show furniture names',
        ]);
    }

    public function down(): void
    {
        DB::table('website_settings')->where('key', 'nitro_furnidata_file')->delete();
    }
};
