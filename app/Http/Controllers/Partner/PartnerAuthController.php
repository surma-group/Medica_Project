<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerAuthController extends Controller
{


    /**
     * Handle Partner Login
     */
    public function login(Request $request)
    {
        // Validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        // Attempt login
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            // Optional: check role (recommended)
            if (isset($user->role) && $user->role !== 'partner') {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'You are not allowed to login as partner.'
                ]);
            }

            return redirect()->route('partner.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.'
        ])->withInput();
    }

    /**
     * Partner Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}