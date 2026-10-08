<?php
// app/Http/Controllers/Auth/GoogleController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Send the user to Google's consent screen.
     */
    public function redirect(): RedirectResponse
    {
        if (empty(config('services.google.client_id'))) {
            abort(404);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Google sends the user back here after they approve access.
     * We match them to an existing account by email, or create a new one.
     * Google emails are verified, so matching by email is safe.
     */
    public function callback(): RedirectResponse
    {
        if (empty(config('services.google.client_id'))) {
            return redirect()->route('login')->withErrors([
                'email' => 'تسجيل الدخول عبر Google غير مفعّل.',
            ]);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')->withErrors([
                'email' => 'تعذّر تسجيل الدخول عبر Google. حاول مرة أخرى.',
            ]);
        }

        $email = strtolower(trim((string) $googleUser->getEmail()));

        if ($email === '') {
            return redirect()->route('login')->withErrors([
                'email' => 'تعذّر الحصول على البريد الإلكتروني من Google.',
            ]);
        }

        $name = trim((string) ($googleUser->getName() ?? $googleUser->getNickname() ?? ''));
        if ($name === '') {
            $name = strstr($email, '@', true) ?: $email;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                // Random password: they'll never use it, since they log in via Google.
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]
        );

        // Google already verified this email; backfill legacy rows.
        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        return redirect()->intended('/');
    }
}
