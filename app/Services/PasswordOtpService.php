<?php

namespace App\Services;

use App\Actions\Fortify\ResetUserPassword;
use App\Mail\PasswordOtpMail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class PasswordOtpService
{
    public function request(string $email, Session $session): void
    {
        $key = 'password-otp:'.hash('sha256', $email);
        $id = Str::random(64);
        $session->forget('password_otp');
        $session->put('password_otp', ['key' => $key, 'id' => $id]);

        Cache::lock($key.':lock', 60)->block(3, function () use ($key, $id, $email): void {
            Cache::forget($key);
            $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

            if (! $user || ! $this->eligible($user)) {
                return;
            }

            $otp = (string) random_int(100000, 999999);
            Cache::put($key, [
                'id' => $id,
                'user_id' => $user->id,
                'password_hash' => hash('sha256', $user->password),
                'hash' => hash_hmac('sha256', $id.$otp, config('app.key')),
                'attempts' => 0,
                'verified' => false,
                'expires_at' => now()->addMinutes(10)->timestamp,
            ], now()->addMinutes(10));

            try {
                Mail::to($user->email)->send(new PasswordOtpMail($otp));
            } catch (Throwable) {
                Cache::forget($key);
                // Do not log transport exceptions that may include the OTP or SMTP credentials.
            }
        });
    }

    public function verify(string $otp, Session $session): void
    {
        $challenge = $this->sessionChallenge($session);
        Cache::lock($challenge['key'].':lock', 60)->block(3, function () use ($challenge, $otp, $session): void {
            $data = $this->challenge($challenge);
            if ($data['verified']) {
                throw ValidationException::withMessages(['otp' => 'Invalid verification code.']);
            }

            if ($data['attempts'] >= 5) {
                throw ValidationException::withMessages(['otp' => 'Too many attempts. Request a new code.']);
            }

            if (! hash_equals($data['hash'], hash_hmac('sha256', $data['id'].$otp, config('app.key')))) {
                $data['attempts']++;
                Cache::put($challenge['key'], $data, max(1, $data['expires_at'] - now()->timestamp));
                throw ValidationException::withMessages(['otp' => $data['attempts'] >= 5
                    ? 'Too many attempts. Request a new code.'
                    : 'Invalid verification code.']);
            }

            unset($data['hash']);
            $data['verified'] = true;
            $data['expires_at'] = now()->addMinutes(5)->timestamp;
            Cache::put($challenge['key'], $data, now()->addMinutes(5));
            $session->regenerate();
            $session->put('password_otp.verified', true);
        });
    }

    public function canReset(Session $session): bool
    {
        try {
            $data = $this->challenge($this->sessionChallenge($session));

            return $session->get('password_otp.verified') === true && $data['verified'];
        } catch (ValidationException) {
            return false;
        }
    }

    /** @param array<string, string> $input */
    public function reset(array $input, Session $session): void
    {
        $challenge = $this->sessionChallenge($session);
        Cache::lock($challenge['key'].':lock', 60)->block(3, function () use ($challenge, $input, $session): void {
            $data = $this->challenge($challenge);
            abort_unless($session->get('password_otp.verified') === true && $data['verified'], 403);
            $user = User::find($data['user_id']);
            abort_unless($user && $this->eligible($user) && hash_equals($data['password_hash'], hash('sha256', $user->password)), 403);
            abort_unless($challenge['key'] === 'password-otp:'.hash('sha256', Str::lower($user->email)), 403);

            app(ResetUserPassword::class)->reset($user, $input);
            $user->forceFill([
                'remember_token' => Str::random(60),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            Cache::forget($challenge['key']);
            $session->invalidate();
            $session->regenerateToken();
            event(new PasswordReset($user));
        });
    }

    private function eligible(User $user): bool
    {
        return Str::afterLast(Str::lower($user->email), '@') === config('goldeneye.organization_email_domain')
            && $user->hasAnyRole(['Admin', 'Staff']);
    }

    /** @return array{key: string, id: string} */
    private function sessionChallenge(Session $session): array
    {
        $challenge = $session->get('password_otp');
        if (! is_array($challenge) || ! isset($challenge['key'], $challenge['id'])) {
            throw ValidationException::withMessages(['otp' => 'OTP expired. Request a new code.']);
        }

        return $challenge;
    }

    /**
     * @param  array{key: string, id: string}  $challenge
     * @return array{id: string, user_id: int, password_hash: string, hash?: string, attempts: int, verified: bool, expires_at: int}
     */
    private function challenge(array $challenge): array
    {
        $data = Cache::get($challenge['key']);
        if (! is_array($data) || ! hash_equals($data['id'], $challenge['id']) || $data['expires_at'] <= now()->timestamp) {
            throw ValidationException::withMessages(['otp' => 'OTP expired. Request a new code.']);
        }

        return $data;
    }
}
