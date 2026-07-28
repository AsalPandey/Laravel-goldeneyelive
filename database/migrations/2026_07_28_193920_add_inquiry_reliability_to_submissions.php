<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $hasCanonicalNewsletterDuplicates = DB::table('news_letters')
            ->selectRaw('LOWER(TRIM(email)) AS normalized_email, COUNT(*) AS aggregate')
            ->groupByRaw('LOWER(TRIM(email))')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasCanonicalNewsletterDuplicates) {
            throw new RuntimeException(
                'Cannot add newsletter email uniqueness: duplicate addresses exist after case and whitespace normalization.'
            );
        }

        DB::table('news_letters')->update([
            'email' => DB::raw('LOWER(TRIM(email))'),
        ]);

        Schema::table('news_letters', function (Blueprint $table): void {
            $table->unique('email', 'news_letters_email_unique');
        });

        Schema::table('contacts', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('join_now_queries', function (Blueprint $table): void {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('join_now_queries', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('contacts', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('news_letters', function (Blueprint $table): void {
            $table->dropUnique('news_letters_email_unique');
        });
    }
};
