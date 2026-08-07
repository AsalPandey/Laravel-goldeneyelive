<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\JoinNowQuery;
use App\Models\NewsLetter;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LiveSiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Phase9ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_release_guide_contains_required_owner_gates_and_safe_deployment_commands(): void
    {
        $guide = file_get_contents(base_path('HOSTINGER_DEPLOYMENT_GUIDE.md'));

        $this->assertIsString($guide);
        $this->assertStringContainsString('Before deployment, select PHP 8.4 for Golden Eye Academy in Hostinger.', $guide);
        $this->assertStringContainsString('Confirm web and Composer commands use PHP 8.4 during deployment.', $guide);
        $this->assertStringContainsString('composer install --no-dev --optimize-autoloader --no-interaction', $guide);
        $this->assertStringContainsString('php artisan migrate --force', $guide);
        $this->assertStringContainsString('Do not run `db:seed`, `migrate:fresh`, `migrate:refresh`', $guide);
        $this->assertStringContainsString('SELECT LOWER(TRIM(email)) AS normalized_email', $guide);
        $this->assertStringContainsString('Back up `public/site/img/` in full', $guide);
        $this->assertStringContainsString('Do not deploy local untracked folders', $guide);
    }

    public function test_committed_manifest_references_the_reproducible_locked_asset(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $manifest = json_decode((string) file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);
        $assetPath = public_path('build/'.$manifest['resources/css/app.css']['file']);

        $this->assertSame(188, filesize($manifestPath));
        $this->assertSame('6d9d926f296c387d3dc39583689d7cba56f484f7bcd6505fbb11d997dbadf12a', hash_file('sha256', $manifestPath));
        $this->assertSame('assets/app-4krHC8Lc.css', $manifest['resources/css/app.css']['file']);
        $this->assertFileExists($assetPath);
        $this->assertSame(297153, filesize($assetPath));
        $this->assertSame('72cb05938b18f3ec2178f3a670a3b7eabd823417e4079b5b1dc1ebddbb99a6f3', hash_file('sha256', $assetPath));
        $this->assertFileDoesNotExist(public_path('build/assets/app-7ZVPn1kE.css'));
    }

    public function test_phase_two_migration_preserves_representative_content_and_inquiries(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(LiveSiteSeeder::class);

        User::factory()->create();
        Contact::query()->create([
            'name' => 'Phase Nine Contact',
            'email' => 'contact@example.test',
            'phone' => '9800000000',
            'subject' => 'Release rehearsal',
            'message' => 'Representative inquiry preserved through migration rehearsal.',
        ]);
        JoinNowQuery::query()->create([
            'firstName' => 'Phase',
            'lastName' => 'Nine',
            'email' => 'learner@example.test',
            'phone' => '9800000000',
            'address' => 'Pokhara',
            'course' => 'IELTS Masterclass',
            'queries' => 'Representative course inquiry preserved through migration rehearsal.',
        ]);
        DB::table('news_letters')->insert([
            'email' => '  Subscriber@Example.Test  ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $preservedCounts = $this->representativeTableCounts();

        $migration = require database_path('migrations/2026_07_28_193920_add_inquiry_reliability_to_submissions.php');
        $migration->down();
        $this->assertFalse(Schema::hasColumn('contacts', 'deleted_at'));
        $this->assertFalse(Schema::hasColumn('join_now_queries', 'deleted_at'));

        $migration->up();

        $this->assertSame($preservedCounts, $this->representativeTableCounts());
        $this->assertSame('subscriber@example.test', NewsLetter::query()->sole()->email);
        $this->assertTrue(Schema::hasColumn('contacts', 'deleted_at'));
        $this->assertTrue(Schema::hasColumn('join_now_queries', 'deleted_at'));

        $indexes = Schema::getIndexes('news_letters');
        $this->assertTrue(collect($indexes)->contains(
            fn (array $index): bool => $index['unique'] && $index['columns'] === ['email']
        ));
    }

    /**
     * @return array<string, int>
     */
    private function representativeTableCounts(): array
    {
        return collect([
            'users',
            'roles',
            'courses',
            'course_categories',
            'blog_posts',
            'f_a_q_s',
            'teachers',
            'testimonials',
            'contacts',
            'join_now_queries',
            'news_letters',
        ])->mapWithKeys(fn (string $table): array => [$table => DB::table($table)->count()])->all();
    }
}
