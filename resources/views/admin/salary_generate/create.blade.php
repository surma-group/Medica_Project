@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Generate Monthly Salary</h1>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
    <div class="alert alert-success alert-has-icon">
        <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
        <div class="alert-body">
            <div class="alert-title">Success!</div>
            {{ session('success') }}
        </div>
    </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
    <div class="alert alert-danger alert-has-icon">
        <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
        <div class="alert-body">
            <div class="alert-title">Error!</div>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <form action="{{ route('salary_generate.store') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-body">

                {{-- Salary Month --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label>Salary Month</label>
                        <input type="month"
                            name="salary_month"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label>Total Employees</label>
                        <input type="text"
                            class="form-control"
                            value="{{ $employees->count() }}"
                            readonly>
                    </div>
                </div>

                {{-- Salary Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Phone</th>
                                <th class="text-end">Basic</th>
                                <th class="text-end">Earning</th>
                                <th class="text-end">Deduction</th>
                                <th class="text-end">Net</th>
                                <th width="40"></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($employees as $employee)
                            @php
                            $structure = $employee->salaryStructure;
                            $basic = $structure->basic_salary ?? 0;
                            $earning = $structure->total_earning ?? 0;
                            $deduction = $structure->total_deduction ?? 0;
                            $net = $structure->net_salary ?? 0;
                            @endphp

                            {{-- Main Row --}}
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $employee->full_name }}</td>
                                <td>{{ $employee->mobile }}</td>

                                <td class="text-end">
                                    <input type="number" step="0.01"
                                        name="employees[{{ $employee->id }}][basic_salary]"
                                        value="{{ $basic }}"
                                        class="form-control form-control-sm text-end basic-input"
                                        data-employee-id="{{ $employee->id }}">
                                </td>

                                <td class="text-end earning-total">{{ number_format($earning, 2) }}</td>
                                <td class="text-end deduction-total">{{ number_format($deduction, 2) }}</td>
                                <td class="text-end fw-bold net-total">{{ number_format($net, 2) }}</td>

                                <td class="text-center">
                                    <button type="button"
                                        class="btn btn-sm btn-icon btn-outline-primary toggle-salary"
                                        data-target="salary-{{ $employee->id }}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- Expandable Row --}}
                            <tr id="salary-{{ $employee->id }}" class="bg-light" style="display:none;">
                                <td colspan="8">
                                    @if($structure)
                                    <div class="row">

                                        {{-- Earnings Table --}}
                                        @php $totalEarnings = 0; @endphp
                                        <table class="table table-sm table-bordered mb-2 earnings-table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="40" class="text-center">Select</th>
                                                    <th>Title</th>
                                                    <th>Type</th>
                                                    <th>Amount</th>
                                                    <th>Total Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($structure->details as $detail)
                                                @if(($detail->component->payment_type ?? 1) == 1)
                                                @php
                                                $amount = $detail->calculated_amount ?? 0;
                                                $totalEarnings += $amount;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox"
                                                            class="component-checkbox"
                                                            name="employees[{{ $employee->id }}][earnings_selected][{{ $detail->salary_component_id }}]"
                                                            value="1"
                                                            checked>
                                                    </td>
                                                    <td>{{ $detail->component->title }}</td>
                                                    <td>Earning</td>
                                                    <td>{{ $detail->component->amount_type == 2 ? $detail->component->amount.'%' : '-' }}</td>
                                                    <td>
                                                        <input type="number" step="0.01"
                                                            class="component-input"
                                                            name="employees[{{ $employee->id }}][earnings][{{ $detail->salary_component_id }}]"
                                                            value="{{ $amount }}">
                                                    </td>
                                                </tr>
                                                @endif
                                                @endforeach
                                                <tr class="fw-bold">
                                                    <td colspan="4" class="text-end">Total Earnings</td>
                                                    <td class="text-end total-earning">{{ number_format($totalEarnings, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        {{-- Deductions Table --}}
                                        @php $totalDeductions = 0; @endphp
                                        <table class="table table-sm table-bordered mb-2 deductions-table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="40" class="text-center">Select</th>
                                                    <th>Title</th>
                                                    <th>Type</th>
                                                    <th>Amount</th>
                                                    <th>Total Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($structure->details as $detail)
                                                @if(($detail->component->payment_type ?? 1) == 2)
                                                @php
                                                $amount = $detail->calculated_amount ?? 0;
                                                $totalDeductions += $amount;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox"
                                                            class="component-checkbox"
                                                            name="employees[{{ $employee->id }}][deductions_selected][{{ $detail->salary_component_id }}]"
                                                            value="1"
                                                            checked>
                                                    </td>
                                                    <td>{{ $detail->component->title }}</td>
                                                    <td>Deduction</td>
                                                    <td>{{ $detail->component->amount_type == 2 ? $detail->component->amount.'%' : '-' }}</td>
                                                    <td>
                                                        <input type="number" step="0.01"
                                                            class="component-input"
                                                            name="employees[{{ $employee->id }}][deductions][{{ $detail->salary_component_id }}]"
                                                            value="{{ $amount }}">
                                                    </td>
                                                </tr>
                                                @endif
                                                @endforeach
                                                <tr class="fw-bold">
                                                    <td colspan="4" class="text-end">Total Deductions</td>
                                                    <td class="text-end total-deduction">{{ number_format($totalDeductions, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        {{-- Net Salary --}}
                                        @php $netSalary = $basic + $totalEarnings - $totalDeductions; @endphp
                                        <table class="table table-sm table-bordered mb-0">
                                            <tbody>
                                                <tr class="bg-success text-white fw-bold">
                                                    <td colspan="4" class="text-end">Net Salary</td>
                                                    <td class="text-end net-salary">{{ number_format($netSalary, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                    @else
                                    <div class="text-danger">
                                        No salary structure found for this employee.
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-warning mt-3">
                    <strong>Note:</strong>
                    You can edit salary structure values before generating salary.
                </div>

            </div>

            <div class="card-footer text-right">
                <a href="{{ route('salary_generate.index') }}" class="btn btn-secondary">
                    Back
                </a>
                <button type="submit" class="btn btn-primary">
                    Generate Salary
                </button>
            </div>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Toggle expandable salary table
        document.querySelectorAll('.toggle-salary').forEach(btn => {
            btn.addEventListener('click', function() {
                let targetId = this.dataset.target;
                let row = document.getElementById(targetId);
                let icon = this.querySelector('i');

                if (row.style.display === 'none') {
                    row.style.display = 'table-row';
                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-minus');
                } else {
                    row.style.display = 'none';
                    icon.classList.remove('fa-minus');
                    icon.classList.add('fa-plus');
                }
            });
        });

        // Live calculation function
        function updateEmployeeSalary(empRow) {
            const empId = empRow.id.replace('salary-', '');
            const basicInput = document.querySelector(`.basic-input[data-employee-id="${empId}"]`);
            const basic = parseFloat(basicInput?.value || 0);

            let totalEarnings = 0;
            let totalDeductions = 0;

            // Earnings table
            empRow.querySelectorAll('.earnings-table tbody tr').forEach(tr => {
                const checkbox = tr.querySelector('.component-checkbox');
                const input = tr.querySelector('.component-input');
                if (!checkbox || !input) return;
                if (checkbox.checked) totalEarnings += parseFloat(input.value) || 0;
            });

            // Deductions table
            empRow.querySelectorAll('.deductions-table tbody tr').forEach(tr => {
                const checkbox = tr.querySelector('.component-checkbox');
                const input = tr.querySelector('.component-input');
                if (!checkbox || !input) return;
                if (checkbox.checked) totalDeductions += parseFloat(input.value) || 0;
            });

            // Update totals
            empRow.querySelector('.total-earning').textContent = totalEarnings.toFixed(2);
            empRow.querySelector('.total-deduction').textContent = totalDeductions.toFixed(2);
            empRow.querySelector('.net-salary').textContent = (basic + totalEarnings - totalDeductions).toFixed(2);

            // Update main row totals
            const mainRow = document.querySelector(`.basic-input[data-employee-id="${empId}"]`).closest('tr');
            if (mainRow) {
                mainRow.querySelector('.earning-total').textContent = totalEarnings.toFixed(2);
                mainRow.querySelector('.deduction-total').textContent = totalDeductions.toFixed(2);
                mainRow.querySelector('.net-total').textContent = (basic + totalEarnings - totalDeductions).toFixed(2);
            }
        }

        // Attach events for each expandable row
        document.querySelectorAll('tr[id^="salary-"]').forEach(empRow => {
            // Basic salary input change
            const basicInput = document.querySelector(`.basic-input[data-employee-id="${empRow.id.replace('salary-', '')}"]`);
            if (basicInput) {
                basicInput.addEventListener('input', () => updateEmployeeSalary(empRow));
            }

            // Component input change or checkbox toggle
            empRow.querySelectorAll('.component-input, .component-checkbox').forEach(el => {
                el.addEventListener('input', () => updateEmployeeSalary(empRow));
                el.addEventListener('change', () => updateEmployeeSalary(empRow));
            });
        });
    });
</script>
@endpush