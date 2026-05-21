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
            if (! Schema::hasColumn('join_now_queries', 'help_topic')) {
                $table->string('help_topic')->nullable()->after('contactMethod')->index();
            }

            if (! Schema::hasColumn('join_now_queries', 'current_education_level')) {
                $table->string('current_education_level')->nullable()->after('help_topic');
            }

            if (! Schema::hasColumn('join_now_queries', 'preferred_batch_time')) {
                $table->string('preferred_batch_time')->nullable()->after('current_education_level');
            }

            if (! Schema::hasColumn('join_now_queries', 'goal')) {
                $table->text('goal')->nullable()->after('preferred_batch_time');
            }

            if (! Schema::hasColumn('join_now_queries', 'selected_course')) {
                $table->string('selected_course')->nullable()->after('course_slug')->index();
            }

            if (! Schema::hasColumn('join_now_queries', 'source_page')) {
                $table->string('source_page')->nullable()->after('landing_page');
            }

            if (! Schema::hasColumn('join_now_queries', 'source_section')) {
                $table->string('source_section')->nullable()->after('source_page')->index();
            }

            if (! Schema::hasColumn('join_now_queries', 'audience_type')) {
                $table->string('audience_type')->nullable()->after('source_section')->index();
            }

            if (! Schema::hasColumn('join_now_queries', 'inquiry_intent')) {
                $table->string('inquiry_intent')->nullable()->after('audience_type')->index();
            }

            if (! Schema::hasColumn('join_now_queries', 'lead_score')) {
                $table->unsignedSmallInteger('lead_score')->default(0)->after('inquiry_intent')->index();
            }

            if (! Schema::hasColumn('join_now_queries', 'lead_status')) {
                $table->string('lead_status')->default('Basic')->after('lead_score')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('join_now_queries', function (Blueprint $table) {
            $columns = [
                'help_topic',
                'current_education_level',
                'preferred_batch_time',
                'goal',
                'selected_course',
                'source_page',
                'source_section',
                'audience_type',
                'inquiry_intent',
                'lead_score',
                'lead_status',
            ];

            $existingColumns = array_filter($columns, fn (string $column): bool => Schema::hasColumn('join_now_queries', $column));

            if ($existingColumns !== []) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
