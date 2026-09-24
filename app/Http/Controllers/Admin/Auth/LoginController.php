<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidMathCaptcha;
use App\Services\AdminOtpService;
use App\Services\MathCaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private AdminOtpService $otp,
        private MathCaptchaService $captcha,
    ) {}

    public function show(): View
    {
        $captcha = $this->captcha->generate();

        return view('admin.auth.login', [
            'captchaQuestion' => $captcha['question'],
        ]);
    }

    /* public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'string', new ValidMathCaptcha],
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        if ($user->two_factor_email_enabled) {
            $this->otp->send($user);

            $request->session()->put('admin_login.pending_user_id', $user->id);
            $request->session()->put('admin_login.expires_at', now()->addMinutes(10)->timestamp);

            return redirect()->route('admin.2fa.show');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    } */

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'string', new ValidMathCaptcha],
        ]);

        $user = User::where('email', $request->input('email'))->first();

        // Generic error — never reveal whether the user exists
        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        // ---------- OTP BYPASS (dev/staging only) ----------
        if ($this->otp->shouldBypass()) {
            Log::warning('[ADMIN OTP] Bypass active — logging in without 2FA', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        // ---------- Normal OTP flow ----------
        if ($user->two_factor_email_enabled) {
            $this->otp->send($user);

            $request->session()->put('admin_login.pending_user_id', $user->id);
            $request->session()->put('admin_login.expires_at', now()->addMinutes(10)->timestamp);

            return redirect()->route('admin.2fa.show');
        }

        // 2FA disabled at user level
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
