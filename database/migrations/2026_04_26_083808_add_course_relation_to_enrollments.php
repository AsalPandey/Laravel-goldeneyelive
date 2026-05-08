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
        Schema::table('join_now_queries', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('address')->constrained('courses')->onDelete('set null');
            $table->string('course_slug')->nullable()->after('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('join_now_queries', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn(['course_id', 'course_slug']);
        });
    }
};
