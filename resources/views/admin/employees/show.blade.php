@extends('layouts.admin')

@section('content')
<section class="section">

    {{-- ================= Header ================= --}}
    <div class="section-header">
        <h1>Employee Profile</h1>
        <div class="section-header-button">
            <a href="{{ route('employees.print', $employee->id) }}" class="btn btn-success">
                <i class="fas fa-file-pdf"></i> Print PDF
            </a>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>

    {{-- ================= Profile Card ================= --}}
    <div class="card mb-3">
        <div class="card-body d-flex align-items-center">
            <div class="me-4">
                @if($employee->photo)
                    <img src="{{ asset('storage/'.$employee->photo) }}"
                         class="rounded-circle"
                         style="width:130px;height:130px;object-fit:cover;">
                @else
                    <img src="{{ asset('assets/img/avatar/avatar-1.png') }}"
                         class="rounded-circle"
                         style="width:130px;height:130px;">
                @endif
            </div>

            <div>
                <h3 class="mb-1">{{ $employee->full_name }}</h3>
                <p class="mb-0 text-muted">
                    {{ $employee->designation->name ?? '-' }}
                    • {{ $employee->department->name ?? '-' }}
                </p>
                <small class="text-muted">
                    Employee Code: {{ $employee->employee_code }}
                </small>
            </div>
        </div>
    </div>

    {{-- ================= Basic Information ================= --}}
    <div class="card mb-3">
        <div class="card-header">
            <h4>Basic Information</h4>
        </div>
        <div class="card-body row">
            <div class="col-md-6">
                <p><strong>Company:</strong> {{ $employee->company->company_name ?? '-' }}</p>
                <p><strong>Branch:</strong> {{ $employee->branch->branch_name ?? '-' }}</p>
                <p><strong>Department:</strong> {{ $employee->department->name ?? '-' }}</p>
                <p><strong>Designation:</strong> {{ $employee->designation->name ?? '-' }}</p>
                <p><strong>Employment Type:</strong> {{ $employee->employmentType->title ?? '-' }}</p>
                <p><strong>Gender:</strong> {{ $employee->gender == 1 ? 'Male' : 'Female' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Joining Date:</strong> {{ $employee->joining_date }}</p>
                <p><strong>Date of Birth:</strong> {{ $employee->date_of_birth }}</p>
                <p><strong>Personal Email:</strong> {{ $employee->personal_email }}</p>
                <p><strong>Official Email:</strong> {{ $employee->official_email }}</p>
                <p><strong>Mobile:</strong> {{ $employee->mobile }}</p>
                <p><strong>Phone:</strong> {{ $employee->phone }}</p>
            </div>
        </div>
    </div>

    {{-- ================= Address ================= --}}
    <div class="card mb-3">
        <div class="card-header">
            <h4>Address Information</h4>
        </div>
        <div class="card-body row">
            <div class="col-md-6">
                <p><strong>Permanent Address:</strong><br>{{ $employee->permanent_address }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Present Address:</strong><br>{{ $employee->present_address }}</p>
            </div>
            <div class="col-md-6 mt-2">
                <p><strong>District:</strong> {{ $employee->districtData->name_en ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- ================= Documents ================= --}}
    <div class="card mb-3">
        <div class="card-header">
            <h4>Documents & IDs</h4>
        </div>
        <div class="card-body row">
            <div class="col-md-6">
                <p><strong>NID No:</strong> {{ $employee->nid_no }}</p>
                <p><strong>Passport No:</strong> {{ $employee->passport_no }}</p>
            </div>

            <div class="col-md-6">
                @if($employee->resume)
                    <p><strong>Resume:</strong>
                        <a href="{{ asset('storage/'.$employee->resume) }}" target="_blank">View</a>
                    </p>
                @endif
                @if($employee->joining_letter)
                    <p><strong>Joining Letter:</strong>
                        <a href="{{ asset('storage/'.$employee->joining_letter) }}" target="_blank">View</a>
                    </p>
                @endif
                @if($employee->other_documents)
                    <p><strong>Other Documents:</strong>
                        <a href="{{ asset('storage/'.$employee->other_documents) }}" target="_blank">View</a>
                    </p>
                @endif
                @if($employee->security_deposit_file)
                    <p><strong>Security Deposit File:</strong>
                        <a href="{{ asset('storage/'.$employee->security_deposit_file) }}" target="_blank">View</a>
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= Emergency Contact ================= --}}
    <div class="card mb-3">
        <div class="card-header">
            <h4>Emergency Contact</h4>
        </div>
        <div class="card-body row">
            <div class="col-md-6">
                <p><strong>Name:</strong> {{ $employee->emergency_contact_name }}</p>
                <p><strong>Phone:</strong> {{ $employee->emergency_contact_phone }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Relation:</strong> {{ $employee->emergency_contact_relation }}</p>
            </div>
        </div>
    </div>

</section>
@endsection
