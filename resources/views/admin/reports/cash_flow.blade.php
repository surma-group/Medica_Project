@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Cash Flow Report</h4>
                    <div class="d-flex align-items-center gap-3">
                        <strong>
                            Balance:
                            <span class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($balance, 2) }}
                            </span>
                        </strong>
                        
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Ledger</th>
                                    <th>Note</th>
                                    <th>Debit (Out)</th>
                                    <th>Credit (In)</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $runningBalance = 0;
                                    $totalCredit = 0;
                                    $totalDebit = 0;
                                @endphp

                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y') }}</td>
                                    <td>{{ $transaction->ledger_name }}</td>
                                    <td>{{ $transaction->note }}</td>
                                    <td>
                                        <span class="text-success">
                                            {{ number_format($transaction->debit ?? 0, 2) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="text-danger">
                                            {{ number_format($transaction->credit ?? 0, 2) }}
                                        </span>
                                    </td>

                                    @php
                                        $credit = $transaction->credit ?? 0;
                                        $debit = $transaction->debit ?? 0;

                                        $totalCredit += $credit;
                                        $totalDebit += $debit;

                                        $runningBalance += $debit-$credit;
                                    @endphp

                                    <td>
                                        {{ number_format($runningBalance, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        No transactions found.
                                    </td>
                                </tr>
                                @endforelse

                                @if(count($transactions))
                                <tr class="fw-bold table-light">
                                    <td colspan="4" class="text-end">Total</td>

                                    <td class="text-success">
                                        {{ number_format($totalDebit, 2) }}
                                    </td>

                                    <td class="text-danger">
                                        {{ number_format($totalCredit, 2) }}
                                    </td>

                                    <td>
                                        {{ number_format($runningBalance, 2) }}
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection