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
        // Courses
        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('courses', 'aeo_summary')) {
                $table->text('aeo_summary')->nullable()->after('meta_keywords');
            }
            if (! Schema::hasColumn('courses', 'schema_markup')) {
                $table->text('schema_markup')->nullable()->after('aeo_summary');
            }
        });

        // Notices
        Schema::table('notices', function (Blueprint $table) {
            if (! Schema::hasColumn('notices', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('subtitle');
            }
            if (! Schema::hasColumn('notices', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (! Schema::hasColumn('notices', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('notices', 'aeo_summary')) {
                $table->text('aeo_summary')->nullable()->after('meta_keywords');
            }
            if (! Schema::hasColumn('notices', 'schema_markup')) {
                $table->text('schema_markup')->nullable()->after('aeo_summary');
            }
        });

        // Teachers
        Schema::table('teachers', function (Blueprint $table) {
            if (! Schema::hasColumn('teachers', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('bio');
            }
            if (! Schema::hasColumn('teachers', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (! Schema::hasColumn('teachers', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('teachers', 'aeo_summary')) {
                $table->text('aeo_summary')->nullable()->after('meta_keywords');
            }
            if (! Schema::hasColumn('teachers', 'schema_markup')) {
                $table->text('schema_markup')->nullable()->after('aeo_summary');
            }
        });

        // Course Categories
        Schema::table('course_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('course_categories', 'aeo_summary')) {
                $table->text('aeo_summary')->nullable()->after('meta_keywords');
            }
            if (! Schema::hasColumn('course_categories', 'schema_markup')) {
                $table->text('schema_markup')->nullable()->after('aeo_summary');
            }
        });

        // Blog Posts
        Schema::table('blog_posts', function (Blueprint $table) {
            if (! Schema::hasColumn('blog_posts', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('blog_posts', 'aeo_summary')) {
                $table->text('aeo_summary')->nullable()->after('meta_keywords');
            }
        });

        // FAQs
        Schema::table('f_a_q_s', function (Blueprint $table) {
            if (! Schema::hasColumn('f_a_q_s', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('f_a_q_s', 'aeo_summary')) {
                $table->text('aeo_summary')->nullable()->after('meta_keywords');
            }
            if (! Schema::hasColumn('f_a_q_s', 'schema_markup')) {
                $table->text('schema_markup')->nullable()->after('aeo_summary');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The conditional up() did not record which columns it introduced.
        // A rollback cannot safely distinguish those columns from pre-existing production data.
    }
};
