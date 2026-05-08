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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('category')->nullable()->change();
            $table->string('category_slug')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Reverting to the approximate original state is complex with change()
            // and usually unnecessary for this bug fix, but we provide a string fallback.
            $table->string('category')->nullable(false)->change();
            $table->string('category_slug')->nullable(false)->change();
        });
    }
};
