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
            $table->index('status');
            $table->index('is_featured');
            $table->index('category_id');
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->index('status');
            $table->index('slug');
            $table->index('published_at');
        });

        Schema::table('course_categories', function (Blueprint $table) {
            $table->index('status');
            $table->index('slug');
            $table->index('order_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['published_at']);
        });

        Schema::table('course_categories', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['order_priority']);
        });
    }
};
