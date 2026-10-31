<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\Ledger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('frontend.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'type'        => 'required|integer',

            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',

            'email'       => 'required|email|unique:users,email',
            'phone'       => 'required|string|max:20',

            'address'     => 'required|string|max:255',

            'password'    => 'required|min:6|confirmed',

            'shop_name'   => 'nullable|string|max:255',
            'trade_license' => 'nullable|string|max:255',

            'image'       => 'nullable|image|max:2048',
            'nid_file'    => 'nullable|file|max:2048',
            'trade_license_file' => 'nullable|file|max:2048',
        ]);

        DB::beginTransaction();

        try {

            // =========================
            // CREATE USER
            // =========================
            $user = User::create([
                'name'     => $request->first_name . ' ' . $request->last_name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => 0,
                'type'     => $request->type,
            ]);

            // =========================
            // WALLET LEDGER (COMMON FOR ALL)
            // =========================
            $walletLedger = Ledger::create([
                'chart_account_id' => CHART_WALLET_LIABILITY_ACCOUNT,
                'title' => "Wallet - " . $user->name,
                'code'  => "WAL-" . $user->id,
                'type'  => 2, // Liability (IMPORTANT)
                'for_income' => 0,
                'for_expense' => 1,
                'reference_type' => 'user',
                'reference_id' => $user->id,
            ]);

            // =========================
            // CUSTOMER PROFILE
            // =========================
            if ($request->type == USER_TYPE_CUSTOMER) {

                Customer::create([
                    'user_id'    => $user->id,
                    'name'       => $user->name,
                    'first_name' => $request->first_name,
                    'last_name'  => $request->last_name,
                    'email'      => $request->email,
                    'phone'      => $request->phone,
                    'address'    => $request->address,
                    'status'     => 1,
                    'ledger_id'  => $walletLedger->id,
                ]);
            }

            // =========================
            // PARTNER (DEALER / AGENT)
            // =========================
            if (
                $request->type == USER_TYPE_DEALER ||
                $request->type == USER_TYPE_AGENT
            ) {

                $upload = function ($file, $path) {
                    return $file ? $file->store($path, 'public') : null;
                };

                Partner::create([
                    'user_id'   => $user->id,
                    'name'      => $user->name,
                    'shop_name' => $request->shop_name,
                    'type'      => $request->type,
                    'phone'     => $request->phone,
                    'email'     => $request->email,
                    'address'   => $request->address,
                    'trade_license' => $request->trade_license,

                    'image'     => $upload($request->file('image'), 'partners'),
                    'nid_file'  => $upload($request->file('nid_file'), 'partners'),
                    'trade_license_file' => $upload($request->file('trade_license_file'), 'partners'),

                    'status'    => 1,
                    'ledger_id'  => $walletLedger->id,
                ]);
            }

            DB::commit();

            Auth::login($user);

            if ($user->type == USER_TYPE_CUSTOMER) {
                return redirect()->route('profile')
                    ->with('success', 'Registration successful!');
            }

            if ($user->type == USER_TYPE_DEALER || $user->type == USER_TYPE_AGENT) {
                return redirect()->route('partner.dashboard')
                    ->with('success', 'Registration successful!');
            }

            return redirect()->route('profile')
                ->with('success', 'Registration successful!');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Registration failed! ' . $e->getMessage());
        }
    }
}
