<?php

namespace Tests\Feature;

use App\Mail\ContactMail;
use App\Models\Contact;
use App\Models\User;
use App\Support\Recaptcha;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RecaptchaConfigurationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        cache()->flush();
    }

    public function test_public_forms_remain_usable_and_render_no_widget_when_both_keys_are_absent(): void
    {
        Mail::fake();

        $this->assertSame(Recaptcha::Disabled, Recaptcha::status());
        $this->assertFalse(Recaptcha::challengeRequired());

        $this->get(route('contact'))
            ->assertOk()
            ->assertDontSee('g-recaptcha', false)
            ->assertDontSee('google.com/recaptcha/api.js', false);

        $this->postValidContact()
            ->assertRedirect(route('contact'))
            ->assertSessionDoesntHaveErrors('g-recaptcha-response');

        $this->assertDatabaseHas(Contact::class, ['email' => 'asha@example.com']);
        Mail::assertQueued(ContactMail::class);
    }

    /**
     * @param  array<string, string>  $configuredKey
     */
    #[DataProvider('partialKeyConfigurations')]
    public function test_partial_configuration_is_logged_and_does_not_make_public_forms_impossible(
        array $configuredKey,
        string $missingKey,
    ): void {
        Mail::fake();
        Log::spy();

        config([
            'services.recaptcha.bypass' => false,
            'services.recaptcha.'.($configuredKey['key'] === 'recaptcha_site_key' ? 'site_key' : 'secret_key') => $configuredKey['value'],
        ]);

        $this->assertSame(Recaptcha::Misconfigured, Recaptcha::status());
        $this->assertFalse(Recaptcha::challengeRequired());

        $this->get(route('contact'))
            ->assertOk()
            ->assertDontSee('g-recaptcha', false)
            ->assertDontSee('google.com/recaptcha/api.js', false);

        $this->postValidContact()
            ->assertRedirect(route('contact'))
            ->assertSessionDoesntHaveErrors('g-recaptcha-response');

        $this->assertDatabaseHas(Contact::class, ['email' => 'asha@example.com']);

        Log::shouldHaveReceived('log')
            ->with('warning', 'reCAPTCHA is not fully configured.', [
                'environment' => 'testing',
                'verification_mode' => 'disabled',
                'site_key_present' => $missingKey !== 'recaptcha_site_key',
                'secret_key_present' => $missingKey !== 'recaptcha_secret_key',
            ])
            ->atLeast()
            ->once();
    }

    public static function partialKeyConfigurations(): array
    {
        return [
            'site key only' => [
                ['key' => 'recaptcha_site_key', 'value' => 'site-key'],
                'recaptcha_secret_key',
            ],
            'secret key only' => [
                ['key' => 'recaptcha_secret_key', 'value' => 'secret-key'],
                'recaptcha_site_key',
            ],
        ];
    }

    public function test_both_keys_render_the_shared_challenge_on_each_approved_public_form(): void
    {
        $this->configureBothKeys();

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('google.com/recaptcha/api.js', false)
            ->assertSee('data-sitekey="site-key"', false);

        $this->get(route('join-now'))
            ->assertOk()
            ->assertSee('data-sitekey="site-key"', false);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="newsletterForm"', false)
            ->assertSee('data-sitekey="site-key"', false)
            ->assertSee('data-size="compact"', false);
    }

    public function test_missing_challenge_has_a_clear_error_and_preserves_contact_input(): void
    {
        $this->configureBothKeys();

        $this->postValidContact()
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors([
                'g-recaptcha-response' => 'Please complete the security verification.',
            ])
            ->assertSessionHasInput('name', 'Asha Sharma')
            ->assertSessionHasInput('email', 'asha@example.com');

        $this->assertDatabaseMissing(Contact::class, ['email' => 'asha@example.com']);
    }

    public function test_missing_challenge_blocks_course_help_and_newsletter_forms_with_preserved_input(): void
    {
        $this->configureBothKeys();

        $this->from(route('join-now'))->post(route('join-now-submit'), [
            'full_name' => 'Asha Sharma',
            'phone' => '9823456780',
            'help_topic' => 'Choosing a course',
        ])
            ->assertRedirect(route('join-now'))
            ->assertSessionHasErrors('g-recaptcha-response')
            ->assertSessionHasInput('full_name', 'Asha Sharma');

        $this->from(route('home'))->post(route('newsletter'), [
            'email' => 'asha@example.com',
        ])
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('g-recaptcha-response', null, 'newsletter')
            ->assertSessionHasInput('email', 'asha@example.com');
    }

    public function test_failed_or_expired_challenge_has_a_clear_error_and_preserves_input(): void
    {
        $this->configureBothKeys();
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['timeout-or-duplicate'],
            ]),
        ]);

        $this->postValidContact([
            'g-recaptcha-response' => 'expired-token',
        ])
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors([
                'g-recaptcha-response' => 'Security verification failed or expired. Please complete it again.',
            ])
            ->assertSessionHasInput('message', 'I want to know more about IELTS classes.');

        $this->assertDatabaseMissing(Contact::class, ['email' => 'asha@example.com']);
    }

    public function test_successful_challenge_is_verified_server_side(): void
    {
        Mail::fake();
        $this->configureBothKeys();
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
        ]);

        $this->postValidContact([
            'g-recaptcha-response' => 'valid-token',
        ])->assertRedirect(route('contact'));

        Http::assertSent(fn ($request): bool => $request['secret'] === 'secret-key'
            && $request['response'] === 'valid-token');

        $this->assertDatabaseHas(Contact::class, ['email' => 'asha@example.com']);
    }

    public function test_admin_branding_screen_reports_partial_configuration(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        config([
            'services.recaptcha.bypass' => false,
            'services.recaptcha.site_key' => 'site-key',
            'services.recaptcha.secret_key' => null,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertSee('Configuration incomplete: the deployment readiness check will fail and production submissions fail closed.', false)
            ->assertSee('Credentials are environment-managed', false);
    }

    private function configureBothKeys(): void
    {
        config([
            'services.recaptcha.bypass' => false,
            'services.recaptcha.site_key' => 'site-key',
            'services.recaptcha.secret_key' => 'secret-key',
        ]);
    }

    /**
     * @param  array<string, string>  $overrides
     */
    private function postValidContact(array $overrides = []): TestResponse
    {
        return $this->from(route('contact'))->post(route('contact-submit'), [
            'name' => 'Asha Sharma',
            'phone' => '9823456780',
            'email' => 'asha@example.com',
            'subject' => 'Course question',
            'message' => 'I want to know more about IELTS classes.',
            ...$overrides,
        ]);
    }
}
