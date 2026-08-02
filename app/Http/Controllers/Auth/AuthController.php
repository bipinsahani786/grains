<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $brandName = \App\Models\System\PlatformSetting::where('key', 'brand_name')->value('value') ?? 'Platform Name';
        $logoPath = \App\Models\System\PlatformSetting::where('key', 'logo_path')->value('value');
        
        return view('auth.login', compact('brandName', 'logoPath'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Super Admin check (role is super_admin)
            if ($user->role === 'super_admin') {
                return redirect()->route('superadmin.dashboard');
            }

            // Normal Admin / Staff check
            return redirect()->route('business.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
