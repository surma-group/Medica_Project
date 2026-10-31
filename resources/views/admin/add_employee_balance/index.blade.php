
@extends('layouts.admin')

@push('styles')
<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #ced4da;
        border-radius: 5px;
    }

    .select2-container--default .select2-selection--single
    .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
    }

    .select2-container--default .select2-selection--single
    .select2-selection__arrow {
        height: 40px;
    }

    .balance-card {
        border-left: 4px solid #6777ef;
    }

    .history-table th,
    .history-table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .balance-amount {
        font-size: 21px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<section class="section">

    <div class="row">
        <div class="col-12">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}

                    <button type="button" class="close" data-dismiss="alert"
                        aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}

                    <button type="button" class="close" data-dismiss="alert"
                        aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Please check the following errors:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Add Employee Balance Form --}}
            <div class="card">
                <div class="card-header">
                    <h4>Add Employee Wallet Balance</h4>
                </div>

                <div class="card-body">

                    {{-- Cash and Bank Balances --}}
                    <div class="row mb-4">

                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="alert alert-light border balance-card mb-0">
                                <strong>Cash Balance</strong>

                                <div class="balance-amount mt-2">
                                    ৳ {{ number_format(
                                        $cashBalance
                                            ? $cashBalance->entries()->sum('debit')
                                                - $cashBalance->entries()->sum('credit')
                                            : 0,
                                        2
                                    ) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="alert alert-light border balance-card mb-0">
                                <strong>Bank Balance</strong>

                                <div class="balance-amount mt-2">
                                    ৳ {{ number_format(
                                        $bankBalance
                                            ? $bankBalance->entries()->sum('debit')
                                                - $bankBalance->entries()->sum('credit')
                                            : 0,
                                        2
                                    ) }}
                                </div>
                            </div>
                        </div>

                    </div>

                    <form
                        action="{{ route('add_employee_balance.store') }}"
                        method="POST"
                    >
                        @csrf

                        <div class="row">

                            {{-- Employee --}}
                            <div class="col-md-6 mb-3">
                                <label for="employee_id">
                                    Employee <span class="text-danger">*</span>
                                </label>

                                <select
                                    name="employee_id"
                                    id="employee_id"
                                    class="form-control select2"
                                    data-placeholder="Search employee name or account number"
                                    required
                                >
                                    <option value=""></option>

                                    @foreach($employees as $employee)
                                        <option
                                            value="{{ $employee->id }}"
                                            {{ (string) old('employee_id') === (string) $employee->id ? 'selected' : '' }}
                                        >
                                            {{ $employee->full_name }}
                                            ({{ $employee->account_number ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Source Account --}}
                            <div class="col-md-6 mb-3">
                                <label for="source_account">
                                    Source Account <span class="text-danger">*</span>
                                </label>

                                <select
                                    name="source_account"
                                    id="source_account"
                                    class="form-control select2"
                                    data-placeholder="Select Cash Desk or Bank"
                                    required
                                >
                                    <option value=""></option>

                                    <option
                                        value="{{ L_CASH_DESK }}"
                                        {{ (string) old('source_account') === (string) L_CASH_DESK ? 'selected' : '' }}
                                    >
                                        Cash Desk
                                    </option>

                                    <option
                                        value="{{ L_BANK_MAIN }}"
                                        {{ (string) old('source_account') === (string) L_BANK_MAIN ? 'selected' : '' }}
                                    >
                                        Bank
                                    </option>
                                </select>
                            </div>

                            {{-- Amount --}}
                            <div class="col-md-6 mb-3">
                                <label for="amount">
                                    Amount <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="amount"
                                    id="amount"
                                    class="form-control"
                                    value="{{ old('amount') }}"
                                    min="0.01"
                                    max="999999999"
                                    step="0.01"
                                    placeholder="Enter amount"
                                    required
                                >
                            </div>

                            {{-- Note --}}
                            <div class="col-md-6 mb-3">
                                <label for="note">Note</label>

                                <input
                                    type="text"
                                    name="note"
                                    id="note"
                                    class="form-control"
                                    value="{{ old('note') }}"
                                    maxlength="255"
                                    placeholder="Enter note (optional)"
                                >
                            </div>

                            <div class="col-12 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-wallet"></i>
                                    Add Wallet Balance
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

            {{-- Employee Balance History --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Employee Wallet History</h4>

                    <span class="badge badge-primary">
                        {{ $history->total() }} Records
                    </span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md history-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee Name</th>
                                    <th>Designation</th>
                                    <th>Account Number</th>
                                    <th>Amount</th>
                                    <th>Source Account</th>
                                    <th>Voucher No</th>
                                    <th>Note</th>
                                    <th>Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($history as $voucher)
                                    @php
                                        $employee = $historyEmployees->get(
                                            $voucher->reference_id
                                        );

                                        // Employee wallet is debited when balance is added.
                                        $employeeEntry = $voucher->entries
                                            ->first(function ($entry) use ($employee) {
                                                return $employee
                                                    && $entry->ledger_id == $employee->ledger_id
                                                    && (float) $entry->debit > 0;
                                            });

                                        // Cash/Bank is credited when money is transferred out.
                                        $sourceEntry = $voucher->entries
                                            ->first(function ($entry) use ($employee) {
                                                return (float) $entry->credit > 0
                                                    && (!$employee
                                                        || $entry->ledger_id != $employee->ledger_id);
                                            });

                                        $amount = $employeeEntry
                                            ? (float) $employeeEntry->debit
                                            : ($sourceEntry
                                                ? (float) $sourceEntry->credit
                                                : 0);

                                        $sourceName = $sourceEntry?->ledger?->title
                                            ?? 'N/A';

                                        $note = $employeeEntry?->note
                                            ?? $sourceEntry?->note
                                            ?? '-';
                                    @endphp

                                    <tr>
                                        <td>
                                            {{ $history->firstItem() + $loop->index }}
                                        </td>

                                        <td>
                                            {{ $employee?->full_name ?? 'N/A' }}
                                        </td>

                                        <td>
                                            {{ $employee?->designation?->name ?? 'N/A' }}
                                        </td>

                                        <td>
                                            {{ $employee?->account_number ?? 'N/A' }}
                                        </td>

                                        <td>
                                            <strong>
                                                ৳ {{ number_format($amount, 2) }}
                                            </strong>
                                        </td>

                                        <td>{{ $sourceName }}</td>

                                        <td>{{ $voucher->code }}</td>

                                        <td>{{ $note }}</td>

                                        <td>
                                            {{ $voucher->created_at
                                                ? $voucher->created_at->format('d M Y, h:i A')
                                                : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            No employee wallet records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-right">
                    {{ $history->links() }}
                </div>
            </div>

        </div>
    </div>

</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#employee_id').select2({
            width: '100%',
            placeholder: 'Search employee name or account number',
            allowClear: true
        });

        $('#source_account').select2({
            width: '100%',
            placeholder: 'Select Cash Desk or Bank',
            allowClear: true,
            minimumResultsForSearch: 0
        });
    });
</script>
@endpush
