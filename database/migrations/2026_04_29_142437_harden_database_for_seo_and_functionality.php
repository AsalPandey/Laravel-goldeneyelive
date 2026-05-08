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
        // 1. Testimonials: Add missing SEO and Metadata fields
        Schema::table('testimonials', function (Blueprint $table) {
            if (! Schema::hasColumn('testimonials', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('is_featured');
            }
            if (! Schema::hasColumn('testimonials', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (! Schema::hasColumn('testimonials', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('testimonials', 'aeo_summary')) {
                $table->text('aeo_summary')->nullable()->after('meta_keywords');
            }
            if (! Schema::hasColumn('testimonials', 'schema_markup')) {
                $table->text('schema_markup')->nullable()->after('aeo_summary');
            }
        });

        // 2. Notices: Ensure all functional fields are present (safety check)
        Schema::table('notices', function (Blueprint $table) {
            if (! Schema::hasColumn('notices', 'status')) {
                $table->string('status')->default('active')->after('image');
            }
            if (! Schema::hasColumn('notices', 'link')) {
                $table->string('link')->nullable()->after('status');
            }
            if (! Schema::hasColumn('notices', 'button_text')) {
                $table->string('button_text')->nullable()->after('link');
            }
        });

        // 3. Blog Posts: Ensure full SEO suite
        Schema::table('blog_posts', function (Blueprint $table) {
            if (! Schema::hasColumn('blog_posts', 'schema_markup')) {
                $table->text('schema_markup')->nullable()->after('aeo_summary');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup']);
        });

        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn(['status', 'link', 'button_text']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['schema_markup']);
        });
    }
};
