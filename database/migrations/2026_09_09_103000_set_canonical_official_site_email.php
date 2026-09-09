<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $officialEmail = config('goldeneye.official_email', 'contact@goldeneye.edu.np');

        DB::table('site_settings')
            ->where('key', 'site_email')
            ->update([
                'value' => $officialEmail,
                'updated_at' => now(),
            ]);

        cache()->forget('setting_site_email');
        cache()->forget('site_shared_data');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        DB::table('site_settings')
            ->where('key', 'site_email')
            ->update([
                'value' => 'goldeneyeacademy2008@gmail.com',
                'updated_at' => now(),
            ]);

        cache()->forget('setting_site_email');
        cache()->forget('site_shared_data');
    }
};
