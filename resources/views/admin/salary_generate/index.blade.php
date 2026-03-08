@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Monthly Salary</h1>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Salary Generated List</h4>
            <a href="{{ route('salary_generate.create') }}" class="btn btn-primary">
                Generate Salary
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-md">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Month / Year</th>
                            <th>Total Employees</th>
                            <th>Total Earnings</th>
                            <th>Total Deductions</th>
                            <th>Net Salary</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($monthlySalaries as $salary)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            {{-- Month --}}
                            <td>
                                {{ \Carbon\Carbon::parse($salary->salary_month)->format('F Y') }}
                            </td>

                            {{-- Total Employees --}}
                            <td>{{ $salary->employee_count }}</td>

                            {{-- Totals --}}
                            <td class="text-end">{{ number_format($salary->total_earning, 2) }}</td>
                            <td class="text-end">{{ number_format($salary->total_deduction, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($salary->net_salary, 2) }}</td>

                            {{-- Status --}}
                            <td>
                                @if($salary->status === 'paid')
                                <div class="badge badge-success">Paid</div>
                                @else
                                <div class="badge badge-danger">Pending</div>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td>
                                <a href="{{ route('salary_generate.show', $salary->salary_month) }}"
                                    class="btn btn-sm btn-info">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No salary generated yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- Pagination --}}
            <div class="float-right">
                {{ $monthlySalaries->links() }}
            </div>
        </div>
    </div>
</section>
@endsection