@extends('layouts.admin')

@push('styles')
<style>
    /* =========================
   NORMAL SCREEN VIEW
========================= */
    .print-only {
        display: none;
    }

    /* =========================
   PRINT VIEW
========================= */
    @media print {

        body * {
            visibility: hidden;
        }

        #print-area,
        #print-area * {
            visibility: visible;
        }

        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .print-only {
            display: block !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000 !important;
            padding: 6px;
            font-size: 12px;
        }

        .badge,
        button,
        a,
        .section-header,
        .card-footer {
            display: none !important;
        }

        /* Prevent page breaks inside table rows */
        table,
        thead,
        tbody,
        tr,
        td,
        th {
            page-break-inside: avoid !important;
        }

        .print-signature {
            margin-top: 50px;
            page-break-inside: avoid;
            page-break-before: avoid;
        }
    }
</style>
@endpush

@section('content')
<section class="section">

    <div class="section-header">
        <h1>Salary Details — {{ \Carbon\Carbon::parse($salaryMonth)->format('F Y') }}</h1>
        <div class="section-header-breadcrumb">
            <button onclick="printSalary()" class="btn btn-sm btn-primary">🖨 Print</button>
        </div>
    </div>

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="badge badge-{{ $status === 'paid' ? 'success' : 'warning' }}">
                {{ ucfirst($status) }}
            </span>
        </div>

        <div class="card-body p-0">

            {{-- PRINT AREA --}}
            <div id="print-area" class="p-4">

                {{-- PRINT HEADER --}}
                <div class="text-center mb-4 print-only">
                    <img src="{{ asset('admin/assets/img/logo.png') }}" alt="Company Logo" style="height:70px; margin-bottom:10px;">
                    <h2>Your Company Name</h2>
                    <p>Monthly Salary Sheet</p>
                    <strong>{{ \Carbon\Carbon::parse($salaryMonth)->format('F Y') }}</strong>
                    <hr>
                </div>

                {{-- SALARY TABLE --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Mobile</th>
                                <th class="text-end">Basic Salary</th>
                                <th class="text-end">Total Earning</th>
                                <th class="text-end">Total Deduction</th>
                                <th class="text-end">Net Salary</th>
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
                                <td>{{ $salary->employee->full_name }}</td>
                                <td>{{ $salary->employee->mobile }}</td>
                                <td class="text-end">{{ number_format($salary->basic_salary, 2) }}</td>
                                <td class="text-end">{{ number_format($salary->total_earning, 2) }}</td>
                                <td class="text-end">{{ number_format($salary->total_deduction, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($salary->net_salary, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No salary data found for this month.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold bg-light">
                                <td colspan="3" class="text-end">Grand Total</td>
                                <td class="text-end">{{ number_format($totalBasic, 2) }}</td>
                                <td class="text-end">{{ number_format($totalEarning, 2) }}</td>
                                <td class="text-end">{{ number_format($totalDeduction, 2) }}</td>
                                <td class="text-end">{{ number_format($totalNet, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- SIGNATURE SECTION --}}
                <div class="row print-signature print-only">

                    <div class="col-6 text-center">
                        <p>__________________________</p>
                        <strong>Approved By</strong>
                    </div>
                </div>

            </div>
            {{-- END PRINT AREA --}}

        </div>

        <div class="card-footer text-end">
            @if($status === 'pending')
            <form action="{{ route('salary.approve', $salaryMonth) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">Approve</button>
            </form>
            @endif
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
    function printSalary() {
        window.print();
    }
</script>
@endpush