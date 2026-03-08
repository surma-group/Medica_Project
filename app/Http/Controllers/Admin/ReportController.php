<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the Cash Flow report.
     */
    public function cashFlow()
    {
        // Get all cash/bank transactions (ledger type = 1)
        $transactions = DB::table('ledger_entry as le')
            ->join('ledger as l', 'l.id', '=', 'le.ledger_id')
            ->where('l.type', 1) // only cash/bank ledgers
            ->whereIn('l.id', [L_CASH_DESK, L_BANK_MAIN]) 
            ->select(
                'le.id',
                'l.title as ledger_name',
                'le.note',
                'le.debit',
                'le.credit',
                'le.created_at'
            )
            ->orderBy('le.created_at', 'asc')
            ->get();

        // Calculate total in/out and balance
        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');
        $balance = $totalDebit - $totalCredit;

        return view('admin.reports.cash_flow', [
            'transactions' => $transactions,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'balance' => $balance,
        ]);
    }
}