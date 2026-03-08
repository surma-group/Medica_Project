@extends('layouts.employee')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>
                        Salary Details — {{ $salary->salary_month->format('F Y') }}
                    </h4>

                    {{-- Back Button --}}
                    <a href="{{ route('employee.salary') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                {{-- Body --}}
                <div class="card-body">

                    {{-- Summary --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <strong>Basic Salary:</strong><br>
                            {{ number_format($salary->basic_salary, 2) }}
                        </div>

                        <div class="col-md-3 text-success">
                            <strong>Total Earning:</strong><br>
                            {{ number_format($salary->total_earning, 2) }}
                        </div>

                        <div class="col-md-3 text-danger">
                            <strong>Total Deduction:</strong><br>
                            {{ number_format($salary->total_deduction, 2) }}
                        </div>

                        <div class="col-md-3 fw-bold">
                            <strong>Payable Salary:</strong><br>
                            {{ number_format($salary->net_salary, 2) }}
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge {{ $salary->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($salary->status) }}
                        </span>
                    </div>

                    <hr>

                    {{-- Salary Breakdown --}}
                    <h5 class="mb-3">Salary Breakdown</h5>

                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Component</th>
                                    <th>Type</th>
                                    <th>Amount Type</th>
                                    <th>Amount</th>
                                    <th>Calculated Amount</th>
                                </tr>
                            </thead>
                            @php
                            $earnings = $salary->details->where('component_type', 'earning');
                            $deductions = $salary->details->where('component_type', 'deduction');

                            $totalEarnings = $earnings->sum('calculated_amount');
                            $totalDeductions = $deductions->sum('calculated_amount');
                            @endphp

                            <tbody>
                                {{-- Earnings --}}
                                @forelse($earnings as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ optional($detail->component)->title ?? 'N/A' }}</td>
                                    <td>Earning</td>
                                    <td>
                                        {{ $detail->amount_type == 1 ? 'Flat' : ($detail->amount_type == 2 ? 'Percentage' : 'N/A') }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($detail->amount, 2) }}{{ $detail->amount_type == 2 ? '%' : '' }}
                                    </td>
                                    <td class="text-end text-success">
                                        {{ number_format($detail->calculated_amount, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No earnings found.</td>
                                </tr>
                                @endforelse

                                {{-- Total Earnings --}}
                                <tr class="fw-bold bg-light">
                                    <td colspan="5" class="text-end">Total Earnings</td>
                                    <td class="text-end text-success">
                                        {{ number_format($totalEarnings, 2) }}
                                    </td>
                                </tr>

                                {{-- Deductions --}}
                                @forelse($deductions as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ optional($detail->component)->title ?? 'N/A' }}</td>
                                    <td>Deduction</td>
                                    <td>
                                        {{ $detail->amount_type == 1 ? 'Flat' : ($detail->amount_type == 2 ? 'Percentage' : 'N/A') }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($detail->amount, 2) }}{{ $detail->amount_type == 2 ? '%' : '' }}
                                    </td>
                                    <td class="text-end text-danger">
                                        {{ number_format($detail->calculated_amount, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No deductions found.</td>
                                </tr>
                                @endforelse

                                {{-- Total Deductions --}}
                                <tr class="fw-bold bg-light">
                                    <td colspan="5" class="text-end">Total Deductions</td>
                                    <td class="text-end text-danger">
                                        {{ number_format($totalDeductions, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection