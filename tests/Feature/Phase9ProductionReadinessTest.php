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
        $editorScriptPath = public_path('build/'.$manifest['resources/js/app.js']['file']);
        $editorStylePath = public_path('build/'.$manifest['resources/js/app.js']['css'][0]);

        $this->assertSame(383, filesize($manifestPath));
        $this->assertSame('6b9af0667b7de52948a6b68e7a72d76374be3eb92a38fefdd5e66b68167a73c7', hash_file('sha256', $manifestPath));
        $this->assertSame('assets/app-8y0hZI5k.css', $manifest['resources/css/app.css']['file']);
        $this->assertFileExists($assetPath);
        $this->assertSame(298250, filesize($assetPath));
        $this->assertSame('72146f2971d20d83e065cb403d1edfe7ff16dbb47c8e53f10f8e7baf9c969e79', hash_file('sha256', $assetPath));
        $this->assertSame('assets/app-CRWgJY2L.js', $manifest['resources/js/app.js']['file']);
        $this->assertFileExists($editorScriptPath);
        $this->assertSame(533444, filesize($editorScriptPath));
        $this->assertSame('50095470b408f8f976bba8b8a81b649eeae6b17ae124be379ee084559bb773bc', hash_file('sha256', $editorScriptPath));
        $this->assertSame('assets/app-BC2rb0sg.css', $manifest['resources/js/app.js']['css'][0]);
        $this->assertFileExists($editorStylePath);
        $this->assertSame(161368, filesize($editorStylePath));
        $this->assertSame('6373ca5bbbb0b3e634388a15eb25b5f22d62ba9e215360416c8a175d22b16733', hash_file('sha256', $editorStylePath));
        $this->assertFileDoesNotExist(public_path('build/assets/app-Bxbibrtw.css'));
        $this->assertFileDoesNotExist(public_path('build/assets/app-BoAFSeC4.css'));
        $this->assertFileDoesNotExist(public_path('build/assets/app-4krHC8Lc.css'));
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
