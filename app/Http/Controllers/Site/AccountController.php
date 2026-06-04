<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function loginForm()
    {
        if (Auth::check() && !Auth::user()->isAdmin()) {
            return redirect()->route('account.dashboard');
        }
        return view('account.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Block admin users from using the customer portal
            if (Auth::user()->isAdmin()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Please use the admin panel to log in.']);
            }

            return redirect()->intended(route('account.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
    }

    public function registerForm()
    {
        if (Auth::check() && !Auth::user()->isAdmin()) {
            return redirect()->route('account.dashboard');
        }
        return view('account.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:200', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'company'  => ['nullable', 'string', 'max:200'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'company'  => $data['company'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('customer');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account.dashboard')->with('success', 'Welcome to pcfy.in! Your account is ready.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function dashboard()
    {
        $user = Auth::user();

        $quotes = $user->quotes()->with('items')->get();

        $wishlist = $user->wishlistProducts()
            ->with(['brand', 'category'])
            ->get();

        return view('account.dashboard', compact('user', 'quotes', 'wishlist'));
    }
}
