<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show login form
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

        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {

            $request->session()->regenerate();

            $user = Auth::guard('web')->user();

            // Customer
            if ($user->type == USER_TYPE_CUSTOMER) {
                return redirect()->route('profile');
            }

            // Dealer / Agent
            if (
                $user->type == USER_TYPE_DEALER ||
                $user->type == USER_TYPE_AGENT
            ) {
                return redirect()->route('partner.dashboard');
            }

            // Employee
            if ($user->type == USER_TYPE_EMPLOYEE) {
                return redirect()->route('employee.dashboard');
            }

            // Other account types are not allowed
            Auth::guard('web')->logout();

            return back()->withErrors([
                'email' => 'Account type not allowed.'
            ])->withInput();
        }

        return back()
            ->withErrors([
                'email' => 'Invalid credentials.'
            ])
            ->withInput();
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
