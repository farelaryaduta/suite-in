<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // If already logged in as admin, redirect to dashboard
        if (Auth::guard('admin')->check()) {
            if (Auth::guard('admin')->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            
            // User is logged in but not as admin - auto logout and show login
            $previousRole = Auth::guard('admin')->user()->role === 'hotel_owner' ? 'Partner' : ucfirst(Auth::guard('admin')->user()->role ?? 'customer');
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return view('admin.auth.login')->with('loggedOut', true)
                ->with('previousRole', $previousRole);
        }
        
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Admin-only guard - double check role
            if (Auth::guard('admin')->user()->role !== 'admin') {
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'This portal is for administrators only.',
                ]);
            }


            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
