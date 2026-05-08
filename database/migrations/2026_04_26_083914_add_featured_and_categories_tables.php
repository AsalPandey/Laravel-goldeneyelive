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
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('status');
            $table->foreignId('category_id')->nullable()->after('category_slug')->constrained('course_categories')->onDelete('set null');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('status');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['is_featured', 'category_id']);
        });

        Schema::dropIfExists('course_categories');
    }
};
