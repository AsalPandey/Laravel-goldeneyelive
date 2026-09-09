<?php

namespace Tests\Feature;

use App\Mail\ContactMail;
use App\Models\Contact;
use App\Models\Course;
use App\Models\JoinNowQuery;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\StructuredData;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OfficialAcademyEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        cache()->flush();
    }

    public function test_canonical_official_email_is_contact_at_goldeneye_edu_np(): void
    {
        $this->assertSame('contact@goldeneye.edu.np', config('goldeneye.official_email'));
    }

    public function test_contact_form_queues_notification_to_canonical_official_email_even_if_site_setting_is_different(): void
    {
        Mail::fake();

        SiteSetting::updateOrCreate(
            ['key' => 'site_email'],
            ['value' => 'rogue-address@example.com', 'type' => 'text']
        );

        $payload = [
            'name' => 'Asha Sharma',
            'phone' => '9823456780',
            'email' => 'asha@example.com',
            'subject' => 'Course query',
            'message' => 'Please share information on IELTS classes.',
        ];

        $response = $this->from(route('contact'))->post(route('contact-submit'), $payload);

        $response->assertRedirect(route('contact'));

        // Assert notification was queued specifically to canonical official email
        Mail::assertQueued(ContactMail::class, function (ContactMail $mail) {
            return $mail->hasTo('contact@goldeneye.edu.np')
                && ! $mail->hasTo('rogue-address@example.com');
        });

        // Assert database inquiry preservation behavior remains intact
        $this->assertDatabaseHas(Contact::class, [
            'name' => 'Asha Sharma',
            'email' => 'asha@example.com',
            'phone' => '9823456780',
            'subject' => 'Course query',
        ]);
    }

    public function test_course_help_form_queues_notification_to_canonical_official_email_even_if_site_setting_is_different(): void
    {
        Mail::fake();

        SiteSetting::updateOrCreate(
            ['key' => 'site_email'],
            ['value' => 'rogue-address@example.com', 'type' => 'text']
        );

        $course = Course::factory()->create([
            'name' => 'IELTS Academic Preparation',
            'slug' => 'ielts-academic-prep',
            'status' => 'active',
        ]);

        $payload = [
            'firstName' => 'Asha',
            'lastName' => 'Sharma',
            'email' => 'asha@example.com',
            'phone' => '9823456780',
            'course' => $course->slug,
            'contactMethod' => 'Phone Call',
            'goal' => 'Need morning IELTS batch',
            'queries' => 'Confirm starting date and fees',
        ];

        $response = $this->from(route('join-now'))->post(route('join-now-submit'), $payload);

        $response->assertRedirect();

        // Assert notification was queued specifically to canonical official email
        Mail::assertQueued(ContactMail::class, function (ContactMail $mail) {
            return $mail->hasTo('contact@goldeneye.edu.np')
                && ! $mail->hasTo('rogue-address@example.com');
        });

        // Assert database inquiry preservation behavior remains intact
        $this->assertDatabaseHas(JoinNowQuery::class, [
            'firstName' => 'Asha',
            'lastName' => 'Sharma',
            'email' => 'asha@example.com',
            'phone' => '9823456780',
            'course' => 'IELTS Academic Preparation',
        ]);
    }

    public function test_seeder_baseline_seeds_canonical_email_and_contains_no_old_gmail(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $seededEmail = SiteSetting::getValue('site_email');
        $this->assertSame('contact@goldeneye.edu.np', $seededEmail);

        // Verify seeder file itself does not contain the old gmail
        $seederContent = file_get_contents(database_path('seeders/SiteSettingSeeder.php'));
        $this->assertStringNotContainsString('goldeneyeacademy2008@gmail.com', $seederContent);
    }

    public function test_public_pages_render_canonical_official_email(): void
    {
        $course = Course::factory()->create([
            'name' => 'English Fluency Pro',
            'slug' => 'english-fluency-pro',
            'status' => 'active',
        ]);

        // 1. Homepage & Footer
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('contact@goldeneye.edu.np');

        // 2. Contact page
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('href="mailto:contact@goldeneye.edu.np"', false)
            ->assertDontSee('info@goldeneye.edu.np')
            ->assertDontSee('goldeneyeacademy2008@gmail.com');

        // 3. Course detail page local trust marker
        $this->get(route('courses-detail', ['slug' => $course->slug]))
            ->assertOk()
            ->assertSee('contact@goldeneye.edu.np')
            ->assertDontSee('goldeneyeacademy2008@gmail.com');

        // 4. Structured data Organization schema
        $siteGraph = StructuredData::siteGraph([
            'site_name' => 'Golden Eye',
            'site_name_suffix' => 'Academy',
        ]);
        $this->assertSame('contact@goldeneye.edu.np', $siteGraph['@graph'][0]['email']);
    }

    public function test_cms_branding_hub_renders_canonical_email_as_readonly(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertSee('contact@goldeneye.edu.np')
            ->assertSee('name="site_email"', false)
            ->assertSee('readonly', false);
    }

    public function test_recaptcha_failure_rejects_contact_submission_and_does_not_queue_mail(): void
    {
        Mail::fake();

        config([
            'services.recaptcha.bypass' => false,
            'services.recaptcha.site_key' => 'fake-site-key',
            'services.recaptcha.secret_key' => 'fake-secret-key',
        ]);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => false], 200),
        ]);

        $payload = [
            'name' => 'Spam Bot',
            'phone' => '9823456780',
            'email' => 'bot@example.com',
            'subject' => 'Spam inquiry',
            'message' => 'Spam content',
            'g-recaptcha-response' => 'invalid-token',
        ];

        $response = $this->from(route('contact'))->post(route('contact-submit'), $payload);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHasErrors('g-recaptcha-response');

        Mail::assertNothingQueued();
        $this->assertDatabaseMissing(Contact::class, ['email' => 'bot@example.com']);
    }

    public function test_data_migration_sets_canonical_official_site_email_and_rolls_back(): void
    {
        // Setup existing row with old gmail
        DB::table('site_settings')->updateOrInsert(
            ['key' => 'site_email'],
            ['value' => 'goldeneyeacademy2008@gmail.com', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()]
        );

        $migration = require database_path('migrations/2026_09_09_103000_set_canonical_official_site_email.php');

        // Test up()
        $migration->up();

        $this->assertSame(
            'contact@goldeneye.edu.np',
            DB::table('site_settings')->where('key', 'site_email')->value('value')
        );

        // Test down()
        $migration->down();

        $this->assertSame(
            'goldeneyeacademy2008@gmail.com',
            DB::table('site_settings')->where('key', 'site_email')->value('value')
        );
    }
}
