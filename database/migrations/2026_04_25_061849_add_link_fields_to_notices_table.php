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
        Schema::table('notices', function (Blueprint $table) {
            if (! Schema::hasColumn('notices', 'link')) {
                $table->string('link')->nullable()->after('image');
            }
            if (! Schema::hasColumn('notices', 'button_text')) {
                $table->string('button_text')->nullable()->after('link');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn(['link', 'button_text']);
        });
    }
};
