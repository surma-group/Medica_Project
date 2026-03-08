<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\VoucherEntry;
use App\Models\LedgerEntry;

class AccountingService
{
    public function addTransaction(
        float $amount,
        int $debitLedgerId,
        int $creditLedgerId,
        int $voucherType,
        ?int $referenceId = null,
        ?string $note = null,
        ?int $createdBy = null
    ): VoucherEntry {
        return DB::transaction(function () use (
            $amount,
            $debitLedgerId,
            $creditLedgerId,
            $voucherType,
            $referenceId,
            $note,
            $createdBy
        ) {

            $voucher = VoucherEntry::create([
                'code' => 'VCH' . now()->format('YmdHis'),
                'type' => $voucherType,
                'reference_id' => $referenceId,
                'created_by' => $createdBy ?? auth()->id() ?? 1,
            ]);

            LedgerEntry::create([
                'code' => 'LE' . now()->format('YmdHis') . 'D',
                'voucher_id' => $voucher->id,
                'ledger_id' => $debitLedgerId,
                'debit' => $amount,
                'credit' => 0,
                'note' => $note,
            ]);

            LedgerEntry::create([
                'code' => 'LE' . now()->format('YmdHis') . 'C',
                'voucher_id' => $voucher->id,
                'ledger_id' => $creditLedgerId,
                'debit' => 0,
                'credit' => $amount,
                'note' => $note,
            ]);

            return $voucher;
        });
    }
}
