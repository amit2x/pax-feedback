<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(private AdminOtpService $otp) {}

    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('admin_login.pending_user_id')) {
            return redirect()->route('admin.login');
        }

        return view('admin.auth.two-factor');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId = $request->session()->get('admin_login.pending_user_id');
        $expiresAt = $request->session()->get('admin_login.expires_at');

        if (! $userId || ! $expiresAt || now()->timestamp > $expiresAt) {
            $request->session()->forget(['admin_login.pending_user_id', 'admin_login.expires_at']);

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $user = User::find($userId);
        if (! $user) {
            return redirect()->route('admin.login');
        }

        if (! $this->otp->verify($user, $request->input('code'))) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        $request->session()->forget(['admin_login.pending_user_id', 'admin_login.expires_at']);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /* ublic function resend(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('admin_login.pending_user_id');
        if (! $userId) {
            return redirect()->route('admin.login');
        }

        $user = User::find($userId);
        if ($user) {
            $this->otp->send($user);
        }

        return back()->with('status', 'A new code has been sent to your email.');
    } */

    public function resend(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('admin_login.pending_user_id');
        if (! $userId) {
            return redirect()->route('admin.login');
        }

        $user = User::find($userId);
        if (! $user) {
            return redirect()->route('admin.login');
        }

        $sent = $this->otp->resend($user);

        if (! $sent) {
            return back()->withErrors([
                'code' => 'Please wait a moment before requesting a new code.',
            ]);
        }

        return back()->with('status', 'A new code has been sent to your email.');
    }
}
