<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReconcileTeacherSocialLinksTest extends TestCase
{
    use RefreshDatabase;

    private const FACEBOOK_URL = 'https://www.facebook.com/goldeneyeacademy';

    private const LINKEDIN_URL = 'https://www.linkedin.com/company/golden-eye-academy/';

    public function test_dry_run_reports_but_does_not_change_exact_institutional_links(): void
    {
        $teacher = Teacher::factory()->create([
            'facebook_url' => self::FACEBOOK_URL,
            'linkedin_url' => self::LINKEDIN_URL,
        ]);

        $this->artisan('goldeneye:reconcile-teacher-social-links')
            ->expectsOutputToContain('Dry run complete')
            ->assertSuccessful();

        $this->assertSame(self::FACEBOOK_URL, $teacher->refresh()->facebook_url);
        $this->assertSame(self::LINKEDIN_URL, $teacher->linkedin_url);
    }

    public function test_apply_clears_only_exact_institutional_values_and_is_idempotent(): void
    {
        $institutional = Teacher::factory()->create([
            'facebook_url' => self::FACEBOOK_URL,
            'linkedin_url' => self::LINKEDIN_URL,
        ]);
        $mixed = Teacher::factory()->create([
            'facebook_url' => self::FACEBOOK_URL,
            'linkedin_url' => 'https://www.linkedin.com/in/verified-teacher/',
        ]);
        $personal = Teacher::factory()->create([
            'facebook_url' => 'https://www.facebook.com/verified.teacher',
            'linkedin_url' => 'https://www.linkedin.com/in/another-verified-teacher/',
        ]);

        $this->artisan('goldeneye:reconcile-teacher-social-links', ['--apply' => true])
            ->expectsOutputToContain('3 exact institutional Teacher social value(s) cleared.')
            ->assertSuccessful();

        $this->assertNull($institutional->refresh()->facebook_url);
        $this->assertNull($institutional->linkedin_url);
        $this->assertNull($mixed->refresh()->facebook_url);
        $this->assertSame('https://www.linkedin.com/in/verified-teacher/', $mixed->linkedin_url);
        $this->assertSame('https://www.facebook.com/verified.teacher', $personal->refresh()->facebook_url);
        $this->assertSame('https://www.linkedin.com/in/another-verified-teacher/', $personal->linkedin_url);

        $this->artisan('goldeneye:reconcile-teacher-social-links', ['--apply' => true])
            ->expectsOutputToContain('0 exact institutional Teacher social value(s) cleared.')
            ->assertSuccessful();
    }

    public function test_production_apply_requires_explicit_backup_confirmation(): void
    {
        Teacher::factory()->create(['facebook_url' => self::FACEBOOK_URL]);
        $originalEnvironment = $this->app->environment();
        $this->app->detectEnvironment(fn (): string => 'production');

        try {
            $this->artisan('goldeneye:reconcile-teacher-social-links', ['--apply' => true])
                ->expectsOutputToContain('Production apply refused')
                ->assertFailed();

            $this->assertDatabaseHas(Teacher::class, ['facebook_url' => self::FACEBOOK_URL]);
        } finally {
            $this->app->detectEnvironment(fn (): string => $originalEnvironment);
        }
    }
}
