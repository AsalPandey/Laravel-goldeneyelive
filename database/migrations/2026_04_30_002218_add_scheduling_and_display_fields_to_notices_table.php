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
            if (! Schema::hasColumn('notices', 'display_type')) {
                $table->string('display_type')->default('popup')->after('badge');
            }
            if (! Schema::hasColumn('notices', 'is_urgent')) {
                $table->boolean('is_urgent')->default(false)->after('display_type');
            }
            if (! Schema::hasColumn('notices', 'starts_at')) {
                $table->timestamp('starts_at')->nullable()->after('is_urgent');
            }
            if (! Schema::hasColumn('notices', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('starts_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn(['display_type', 'is_urgent', 'starts_at', 'expires_at']);
        });
    }
};
