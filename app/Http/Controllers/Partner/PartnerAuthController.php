<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PartnerAuthController extends Controller
{
    public function showRegisterForm(Request $request)
    {
        // If already logged in as hotel_owner, redirect to dashboard
        if (Auth::guard('partner')->check()) {
            if (Auth::guard('partner')->user()->isHotelOwner()) {
                return redirect()->route('partner.dashboard');
            }
            
            // User is logged in but not as partner - auto logout and show register
            $previousRole = Auth::guard('partner')->user()->role === 'admin' ? 'Admin' : ucfirst(Auth::guard('partner')->user()->role ?? 'customer');
            Auth::guard('partner')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return view('partner.auth.register')->with('loggedOut', true)
                ->with('previousRole', $previousRole);
        }
        
        return view('partner.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = 'hotel_owner'; // Explicitly set role (not from fillable)
            $user->save();

            Auth::guard('partner')->login($user);
            
            // Store authenticated role in session for security validation
            session(['authenticated_role' => 'hotel_owner']);

            return redirect()->route('partner.dashboard');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    public function showLoginForm(Request $request)
    {
        // If already logged in as hotel_owner, redirect to dashboard
        if (Auth::guard('partner')->check()) {
            if (Auth::guard('partner')->user()->isHotelOwner()) {
                return redirect()->route('partner.dashboard');
            }
            
            // User is logged in but not as partner - auto logout and show login
            $previousRole = Auth::guard('partner')->user()->role === 'admin' ? 'Admin' : ucfirst(Auth::guard('partner')->user()->role ?? 'customer');
            Auth::guard('partner')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return view('partner.auth.login')->with('loggedOut', true)
                ->with('previousRole', $previousRole);
        }
        
        return view('partner.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('partner')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Partner-only guard - double check role
            if (Auth::guard('partner')->user()->role !== 'hotel_owner') {
                Auth::guard('partner')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'This portal is for partners only. If you are an admin, please use the admin login.',
                ]);
            }


            return redirect()->intended(route('partner.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('partner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('partner.index');
    }
}
