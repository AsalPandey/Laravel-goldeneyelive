<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->text('landing_page')->nullable()->change();
        });

        Schema::table('join_now_queries', function (Blueprint $table) {
            $table->text('landing_page')->nullable()->change();
            $table->text('source_page')->nullable()->change();
        });

        Schema::table('f_a_q_s', function (Blueprint $table) {
            $table->text('question')->change();
        });

        Schema::table('notices', function (Blueprint $table) {
            $table->text('link')->nullable()->change();
        });

        Schema::table('service_pillars', function (Blueprint $table) {
            $table->text('cta_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally irreversible: shrinking these columns could truncate production data.
    }
};
