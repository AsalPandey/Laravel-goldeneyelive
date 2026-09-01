<?php

namespace Tests\Feature\Security;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\BlogSeeder;
use Database\Seeders\CourseCategorySeeder;
use Database\Seeders\CourseFaqSeeder;
use Database\Seeders\CourseSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\FAQSeeder;
use Database\Seeders\LiveSiteSeeder;
use Database\Seeders\NoticeSeeder;
use Database\Seeders\ServicePillarSeeder;
use Database\Seeders\SiteSettingSeeder;
use Database\Seeders\TeacherSeeder;
use Database\Seeders\TestimonialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SeederContainmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_generic_database_seeding_only_creates_system_reference_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('roles', 4);
        $this->assertDatabaseCount(User::class, 0);
        $this->assertDatabaseCount(Course::class, 0);
        $this->assertDatabaseCount(SiteSetting::class, 0);
    }

    /**
     * @param  class-string  $seeder
     */
    #[DataProvider('baselineSeeders')]
    public function test_baseline_content_seeders_refuse_production_execution(string $seeder): void
    {
        $originalEnvironment = $this->app->environment();
        $this->app['env'] = 'production';

        try {
            $this->expectException(LogicException::class);
            $this->seed($seeder);
        } finally {
            $this->app['env'] = $originalEnvironment;
        }
    }

    /**
     * @return array<string, array{class-string}>
     */
    public static function baselineSeeders(): array
    {
        return [
            'live site' => [LiveSiteSeeder::class],
            'blog' => [BlogSeeder::class],
            'course category' => [CourseCategorySeeder::class],
            'course FAQ' => [CourseFaqSeeder::class],
            'course' => [CourseSeeder::class],
            'faq' => [FAQSeeder::class],
            'notice' => [NoticeSeeder::class],
            'service pillar' => [ServicePillarSeeder::class],
            'site setting' => [SiteSettingSeeder::class],
            'teacher' => [TeacherSeeder::class],
            'testimonial' => [TestimonialSeeder::class],
        ];
    }

    public function test_baseline_content_preserves_environment_settings_accounts_and_operational_records(): void
    {
        $this->seed(LiveSiteSeeder::class);

        SiteSetting::updateOrCreate(
            ['key' => 'recaptcha_secret_key'],
            ['value' => 'test-environment-secret', 'type' => 'text'],
        );
        SiteSetting::where('key', 'google_analytics_id')->update(['value' => 'test-analytics-id']);
        SiteSetting::where('key', 'hero_title')->update(['value' => 'Damaged public content']);

        $user = User::factory()->create([
            'password' => Hash::make('existing-test-password'),
        ]);
        $passwordHash = $user->password;

        DB::table('contacts')->insert([
            'name' => 'Inquiry Owner',
            'email' => 'inquiry@example.test',
            'phone' => '0000000000',
            'subject' => 'Course',
            'message' => 'Protected inquiry',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('news_letters')->insert([
            'email' => 'subscriber@example.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('join_now_queries')->insert([
            'firstName' => 'Inquiry',
            'lastName' => 'Owner',
            'email' => 'enrollment@example.test',
            'phone' => '0000000000',
            'address' => 'Pokhara',
            'course' => 'Course',
            'queries' => 'Protected enrollment inquiry',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->seed(LiveSiteSeeder::class);

        $this->assertSame('test-environment-secret', SiteSetting::getValue('recaptcha_secret_key'));
        $this->assertSame('test-analytics-id', SiteSetting::getValue('google_analytics_id'));
        $this->assertSame('Build skills you can use with confidence.', SiteSetting::getValue('hero_title'));
        $this->assertSame($passwordHash, $user->fresh()->password);
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount('contacts', 1);
        $this->assertDatabaseCount('news_letters', 1);
        $this->assertDatabaseCount('join_now_queries', 1);
    }

    public function test_repeated_blog_seeding_preserves_deterministic_publication_dates(): void
    {
        $this->seed(BlogSeeder::class);
        $publicationDates = BlogPost::orderBy('slug')->pluck('published_at', 'slug');

        $this->travel(30)->days();
        $this->seed(BlogSeeder::class);

        $this->assertEquals(
            $publicationDates->all(),
            BlogPost::orderBy('slug')->pluck('published_at', 'slug')->all(),
        );
    }
}
