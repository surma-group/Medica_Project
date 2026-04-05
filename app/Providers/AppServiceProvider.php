<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Voucher Type
        if (!defined('VT_CASH_IN')) {
            define('VT_CASH_IN', 1);
        }

        if (!defined('VT_SALARY_APPROVE')) {
            define('VT_SALARY_APPROVE', 2);
        }

        if (!defined('VT_WITHDRAW_APPROVE')) {
            define('VT_WITHDRAW_APPROVE', 3);
        }

        // Ledger IDs
        if (!defined('L_CASH_DESK')) {
            define('L_CASH_DESK', 1);
        }
        if (!defined('L_BANK_MAIN')) {
            define('L_BANK_MAIN', 2);
        }
        if (!defined('L_SALARY_EXPENSE')) {
            define('L_SALARY_EXPENSE', 7);
        }
        if (!defined('L_OWNER_CAPITAL')) {
            define('L_OWNER_CAPITAL', 8);
        }
        if (!defined('L_PROVIDENT_FUND_PAYABLE')) {
            define('L_PROVIDENT_FUND_PAYABLE', 9);
        }

        //WITHDRAW TYPE
        // WITHDRAW TYPES
        // WITHDRAW TYPES
        if (!defined('WITHDRAW_TYPE_CASH')) {
            define('WITHDRAW_TYPE_CASH', 1);
        }

        if (!defined('WITHDRAW_TYPE_MOBILE_BANKING')) {
            define('WITHDRAW_TYPE_MOBILE_BANKING', 2);
        }

        if (!defined('WITHDRAW_TYPE_BANK')) {
            define('WITHDRAW_TYPE_BANK', 3);
        }

        //MOBILE BANKING TYPE
        if (!defined('MOBILE_BANKING_TYPE_BKASH')) {
            define('MOBILE_BANKING_TYPE_BKASH', 1);
        }

        if (!defined('MOBILE_BANKING_TYPE_NAGAD')) {
            define('MOBILE_BANKING_TYPE_NAGAD', 2);
        }

        if (!defined('MOBILE_BANKING_TYPE_ROCKET')) {
            define('MOBILE_BANKING_TYPE_ROCKET', 3);
        }

        //Salary Components Provident Fund

        if (!defined('SALARY_COMPONENT_PROVIDENT_FUND')) {
            define('SALARY_COMPONENT_PROVIDENT_FUND', 4);
        }

        View::composer('partials.employee.navbar', function ($view) {
            $balance = 0;

            $user = Auth::guard('web')->user(); // logged-in User

            if ($user) {
                $employee = Employee::with('ledger.entries')->where('user_id', $user->id)->first();

                if ($employee && $employee->ledger) {
                    $balance = $employee->ledger->currentEmployeeBalance();
                }
            }

            $view->with('employeeBalance', $balance);
        });


        View::composer('partials.admin.navbar', function ($view) {

            $cashBalance = DB::table('ledger_entry as le')
                ->join('ledger as l', 'l.id', '=', 'le.ledger_id')
                ->where('l.type', 1)          // ✅ Only cash/bank
                ->whereIn('l.id', [L_CASH_DESK, L_BANK_MAIN])    // ✅ Only ledger IDs 1 and 2
                ->selectRaw('COALESCE(SUM(le.debit) - SUM(le.credit), 0) as balance')
                ->value('balance');

            $view->with('systemBalance', $cashBalance);
        });

        Paginator::useBootstrap();
    }
    
}
