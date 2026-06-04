<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (auth()->check()) {
            return redirect()->route('adminhu.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $user = auth()->user();
            $adminRoles = ['super_admin', 'admin', 'sales', 'content', 'products'];

            if (!$user->hasAnyRole($adminRoles)) {
                auth()->logout();
                return back()->withErrors(['email' => 'You do not have admin access.']);
            }

            $request->session()->regenerate();
            return redirect()->route('adminhu.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('adminhu.login');
    }

    public function profile()
    {
        return view('admin.users.profile');
    }
}
