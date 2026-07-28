<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_routes_are_not_registered(): void
    {
        $this->assertFalse(Route::has('register'));
        $this->assertFalse(Route::has('register.store'));

        $this->get('/register')->assertNotFound();
        $this->post('/register')->assertNotFound();
    }

    public function test_direct_registration_attempt_cannot_create_any_user(): void
    {
        $this->post('/register', [
            'name' => 'Unapproved Account',
            'email' => 'unapproved@example.test',
            'password' => 'test-only-password',
            'password_confirmation' => 'test-only-password',
        ]);

        $this->assertDatabaseCount(User::class, 0);
        $this->assertGuest();
    }

    public function test_login_screen_has_no_public_registration_link(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('New Account?')
            ->assertDontSee('Register');
    }
}
