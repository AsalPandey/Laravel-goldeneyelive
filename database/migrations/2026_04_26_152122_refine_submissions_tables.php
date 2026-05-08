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
            $table->text('admin_notes')->nullable()->after('message');
            $table->timestamp('replied_at')->nullable()->after('admin_notes');
        });

        Schema::table('join_now_queries', function (Blueprint $table) {
            $table->text('admin_notes')->nullable()->after('queries');
            $table->timestamp('followed_up_at')->nullable()->after('admin_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['admin_notes', 'replied_at']);
        });

        Schema::table('join_now_queries', function (Blueprint $table) {
            $table->dropColumn(['admin_notes', 'followed_up_at']);
        });
    }
};
