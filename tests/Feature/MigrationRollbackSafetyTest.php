<?php

namespace Tests\Feature;

use App\Models\FAQ;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationRollbackSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_ambiguous_conditional_rollbacks_preserve_columns_and_data(): void
    {
        DB::table('notices')->insert([
            'title' => 'Rollback sentinel',
            'image' => 'sentinel.jpg',
            'status' => 'active',
            'link' => '/sentinel',
            'button_text' => 'Sentinel',
            'display_type' => 'popup',
            'is_urgent' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('blog_posts')->insert([
            'title' => 'Rollback sentinel',
            'slug' => 'rollback-sentinel',
            'content' => 'Sentinel content',
            'category' => 'Sentinel category',
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('join_now_queries')->insert([
            'firstName' => 'Rollback',
            'lastName' => 'Sentinel',
            'email' => 'rollback@example.com',
            'phone' => '9823456789',
            'address' => '',
            'course' => 'Sentinel course',
            'queries' => '',
            'selected_course' => 'sentinel-course',
            'source_page' => '/sentinel',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($this->ambiguousMigrationPaths() as $path) {
            $migration = require database_path($path);
            $migration->down();
        }

        foreach (['link', 'button_text', 'display_type', 'is_urgent', 'starts_at', 'expires_at', 'meta_title'] as $column) {
            $this->assertTrue(Schema::hasColumn('notices', $column), "Expected notices.{$column} to survive rollback.");
        }
        $this->assertTrue(Schema::hasColumn('blog_posts', 'category'));
        $this->assertTrue(Schema::hasColumn('blog_posts', 'schema_markup'));
        $this->assertTrue(Schema::hasColumn('join_now_queries', 'selected_course'));
        $this->assertTrue(Schema::hasColumn('join_now_queries', 'source_page'));
        $this->assertDatabaseHas('notices', ['title' => 'Rollback sentinel', 'link' => '/sentinel']);
        $this->assertDatabaseHas('blog_posts', ['slug' => 'rollback-sentinel', 'category' => 'Sentinel category']);
        $this->assertDatabaseHas('join_now_queries', ['email' => 'rollback@example.com', 'selected_course' => 'sentinel-course']);

        foreach ($this->ambiguousMigrationPaths() as $path) {
            $migration = require database_path($path);
            $migration->up();
        }

        $this->assertDatabaseHas('notices', ['title' => 'Rollback sentinel', 'link' => '/sentinel']);
        $this->assertDatabaseHas('blog_posts', ['slug' => 'rollback-sentinel', 'category' => 'Sentinel category']);
        $this->assertDatabaseHas('join_now_queries', ['email' => 'rollback@example.com', 'selected_course' => 'sentinel-course']);
    }

    public function test_faq_seo_migration_reverses_only_its_known_columns_and_reapplies(): void
    {
        $faq = FAQ::factory()->create([
            'question' => 'Rollback-safe question',
            'answer' => 'Rollback-safe answer',
            'status' => 'active',
            'meta_title' => 'Introduced metadata',
            'meta_description' => 'Introduced description',
            'order_priority' => 42,
            'meta_keywords' => 'Later metadata',
            'aeo_summary' => 'Later summary',
            'schema_markup' => '{"@type":"Question"}',
        ]);

        $migration = require database_path('migrations/2026_04_26_092157_add_seo_fields_to_faqs_table.php');
        $migration->down();

        $this->assertFalse(Schema::hasColumn('f_a_q_s', 'meta_title'));
        $this->assertFalse(Schema::hasColumn('f_a_q_s', 'meta_description'));
        $this->assertFalse(Schema::hasColumn('f_a_q_s', 'order_priority'));
        $this->assertTrue(Schema::hasColumn('f_a_q_s', 'meta_keywords'));
        $this->assertTrue(Schema::hasColumn('f_a_q_s', 'aeo_summary'));
        $this->assertTrue(Schema::hasColumn('f_a_q_s', 'schema_markup'));
        $this->assertDatabaseHas('f_a_q_s', [
            'id' => $faq->id,
            'question' => 'Rollback-safe question',
            'answer' => 'Rollback-safe answer',
            'meta_keywords' => 'Later metadata',
        ]);

        $migration->up();

        $this->assertTrue(Schema::hasColumn('f_a_q_s', 'meta_title'));
        $this->assertTrue(Schema::hasColumn('f_a_q_s', 'meta_description'));
        $this->assertTrue(Schema::hasColumn('f_a_q_s', 'order_priority'));
        $this->assertDatabaseHas('f_a_q_s', [
            'id' => $faq->id,
            'question' => 'Rollback-safe question',
            'answer' => 'Rollback-safe answer',
            'meta_keywords' => 'Later metadata',
            'order_priority' => 0,
        ]);
    }

    public function test_contract_widening_rollback_and_reapply_never_shrinks_or_loses_data(): void
    {
        $longReferrer = str_repeat('r', 500);
        DB::table('contacts')->insert([
            'name' => 'Rollback Sentinel',
            'email' => 'contract-rollback@example.com',
            'phone' => '9823456789',
            'subject' => 'Sentinel',
            'message' => 'Sentinel',
            'landing_page' => $longReferrer,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migration = require database_path('migrations/2026_08_23_173930_align_production_data_contracts.php');
        $migration->down();

        $this->assertSame('text', Schema::getColumnType('contacts', 'landing_page'));
        $this->assertDatabaseHas('contacts', [
            'email' => 'contract-rollback@example.com',
            'landing_page' => $longReferrer,
        ]);

        $migration->up();

        $this->assertSame('text', Schema::getColumnType('contacts', 'landing_page'));
        $this->assertDatabaseHas('contacts', [
            'email' => 'contract-rollback@example.com',
            'landing_page' => $longReferrer,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function ambiguousMigrationPaths(): array
    {
        return [
            'migrations/2026_04_25_061849_add_link_fields_to_notices_table.php',
            'migrations/2026_04_29_071915_add_seo_and_aeo_fields_to_models.php',
            'migrations/2026_04_29_142437_harden_database_for_seo_and_functionality.php',
            'migrations/2026_04_30_002218_add_scheduling_and_display_fields_to_notices_table.php',
            'migrations/2026_05_02_173802_add_category_to_blog_posts_table.php',
            'migrations/2026_05_19_223516_add_lead_qualification_fields_to_join_now_queries_table.php',
        ];
    }
}
