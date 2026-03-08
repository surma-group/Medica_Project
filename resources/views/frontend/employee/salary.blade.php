@extends('layouts.employee')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Salarys</h4>
                </div>

                {{-- Body --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Salary Month</th>
                                    <th>Basic Salary</th>
                                    <th>Total Earning</th>
                                    <th>Total Deduction</th>
                                    <th>Payable Salary</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalBasic = 0;
                                    $totalEarning = 0;
                                    $totalDeduction = 0;
                                    $totalNet = 0;
                                @endphp

                                @forelse($salaries as $salary)
                                    @php
                                        $totalBasic += $salary->basic_salary;
                                        $totalEarning += $salary->total_earning;
                                        $totalDeduction += $salary->total_deduction;
                                        $totalNet += $salary->net_salary;
                                    @endphp

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $salary->salary_month->format('F Y') }}</td>

                                        <td>
                                            {{ number_format($salary->basic_salary, 2) }}
                                        </td>

                                        <td class="text-success">
                                            {{ number_format($salary->total_earning, 2) }}
                                        </td>

                                        <td class="text-danger">
                                            {{ number_format($salary->total_deduction, 2) }}
                                        </td>

                                        <td class="fw-bold">
                                            {{ number_format($salary->net_salary, 2) }}
                                        </td>

                                        <td>
                                            <span class="badge 
                                                {{ $salary->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($salary->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('employee.salary.details', $salary->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            No salary records found.
                                        </td>
                                    </tr>
                                @endforelse

                                @if($salaries->count())
                                    <tr class="fw-bold table-light">
                                        <td colspan="2" class="text-end">Total</td>

                                        <td>
                                            {{ number_format($totalBasic, 2) }}
                                        </td>

                                        <td class="text-success">
                                            {{ number_format($totalEarning, 2) }}
                                        </td>

                                        <td class="text-danger">
                                            {{ number_format($totalDeduction, 2) }}
                                        </td>

                                        <td>
                                            {{ number_format($totalNet, 2) }}
                                        </td>

                                        <td></td>
                                        <td></td>
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
