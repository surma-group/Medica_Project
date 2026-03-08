<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show login form (optional since we use closure route)
    public function showLoginForm()
    {
        return view('frontend.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Only allow employees (is_admin = 0)
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'is_admin' => 0,
        ];

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('employee.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or you are not an employee.',
        ])->withInput();
    }



    // Logout
    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/login');
    }
}
