@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Employees</h4>
                    <a href="{{ route('employees.create') }}" class="btn btn-primary">Add Employee</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="employeeTable" class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee Code</th>
                                    <th>Full Name</th>
                                    <th>Mobile</th>
                                    <th>Company</th>
                                    <th>Branch</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Joining Date</th>
                                    <th>Status</th>
                                    <th>Set Salary</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $employee->employee_code }}</td>
                                    <td>{{ $employee->full_name }}</td>
                                    <td>{{ $employee->mobile }}</td>
                                    <td>{{ $employee->company->company_name ?? '-' }}</td>
                                    <td>{{ $employee->branch->branch_name ?? '-' }}</td>
                                    <td>{{ $employee->department->name ?? '-' }}</td>
                                    <td>{{ $employee->designation->name ?? '-' }}</td>
                                    <td>{{ $employee->joining_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td>
                                        @if($employee->status == 1)
                                        <div class="badge badge-success">Active</div>
                                        @else
                                        <div class="badge badge-danger">Inactive</div>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-sm btn-info btn-set-salary"
                                            data-id="{{ $employee->id }}"
                                            data-name="{{ $employee->full_name }}">
                                            Set Salary
                                        </button>
                                    </td>
                                    <td>
                                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                @if($employees->isEmpty())
                                <tr>
                                    <td colspan="12" class="text-center">No employees found.</td>
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
<div class="modal fade" id="salaryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <form id="salaryForm" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">
                        Set Salary for <span id="employeeName"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="employee_id" id="employeeId">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Basic Salary Row --}}
                            <tr>
                                <td>Basic Salary</td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end">
                                    <input type="number"
                                        step="0.01"
                                        name="basic_salary"
                                        id="basicSalary"
                                        class="form-control text-end"
                                        value="{{ $basicSalary ?? 0 }}"
                                        required>
                                </td>
                            </tr>

                            {{-- Earning Components --}}
                            @foreach($salaryComponents->where('payment_type', 1) as $component)
                            <tr>
                                <td>{{ $component->title }}</td>
                                <td class="text-end">Earning</td>
                                <td class="text-end">
                                    @if($component->amount_type == 2)
                                    {{ $component->amount ?? 0 }}%
                                    @else
                                    {{ $component->amount ?? 0 }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    <input type="number"
                                        name="components[{{ $component->id }}]"
                                        class="form-control component-input text-end"
                                        data-type="{{ $component->amount_type }}"
                                        data-amount="{{ $component->amount }}"
                                        value="0"
                                        readonly>
                                </td>
                            </tr>
                            @endforeach

                            {{-- Total Earnings --}}
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Total Earnings</td>
                                <td class="text-end total-earnings">0.00</td>
                            </tr>

                            {{-- Deduction Components --}}
                            @foreach($salaryComponents->where('payment_type', 2) as $component)
                            <tr>
                                <td>{{ $component->title }}</td>
                                <td class="text-end">Deduction</td>
                                <td class="text-end">
                                    @if($component->amount_type == 2)
                                    {{ $component->amount ?? 0 }}%
                                    @else
                                    {{ $component->amount ?? 0 }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    <input type="number"
                                        name="components[{{ $component->id }}]"
                                        class="form-control component-input text-end"
                                        data-type="{{ $component->amount_type }}"
                                        data-amount="{{ $component->amount }}"
                                        value="0"
                                        readonly>
                                </td>
                            </tr>
                            @endforeach

                            {{-- Total Deductions --}}
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Total Deductions</td>
                                <td class="text-end total-deductions">0.00</td>
                            </tr>

                            {{-- Net Salary --}}
                            <tr class="table-success fw-bold">
                                <td colspan="3" class="text-end">Net Salary</td>
                                <td class="text-end net-salary">0.00</td>
                            </tr>
                        </tbody>

                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Close
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Save Salary
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Trigger when basic salary or component amount changes
    $('#basicSalary').on('input', calculateComponentTotals);
    $(document).on('input', '.component-amount', calculateComponentTotals);

    // Also trigger on modal open
    $('#salaryModal').on('shown.bs.modal', calculateComponentTotals);

    // Salary save
    $(document).ready(function() {
        $('#salaryForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let submitBtn = form.find('button[type="submit"]');

            submitBtn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('salary.structure') }}",
                type: "POST",
                data: form.serialize(), // all input fields serialized
                success: function(response) {
                    submitBtn.prop('disabled', false).text('Save Salary');

                    $('#salaryModal').modal('hide');

                    // Optional: reset form
                    form[0].reset();

                    // Show success message
                    alert(response.message ?? 'Salary saved successfully');
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).text('Save Salary');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let msg = '';

                        $.each(errors, function(key, value) {
                            msg += value[0] + '\n';
                        });

                        alert(msg);
                    } else {
                        alert('Something went wrong. Try again.');
                    }
                }
            });
        });
    });


    // calculate Salary
    $(document).ready(function() {

        function calculateSalary() {
            let basic = parseFloat($('#basicSalary').val()) || 0;
            let totalEarnings = basic;
            let totalDeductions = 0;

            $('#salaryModal tbody tr').each(function() {
                let paymentType = $(this).find('td:nth-child(2)').text().trim(); // Earning or Deduction
                let totalCell = $(this).find('.component-input'); // input inside 4th td
                if (!totalCell.length) return;

                let type = totalCell.data('type'); // 1=Flat, 2=Percentage
                let amount = parseFloat(totalCell.data('amount')) || 0;
                let value = 0;

                if (type == 1) {
                    value = amount; // Flat amount stays as-is
                } else if (type == 2) {
                    value = (basic * amount) / 100; // Percentage of basic
                }

                // Update input value
                totalCell.val(value.toFixed(2));

                // Add to totals
                if (paymentType === 'Earning') totalEarnings += value;
                if (paymentType === 'Deduction') totalDeductions += value;
            });

            // Update Total Earnings / Deductions / Net Salary
            $('#salaryModal tbody tr').each(function() {
                if ($(this).find('td:contains("Total Earnings")').length) {
                    $(this).find('td:last').text(totalEarnings.toFixed(2));
                }
                if ($(this).find('td:contains("Total Deductions")').length) {
                    $(this).find('td:last').text(totalDeductions.toFixed(2));
                }
                if ($(this).find('td:contains("Net Salary")').length) {
                    $(this).find('td:last').text((totalEarnings - totalDeductions).toFixed(2));
                }
            });
        }

        // Trigger calculation on Basic Salary input
        $('#basicSalary').on('input', calculateSalary);

        // Trigger initial calculation
        calculateSalary();

    });


    $(document).ready(function() {
        $('#basicSalary').on('input', function() {
            let basic = parseFloat($(this).val()) || 0;

            $('.component-input').each(function() {
                let type = $(this).data('type'); // 1 = Flat, 2 = Percentage
                let amount = parseFloat($(this).data('amount'));

                if (type == 2) {
                    // percentage calculation
                    $(this).val((basic * amount / 100).toFixed(2));
                }
                // Flat components remain as-is (already in value)
            });
        });

        // Trigger calculation on page load (in case Basic Salary is prefilled)
        $('#basicSalary').trigger('input');
    });

    $(document).on('click', '.btn-set-salary', function() {
        let employeeId = $(this).data('id');
        let employeeName = $(this).data('name');

        // Reset form
        $('#salaryForm')[0].reset();

        // Reset component inputs
        $('.component-input').each(function() {
            let type = $(this).data('type');
            let amount = parseFloat($(this).data('amount')) || 0;
            $(this).val(type === 1 ? amount : 0); // flat = original amount, percentage = 0
        });

        // Reset totals
        $('.total-earnings').text('0.00');
        $('.total-deductions').text('0.00');
        $('.net-salary').text('0.00');

        // Set employee info
        $('#employeeId').val(employeeId);
        $('#employeeName').text(employeeName);

        // Show modal
        $('#salaryModal').modal('show');

        // Load existing salary if exists
        $.ajax({
            url: "{{ route('salary.structure.show', ':id') }}".replace(':id', employeeId),
            type: "GET",
            success: function(res) {
                if (!res.status) return;

                let salary = res.data;

                // Set basic salary
                $('#basicSalary').val(salary.basic_salary);

                // Set component amounts
                salary.details.forEach(function(detail) {
                    $('input[name="components[' + detail.salary_component_id + ']"]').val(detail.amount);
                });

                // Recalculate totals
                recalcSalaryTotals();
            }
        });
    });

    // Function to recalculate totals dynamically
    function recalcSalaryTotals() {
        let basicSalary = parseFloat($('#basicSalary').val()) || 0;
        let totalEarnings = basicSalary;
        let totalDeductions = 0;

        $('.component-input').each(function() {
            let type = $(this).data('type'); // 1 = flat, 2 = percentage
            let amount = parseFloat($(this).data('amount')) || 0;
            let value = parseFloat($(this).val()) || 0;

            if (type === 2) {
                value = (basicSalary * amount) / 100;
                $(this).val(value.toFixed(2));
            }

            let paymentType = $(this).closest('tr').find('td:nth-child(2)').text().trim();
            if (paymentType === 'Earning') totalEarnings += value;
            if (paymentType === 'Deduction') totalDeductions += value;
        });

        // Update totals
        $('.total-earnings').text(totalEarnings.toFixed(2));
        $('.total-deductions').text(totalDeductions.toFixed(2));
        $('.net-salary').text((totalEarnings - totalDeductions).toFixed(2));
    }

    // Trigger recalculation when Basic Salary changes
    $('#basicSalary').on('input', recalcSalaryTotals);




    // DataTable Initialization with Export Buttons
    $(document).ready(function() {
        $('#employeeTable').DataTable({
            dom: 'Bfrtip',
            buttons: [

                {
                    extend: 'excel',
                    exportOptions: {
                        modifier: {
                            page: 'all'
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        modifier: {
                            page: 'all'
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        modifier: {
                            page: 'all'
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                }
            ]
        });
    });

    function calculateComponentTotals() {
        let basicSalary = parseFloat($('#basicSalary').val()) || 0;
        let totalSalary = basicSalary;

        $('.component-amount').each(function() {
            let type = $(this).data('type'); // 1=fixed, 2=percentage
            let value = parseFloat($(this).val()) || 0;
            let total = 0;

            if (type == 1) {
                total = value; // Fixed
            } else if (type == 2) {
                total = (basicSalary * value) / 100; // Percentage of basic
            }

            // Update Total Amount input
            $(this).closest('tr').find('.component-input').val(total.toFixed(2));

            // Add/subtract from total salary
            let paymentType = $(this).closest('tr').find('td:nth-child(2)').text();
            if (paymentType.toLowerCase() === 'earning') {
                totalSalary += total;
            } else {
                totalSalary -= total;
            }
        });

        $('#totalSalary').text(totalSalary.toFixed(2));
    }
</script>

@endpush