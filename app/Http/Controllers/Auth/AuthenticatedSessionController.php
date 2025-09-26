<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Redirect to role-specific dashboard if available
        return match ($user->role ?? 'student') {
            'student' => redirect()->intended(route('dashboard.student', absolute: false)),
            'instructor' => redirect()->intended(route('dashboard.instructor', absolute: false)),
            'admin' => redirect()->intended(route('dashboard.admin', absolute: false)),
            'registrar' => redirect()->intended(route('dashboard.registrar', absolute: false)),
            default => redirect()->intended(route('dashboard', absolute: false)),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
