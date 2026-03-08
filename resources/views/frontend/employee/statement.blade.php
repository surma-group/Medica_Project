@extends('layouts.employee')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Account Statement</h4>
                    <div class="d-flex align-items-center gap-3">
                        <strong>
                            Balance:
                            <span class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($balance, 2) }}
                            </span>
                        </strong>
                        <a href="{{ route('employee.wallet') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $runningBalance = 0;
                                $totalCredit = 0;
                                $totalDebit = 0;
                                @endphp

                                @forelse($entries as $entry)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $entry->created_at->format('d M Y') }}</td>
                                    <td>{{ $entry->note ?? 'N/A' }}</td>

                                    <td>
                                        <span class="text-success">
                                            {{ number_format($entry->credit ?? 0, 2) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="text-danger">
                                            {{ number_format($entry->debit ?? 0, 2) }}
                                        </span>
                                    </td>

                                    @php
                                    $credit = $entry->credit ?? 0;
                                    $debit = $entry->debit ?? 0;

                                    $totalCredit += $credit;
                                    $totalDebit += $debit;

                                    $runningBalance += $credit - $debit;
                                    @endphp

                                    <td>
                                        {{ number_format($runningBalance, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        No statement found.
                                    </td>
                                </tr>
                                @endforelse

                                @if(count($entries))
                                <tr class="fw-bold table-light">
                                    <td colspan="3" class="text-end">Total</td>

                                    <td class="text-success">
                                        {{ number_format($totalCredit, 2) }}
                                    </td>

                                    <td class="text-danger">
                                        {{ number_format($totalDebit, 2) }}
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