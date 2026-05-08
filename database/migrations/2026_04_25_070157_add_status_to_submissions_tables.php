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
            $table->string('status')->default('new')->after('message');
        });
        Schema::table('join_now_queries', function (Blueprint $table) {
            $table->string('status')->default('new')->after('queries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('join_now_queries', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
