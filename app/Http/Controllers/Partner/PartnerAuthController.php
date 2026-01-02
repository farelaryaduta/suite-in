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
        if (Auth::check()) {
            if (Auth::user()->isHotelOwner()) {
                return redirect()->route('partner.dashboard');
            }
            
            // User is logged in but not as partner - auto logout and show register
            $previousRole = Auth::user()->role === 'admin' ? 'Admin' : ucfirst(Auth::user()->role ?? 'customer');
            Auth::logout();
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

            Auth::login($user);

            return redirect()->route('partner.dashboard');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    public function showLoginForm(Request $request)
    {
        // If already logged in as hotel_owner, redirect to dashboard
        if (Auth::check()) {
            if (Auth::user()->isHotelOwner()) {
                return redirect()->route('partner.dashboard');
            }
            
            // User is logged in but not as partner - auto logout and show login
            $previousRole = Auth::user()->role === 'admin' ? 'Admin' : ucfirst(Auth::user()->role ?? 'customer');
            Auth::logout();
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

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Partner portal is ONLY for hotel_owner role - no admin access
            if (Auth::user()->role !== 'hotel_owner') {
                Auth::logout();
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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('partner.index');
    }
}
