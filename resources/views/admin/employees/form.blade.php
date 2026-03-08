@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($employee) ? 'Employee Edit' : 'Employee Add' }}</h1>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger alert-has-icon">
        <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
        <div class="alert-body">
            <div class="alert-title">Whoops! Something went wrong.</div>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ isset($employee) ? route('employees.update', $employee->id) : route('employees.store') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($employee))
        @method('PUT')
        @endif

        {{-- =================== Basic Information =================== --}}
        <div class="card mb-3">
            <div class="card-header">
                <h4>Basic Information</h4>
            </div>
            <div class="card-body row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Company <span class="text-danger">*</span></label>
                        <select name="company_id" id="company_id" class="form-control select2" required>
                            <option value="">--Select Company--</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $employee->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                {{ $company->company_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Department <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-control select2" required>
                            <option value="">--Select Department--</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id ?? '') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Employment Type <span class="text-danger">*</span></label>
                        <select name="employment_type" class="form-control select2" required>
                            <option value="">--Select Employment Type--</option>
                            @foreach($employmentTypes as $etype)
                            <option value="{{ $etype->id }}" {{ old('employment_type', $employee->employment_type ?? '') == $etype->id ? 'selected' : '' }}>
                                {{ $etype->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control"
                            value="{{ old('last_name', $employee->last_name ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-control" required>
                            <option value="1" {{ old('gender', $employee->gender ?? '') == 1 ? 'selected' : '' }}>Male</option>
                            <option value="2" {{ old('gender', $employee->gender ?? '') == 2 ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Joining Date</label>
                        <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', isset($employee) ? $employee->joining_date->format('Y-m-d') : '') }}">

                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Branch <span class="text-danger">*</span></label>
                        <select name="branch_id" id="branch_id" class="form-control select2" required>
                            <option value="">--Select Branch--</option>
                            @if(isset($employee))
                            @foreach($employee->company->branches ?? [] as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $employee->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Designation <span class="text-danger">*</span></label>
                        <select name="designation_id" class="form-control select2" required>
                            <option value="">--Select Designation--</option>
                            @foreach($designations as $designation)
                            <option value="{{ $designation->id }}" {{ old('designation_id', $employee->designation_id ?? '') == $designation->id ? 'selected' : '' }}>
                                {{ $designation->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control"
                            value="{{ old('first_name', $employee->first_name ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control"
                            value="{{ old('full_name', $employee->full_name ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', isset($employee) && $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- =================== Contact Information =================== --}}
        <div class="card mb-3">
            <div class="card-header">
                <h4>Contact Information</h4>
            </div>
            <div class="card-body row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Personal Email <span class="text-danger">*</span></label>
                        <input type="email" required name="personal_email" class="form-control"
                            value="{{ old('personal_email', $employee->personal_email ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Mobile <span class="text-danger">*</span></label>
                        <input type="text" required name="mobile" class="form-control"
                            value="{{ old('mobile', $employee->mobile ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Permanent Address</label>
                        <textarea name="permanent_address" class="form-control">{{ old('permanent_address', $employee->permanent_address ?? '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>District <span class="text-danger">*</span></label>

                        <select name="district"
                            class="form-control select2 @error('district') is-invalid @enderror"
                            required>
                            <option value="">--Select District--</option>
                            @foreach($districts as $district)
                            <option value="{{ $district->id }}"
                                {{ old('district', $employee->district ?? '') == $district->id ? 'selected' : '' }}>
                                {{ $district->name_en }}
                            </option>
                            @endforeach
                        </select>

                        @error('district')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Official Email</label>
                        <input type="email" name="official_email" class="form-control"
                            value="{{ old('official_email', $employee->official_email ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control"
                            value="{{ old('phone', $employee->phone ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Present Address</label>
                        <textarea name="present_address" class="form-control">{{ old('present_address', $employee->present_address ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================== Documents & IDs =================== --}}
        <div class="card mb-3">
            <div class="card-header">
                <h4>Documents & IDs</h4>
            </div>
            <div class="card-body row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Photo</label>
                        <input type="file" name="photo" class="form-control">
                        @if(isset($employee) && $employee->photo)
                        <img src="{{ asset('storage/' . $employee->photo) }}" class="img-thumbnail mt-2" style="max-width:150px;">
                        @endif
                    </div>

                    <div class="form-group">
                        <label>Passport No</label>
                        <input type="text" name="passport_no" class="form-control" value="{{ old('passport_no', $employee->passport_no ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Resume</label>
                        <input type="file" name="resume" class="form-control">
                        @if(isset($employee) && $employee->resume)
                        <a href="{{ asset('storage/' . $employee->resume) }}" target="_blank">View</a>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Security Deposit Type</label>
                        <select name="security_deposit_type" class="form-control">
                            <option value="">-- Select Type --</option>
                            <option value="cash"
                                {{ old('security_deposit_type', $employee->security_deposit_type ?? '') == 'cash' ? 'selected' : '' }}>
                                Cash Amount
                            </option>
                            <option value="document"
                                {{ old('security_deposit_type', $employee->security_deposit_type ?? '') == 'document' ? 'selected' : '' }}>
                                Valuable Documents
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>NID No</label>
                        <input type="text" name="nid_no" class="form-control" value="{{ old('nid_no', $employee->nid_no ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Joining Letter</label>
                        <input type="file" name="joining_letter" class="form-control">
                        @if(isset($employee) && $employee->joining_letter)
                        <a href="{{ asset('storage/' . $employee->joining_letter) }}" target="_blank">View</a>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Other Documents</label>
                        <input type="file" name="other_documents" class="form-control">
                        @if(isset($employee) && $employee->other_documents)
                        <a href="{{ asset('storage/' . $employee->other_documents) }}" target="_blank">View</a>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Security Deposit Attachment</label>
                        <input type="file" name="security_deposit_file" class="form-control">

                        @if(isset($employee) && $employee->security_deposit_file)
                        <a href="{{ asset('storage/' . $employee->security_deposit_file) }}"
                            target="_blank" class="d-block mt-1">
                            View Uploaded File
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- =================== Emergency Contact =================== --}}
        <div class="card mb-3">
            <div class="card-header">
                <h4>Emergency Contact</h4>
            </div>
            <div class="card-body row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Contact Name</label>
                        <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $employee->emergency_contact_name ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone ?? '') }}">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Relation</label>
                        <input type="text" name="emergency_contact_relation" class="form-control" value="{{ old('emergency_contact_relation', $employee->emergency_contact_relation ?? '') }}">
                    </div>
                </div>
            </div>
        </div>
        {{-- =================== Account Security =================== --}}
        @unless(isset($employee))
        <div class="card mb-3">
            <div class="card-header">
                <h4>Account Security</h4>
            </div>
            <div class="card-body row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter password" required>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Confirm Password <span class="text-danger">*</span></label>
                        <input type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Confirm password" required>
                    </div>
                </div>
            </div>
        </div>
        @endunless


        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($employee) ? 'Update' : 'Submit' }}
            </button>
        </div>
    </form>
</section>
@endsection


@push('scripts')
<script>
$(document).ready(function() {
    $('#company_id').on('change', function() {
        var companyId = $(this).val();
        var branchSelect = $('#branch_id');
        branchSelect.html('<option value="">Loading...</option>');

        if(companyId) {
            $.ajax({
                url: '/em_admin/employees/get-branches/' + companyId,
                type: 'GET',
                success: function(data) {
                    var options = '<option value="">--Select Branch--</option>';
                    $.each(data, function(index, branch) {
                        options += '<option value="' + branch.id + '">' + branch.branch_name + '</option>';
                    });
                    branchSelect.html(options);

                    // Pre-select old branch if editing
                    var oldBranch = "{{ old('branch_id', $employee->branch_id ?? '') }}";
                    if(oldBranch) {
                        branchSelect.val(oldBranch);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to fetch branches:", error);
                    branchSelect.html('<option value="">--Select Branch--</option>');
                }
            });
        } else {
            branchSelect.html('<option value="">--Select Branch--</option>');
        }
    });

    // Trigger change on page load for edit forms
    if($('#company_id').val()) {
        $('#company_id').trigger('change');
    }
});
</script>
@endpush