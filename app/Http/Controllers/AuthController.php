<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        // Debug logging
        \Log::info('Login request received', [
            'method' => $request->method(),
            'url' => $request->url(),
            'session_id' => $request->session()->getId(),
            'csrf_token' => $request->session()->token(),
            'request_token' => $request->input('_token'),
            'has_session' => $request->hasSession(),
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Debug logging
        \Log::info('Login attempt', [
            'email' => $credentials['email'],
            'credentials' => $credentials
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            \Log::info('Login successful for: ' . $credentials['email']);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        \Log::info('Login failed for: ' . $credentials['email']);
        return back()->withErrors([
            'email' => 'A megadott adatok nem egyeznek nyilvántartásunkkal.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Generate membership number
        $membershipNumber = 'TAG' . str_pad(User::count() + 1, 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'membership_number' => $membershipNumber,
            'membership_type' => 'basic',
            'is_active' => true,
            'is_admin' => false,
            'is_librarian' => false,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Sikeres regisztráció! Üdvözöljük a BiblioTech rendszerben!');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Sikeres kijelentkezés!');
    }

    /**
     * Show the user's profile.
     */
    public function profile()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    /**
     * Update the user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return back()->with('success', 'Profil sikeresen frissítve!');
    }
}
