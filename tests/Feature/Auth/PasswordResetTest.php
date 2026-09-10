<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordOtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Role::findOrCreate('Staff', 'web');
        Role::findOrCreate('Admin', 'web');
        $this->user = User::factory()->unverified()->create(['email' => 'staff@goldeneye.edu.np']);
        $this->user->assignRole('Staff');
    }

    private function requestCode(): string
    {
        $this->post(route('password.email'), ['email' => strtoupper($this->user->email)])
            ->assertRedirect(route('password.otp'))
            ->assertSessionHas('status', 'If an eligible account exists, an OTP has been sent.');

        return Mail::sent(PasswordOtpMail::class)->last()->otp;
    }

    private function verifyCode(): string
    {
        $code = $this->requestCode();
        $this->post(route('password.otp.verify'), ['otp' => $code])->assertRedirect(route('password.reset'));

        return $code;
    }

    /** @return array<string, string> */
    private function passwordInput(): array
    {
        return ['password' => 'New-Secret-Password1!', 'password_confirmation' => 'New-Secret-Password1!'];
    }

    public function test_screens_and_sender_and_hashed_cache_storage(): void
    {
        $this->get(route('password.request'))->assertOk()->assertSee('verification code');
        $code = $this->requestCode();
        $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $code);
        $this->get(route('password.otp'))->assertOk()->assertSee('Verification code');
        $mail = Mail::sent(PasswordOtpMail::class)->last();
        $this->assertSame(config('goldeneye.security_email'), $mail->envelope()->from->address);
        $this->assertSame('security@goldeneye.edu.np', $mail->envelope()->from->address);
        $this->assertSame('Golden Eye Academy Security', $mail->envelope()->from->name);
        $mail->assertSeeInHtml($code);
        $cached = Cache::get(session('password_otp.key'));
        $this->assertArrayHasKey('hash', $cached);
        $this->assertStringNotContainsString($code, json_encode($cached));
        $this->assertStringNotContainsString($code, json_encode(session()->all()));
        $this->assertDatabaseCount('password_reset_tokens', 0);
        $this->assertDatabaseCount('jobs', 0);
    }

    public function test_wrong_otp_is_rejected_and_not_flashed_into_session(): void
    {
        $code = $this->requestCode();
        $wrong = $code === '123456' ? '654321' : '123456';
        $this->post(route('password.otp.verify'), ['otp' => $wrong])->assertSessionHasErrors('otp');
        $this->assertNull(session()->getOldInput('otp'));
        $this->post(route('password.update'), $this->passwordInput())->assertForbidden();
    }

    public function test_expired_otp_is_rejected(): void
    {
        $code = $this->requestCode();
        $this->travel(10)->minutes();
        $this->post(route('password.otp.verify'), ['otp' => $code])->assertSessionHasErrors(['otp' => 'OTP expired. Request a new code.']);
    }

    public function test_five_wrong_attempts_block_even_the_correct_code(): void
    {
        $code = $this->requestCode();
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('password.otp.verify'), ['otp' => '000000'])->assertSessionHasErrors('otp');
        }
        $this->post(route('password.otp.verify'), ['otp' => $code])->assertSessionHasErrors(['otp' => 'Too many attempts. Request a new code.']);
        $this->post(route('password.update'), $this->passwordInput())->assertForbidden();
    }

    public function test_verified_user_can_reset_once_and_otp_is_consumed(): void
    {
        $code = $this->verifyCode();
        $key = session('password_otp.key');
        $this->assertArrayNotHasKey('hash', Cache::get($key));
        $this->get(route('password.reset'))->assertOk();
        $this->post(route('password.otp.verify'), ['otp' => $code])->assertSessionHasErrors('otp');
        $this->post(route('password.update'), $this->passwordInput())->assertRedirect(route('login'))->assertSessionHas('status', 'Password updated successfully.');
        $this->assertTrue(Hash::check($this->passwordInput()['password'], $this->user->fresh()->password));
        $this->assertNotNull($this->user->fresh()->email_verified_at);
        $this->assertFalse(Cache::has($key));
        $this->assertNull(session('password_otp'));
        $this->post(route('password.update'), $this->passwordInput())->assertForbidden();
        $this->assertGuest();
    }

    public function test_password_validation_does_not_consume_verified_challenge(): void
    {
        $this->verifyCode();
        $this->post(route('password.update'), ['password' => 'x', 'password_confirmation' => 'y'])->assertSessionHasErrors('password');
        $this->post(route('password.update'), $this->passwordInput())->assertRedirect(route('login'));
    }

    public function test_unverified_and_expired_verified_sessions_cannot_reset(): void
    {
        $this->post(route('password.update'), $this->passwordInput())->assertForbidden();
        $this->get(route('password.reset'))->assertRedirect(route('password.request'));
        $this->verifyCode();
        $this->travel(5)->minutes();
        $this->post(route('password.update'), $this->passwordInput())->assertForbidden();
    }

    public function test_resend_invalidates_old_code_and_verified_session(): void
    {
        $oldCode = $this->verifyCode();
        $oldSession = session('password_otp');
        $this->travel(61)->seconds();
        $newCode = $this->requestCode();
        $newSession = session('password_otp');
        $this->assertNotSame($oldSession['id'], $newSession['id']);
        $this->withSession(['password_otp' => $oldSession])
            ->post(route('password.update'), $this->passwordInput())->assertForbidden();
        $this->withSession(['password_otp' => $oldSession])
            ->post(route('password.otp.verify'), ['otp' => $oldCode])->assertSessionHasErrors('otp');
        $this->withSession(['password_otp' => $newSession])
            ->post(route('password.otp.verify'), ['otp' => $newCode])->assertRedirect(route('password.reset'));
    }

    public function test_ineligible_accounts_get_generic_response_and_cannot_reset(): void
    {
        $outside = User::factory()->create(['email' => 'outside@gmail.com']);
        $outside->assignRole('Admin');
        User::factory()->create(['email' => 'ordinary@goldeneye.edu.np']);
        foreach (['outside@gmail.com', 'ordinary@goldeneye.edu.np', 'missing@goldeneye.edu.np', 'person@goldeneye.edu.np.fake.com'] as $email) {
            $this->post(route('password.email'), ['email' => $email])->assertRedirect(route('password.otp'))
                ->assertSessionHas('status', 'If an eligible account exists, an OTP has been sent.');
            $this->post(route('password.otp.verify'), ['otp' => '123456'])->assertSessionHasErrors('otp');
            $this->post(route('password.update'), $this->passwordInput())->assertForbidden();
        }
        Mail::assertNothingSent();
    }

    public function test_resend_and_hourly_limits_apply(): void
    {
        $this->requestCode();
        $this->post(route('password.email'), ['email' => $this->user->email])->assertStatus(429);
        for ($i = 0; $i < 4; $i++) {
            $this->travel(61)->seconds();
            $this->requestCode();
        }
        $this->travel(61)->seconds();
        $this->post(route('password.email'), ['email' => $this->user->email])->assertStatus(429);
        Mail::assertSentCount(5);
    }

    public function test_deleted_or_role_removed_account_cannot_reset(): void
    {
        $this->verifyCode();
        $this->user->removeRole('Staff');
        $this->post(route('password.update'), $this->passwordInput())->assertForbidden();
        $this->user->delete();
        $this->post(route('password.update'), $this->passwordInput())->assertForbidden();
    }

    public function test_password_or_email_change_invalidates_challenge_and_other_email_cannot_be_targeted(): void
    {
        $this->verifyCode();
        $other = User::factory()->create();
        $oldPassword = $other->password;
        $this->post(route('password.update'), [...$this->passwordInput(), 'email' => $other->email])->assertRedirect(route('login'));
        $this->assertSame($oldPassword, $other->fresh()->password);
        $this->travel(61)->seconds();
        $this->verifyCode();
        $this->user->forceFill(['password' => 'Another-password'])->save();
        $this->post(route('password.update'), $this->passwordInput())->assertForbidden();
    }

    public function test_old_token_endpoint_is_unavailable_and_admin_is_eligible(): void
    {
        $this->get('/reset-password/old-token')->assertNotFound();
        $this->user->syncRoles('Admin');
        $this->verifyCode();
        $this->post(route('password.update'), $this->passwordInput())->assertRedirect(route('login'));
    }

    public function test_a_code_requires_the_requesting_session_and_email_must_still_match(): void
    {
        $code = $this->requestCode();
        $challenge = session('password_otp');
        session()->forget('password_otp');
        $this->post(route('password.otp.verify'), ['otp' => $code])->assertSessionHasErrors('otp');
        $this->withSession(['password_otp' => $challenge])->post(route('password.otp.verify'), ['otp' => $code])->assertRedirect(route('password.reset'));
        $this->user->forceFill(['email' => 'changed@goldeneye.edu.np'])->save();
        $this->post(route('password.update'), $this->passwordInput())->assertForbidden();
    }

    public function test_mail_failure_invalidates_challenge_without_exposing_transport_details(): void
    {
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('Sensitive transport detail'));
        $this->post(route('password.email'), ['email' => $this->user->email])->assertRedirect(route('password.otp'));
        $this->assertFalse(Cache::has(session('password_otp.key')));
        $this->post(route('password.otp.verify'), ['otp' => '123456'])->assertSessionHasErrors('otp');
    }

    public function test_malformed_email_is_validation_error_and_ip_request_limit_applies(): void
    {
        $this->post(route('password.email'), ['email' => ['invalid']])->assertSessionHasErrors('email');
        for ($i = 0; $i < 4; $i++) {
            $this->post(route('password.email'), ['email' => 'missing'.$i.'@goldeneye.edu.np'])->assertRedirect();
        }
        $this->post(route('password.email'), ['email' => 'another@goldeneye.edu.np'])->assertStatus(429);
        Mail::assertNothingSent();
    }
}
