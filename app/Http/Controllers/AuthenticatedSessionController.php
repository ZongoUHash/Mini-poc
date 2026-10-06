<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Ces identifiants ne correspondent à aucun compte de démonstration.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $defaultDestination = $request->user()->role === 'hr'
            ? route('hr.dashboard')
            : route('employee.portal');

        $intendedUrl = $request->session()->pull('url.intended');
        if (! is_string($intendedUrl) || ! $this->canAccessIntendedUrl($request, $intendedUrl)) {
            return redirect()->to($defaultDestination);
        }

        return redirect()->to($intendedUrl);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }

    private function canAccessIntendedUrl(Request $request, string $intendedUrl): bool
    {
        $host = parse_url($intendedUrl, PHP_URL_HOST);
        $path = parse_url($intendedUrl, PHP_URL_PATH) ?? '/';

        if ($host !== null && $host !== $request->getHost()) {
            return false;
        }

        if ($request->user()->role === 'hr') {
            return Str::startsWith($path, '/rh');
        }

        return Str::startsWith($path, ['/salarié', '/pointer/']);
    }
}
