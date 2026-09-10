<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordOtpController extends Controller
{
    public function create(): View
    {
        return view('pages::auth.forgot-password');
    }

    public function store(Request $request, PasswordOtpService $otp): RedirectResponse
    {
        $input = $request->validate(['email' => ['required', 'string', 'email', 'max:255']]);
        $otp->request(Str::lower(trim($input['email'])), $request->session());

        return to_route('password.otp')->with('status', 'If an eligible account exists, an OTP has been sent.');
    }

    public function challenge(Request $request): View|RedirectResponse
    {
        return $request->session()->has('password_otp')
            ? view('pages::auth.verify-otp')
            : to_route('password.request');
    }

    public function verify(Request $request, PasswordOtpService $otp): RedirectResponse
    {
        $input = $request->validate(['otp' => ['required', 'string', 'regex:/^[0-9]{6}$/']]);
        $otp->verify($input['otp'], $request->session());

        return to_route('password.reset');
    }

    public function edit(Request $request, PasswordOtpService $otp): View|RedirectResponse
    {
        return $otp->canReset($request->session())
            ? view('pages::auth.reset-password')
            : to_route('password.request')->withErrors(['email' => 'OTP expired. Request a new code.']);
    }

    public function update(Request $request, PasswordOtpService $otp): RedirectResponse
    {
        abort_unless($otp->canReset($request->session()), 403);
        $otp->reset($request->only('password', 'password_confirmation'), $request->session());

        return to_route('login')->with('status', 'Password updated successfully.');
    }
}
