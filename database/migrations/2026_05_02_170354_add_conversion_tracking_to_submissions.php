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
            $table->string('lead_source')->nullable()->after('message')->index();
            $table->string('landing_page')->nullable()->after('lead_source');
            $table->string('cta_id')->nullable()->after('landing_page')->index();
        });

        Schema::table('join_now_queries', function (Blueprint $table) {
            $table->string('lead_source')->nullable()->after('contactMethod')->index();
            $table->string('landing_page')->nullable()->after('lead_source');
            $table->string('cta_id')->nullable()->after('landing_page')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['lead_source']);
            $table->dropIndex(['cta_id']);
            $table->dropColumn(['lead_source', 'landing_page', 'cta_id']);
        });

        Schema::table('join_now_queries', function (Blueprint $table) {
            $table->dropIndex(['lead_source']);
            $table->dropIndex(['cta_id']);
            $table->dropColumn(['lead_source', 'landing_page', 'cta_id']);
        });
    }
};
