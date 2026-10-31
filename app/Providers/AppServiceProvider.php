<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Employee;
use App\Models\Partner;

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
        Relation::morphMap([
            '5' => Employee::class,
            '2' => Partner::class,
            '3' => Partner::class,
        ]);
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

        if (!defined('VT_ADD_MONEY_APPROVE')) {
            define('VT_ADD_MONEY_APPROVE', 4);
        }

        if (!defined('VT_PURCHASE')) {
            define('VT_PURCHASE', 5);
        }

        if (!defined('VT_SALE')) {
            define('VT_SALE', 6);
        }

        if (!defined('VT_EMPLOYEE_BALANCE_ADD')) {
            define('VT_EMPLOYEE_BALANCE_ADD', 7);
        }

        if (!defined('VT_EMPLOYEE_TRANSFER')) {
            define('VT_EMPLOYEE_TRANSFER', 8);
        }
        
        if (!defined('VT_PARTNER_TRANSFER')) {
            define('VT_PARTNER_TRANSFER', 9);
        }
        

        //Chart of Accounts
        if (!defined('CHART_WALLET_LIABILITY_ACCOUNT')) {
            define('CHART_WALLET_LIABILITY_ACCOUNT', 11);
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

        if (!defined('L_INVENTORY')) {
            define('L_INVENTORY', 10);
        }

        //WITHDRAW TYPE
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

        //All default
        if (!defined('DEFAULT_UNIT')) {
            define('DEFAULT_UNIT', 1);
        }


        //STAKEHOLDER TYPE
        if (!defined('USER_TYPE_ADMIN')) {
            define('USER_TYPE_ADMIN', 0);
        }
        
        if (!defined('USER_TYPE_CUSTOMER')) {
            define('USER_TYPE_CUSTOMER', 1);
        }

        if (!defined('USER_TYPE_DEALER')) {
            define('USER_TYPE_DEALER', 2);
        }

        if (!defined('USER_TYPE_AGENT')) {
            define('USER_TYPE_AGENT', 3);
        }

        if (!defined('USER_TYPE_SUPPLIER')) {
            define('USER_TYPE_SUPPLIER', 4);
        }

        if (!defined('USER_TYPE_EMPLOYEE')) {
            define('USER_TYPE_EMPLOYEE', 5);
        }

        if (!defined('ORDER_STATUS_PENDING')) {
            define('ORDER_STATUS_PENDING', 0);
        }
        if (!defined('ORDER_STATUS_COMPLETED')) {
            define('ORDER_STATUS_COMPLETED', 1);
        }
        if (!defined('ORDER_STATUS_ACCEPTED')) {
            define('ORDER_STATUS_ACCEPTED', 2);
        }
        if (!defined('ORDER_STATUS_ON_THE_WAY')) {
            define('ORDER_STATUS_ON_THE_WAY', 3);
        }
        if (!defined('ORDER_STATUS_REJECTED')) {
            define('ORDER_STATUS_REJECTED', 4);
        }

        if (!defined('COMMISSION_TYPE_FLAT')) {
            define('COMMISSION_TYPE_FLAT', 1);
        }
        if (!defined('COMMISSION_TYPE_PERCENTAGE')) {
            define('COMMISSION_TYPE_PERCENTAGE', 2);
        }

        if (!defined('TYPE_FLAT')) {
            define('TYPE_FLAT', 1);
        }
        if (!defined('TYPE_PERCENTAGE')) {
            define('TYPE_PERCENTAGE', 2);
        }

        if (!defined('PERIOD_TYPE_DAILY')) {
            define('PERIOD_TYPE_DAILY', 1);
        }
        if (!defined('PERIOD_TYPE_WEEKLY')) {
            define('PERIOD_TYPE_WEEKLY', 2);
        }
        if (!defined('PERIOD_TYPE_MONTHLY')) {
            define('PERIOD_TYPE_MONTHLY', 3);
        }
        if (!defined('PERIOD_TYPE_YEARLY')) {
            define('PERIOD_TYPE_YEARLY', 4);
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

        View::composer('partials.partner.navbar', function ($view) {

            $balance = 0;

            $user = Auth::guard('web')->user();

            if ($user) {

                $partner = Partner::with('ledger.entries')
                    ->where('user_id', $user->id)
                    ->first();

                if ($partner && $partner->ledger) {

                    $balance =
                        $partner->ledger->entries()->sum('credit')
                        -
                        $partner->ledger->entries()->sum('debit');
                }
            }

            $view->with('partnerBalance', $balance);
        });

        Paginator::useBootstrap();
    }
}
