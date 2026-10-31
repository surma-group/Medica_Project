@extends('layouts.employee')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row mt-sm-4"> {{-- LEFT PROFILE CARD --}}
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                    <div class="card-body">
                        <div class="author-box-center"> <img src="{{ optional($employee)->photo ? asset('storage/'.optional($employee)->photo) : asset('admin/assets/img/users/user-1.png') }}" class="rounded-circle author-box-picture">
                            <div class="clearfix"></div>
                            <div class="author-box-name"> <a href="#">{{ $employee->full_name }}</a> </div>
                            <div class="author-box-job"> {{ $employee?->designation?->name ?? 'N/A' }} </div>
                        </div>
                    </div>
                </div> {{-- PERSONAL INFO --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Personal Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="py-4">
                            <p class="clearfix"> <span class="float-start">Birthday</span> <span class="float-right text-muted"> {{ optional($employee->date_of_birth)->format('d M Y') }} </span> </p>
                            <p class="clearfix"> <span class="float-start">Phone</span> <span class="float-right text-muted"> {{ $employee->mobile }} </span> </p>
                            <p class="clearfix"> <span class="float-start">Email</span> <span class="float-right text-muted"> {{ $employee->personal_email}} </span> </p>
                            <p class="clearfix"> <span class="float-start">Department</span> <span class="float-right text-muted"> {{ $employee->department->name ?? 'N/A' }} </span> </p>
                            <p class="clearfix"> <span class="float-start">Company</span> <span class="float-right text-muted"> {{ $employee->company->company_name ?? 'N/A' }} </span> </p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- RIGHT CONTENT --}}
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card">
                    <div class="padding-20">

                        <ul class="nav nav-tabs" id="profileTab">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#basic-info">Basic</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#contact-info">Contact</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#documents">Documents & IDs</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#emergency">Emergency Contact</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#account">Security</a>
                            </li>
                        </ul>

                        <div class="tab-content tab-bordered mt-3">

                            {{-- BASIC INFORMATION --}}
                            <div class="tab-pane fade show active" id="basic-info">
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>First Name:</strong> {{ $employee->first_name }}</div>
                                    <div class="col-md-6"><strong>Last Name:</strong> {{ $employee->last_name }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Full Name:</strong> {{ $employee->full_name }}</div>
                                    <div class="col-md-6"><strong>Gender:</strong> {{ $employee->gender == 1 ? 'Male' : 'Female' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Company:</strong> {{ $employee->company->company_name ?? 'N/A' }}</div>
                                    <div class="col-md-6"><strong>Branch:</strong> {{ $employee->branch->branch_name ?? 'N/A' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}</div>
                                    <div class="col-md-6"><strong>Designation:</strong> {{ $employee->designation->name ?? 'N/A' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Employment Type:</strong> {{ $employee->employmentType->title ?? 'N/A' }}</div>
                                    <div class="col-md-6"><strong>Joining Date:</strong> {{ optional($employee->joining_date)->format('d M Y') ?? 'N/A' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Date of Birth:</strong> {{ optional($employee->date_of_birth)->format('d M Y') ?? 'N/A' }}</div>
                                </div>
                            </div>

                            {{-- CONTACT INFORMATION --}}
                            <div class="tab-pane fade" id="contact-info">
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Personal Email:</strong> {{ $employee->personal_email }}</div>
                                    <div class="col-md-6"><strong>Official Email:</strong> {{ $employee->official_email ?? 'N/A' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Mobile:</strong> {{ $employee->mobile }}</div>
                                    <div class="col-md-6"><strong>Phone:</strong> {{ $employee->phone ?? 'N/A' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Present Address:</strong> {{ $employee->present_address ?? 'N/A' }}</div>
                                    <div class="col-md-6"><strong>Permanent Address:</strong> {{ $employee->permanent_address ?? 'N/A' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>District:</strong> {{ $employee->districtInfo->name ?? 'N/A' }}</div>
                                </div>
                            </div>

                            {{-- DOCUMENTS & IDS --}}
                            <div class="tab-pane fade" id="documents">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <strong>Photo:</strong><br>
                                        @if($employee->photo)
                                        <img src="{{ asset('storage/'.$employee->photo) }}" class="img-thumbnail" style="max-width:150px;">
                                        @else N/A @endif
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Resume:</strong><br>
                                        @if($employee->resume)
                                        <a href="{{ asset('storage/'.$employee->resume) }}" target="_blank">View</a>
                                        @else N/A @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Passport No:</strong> {{ $employee->passport_no ?? 'N/A' }}</div>
                                    <div class="col-md-6"><strong>NID No:</strong> {{ $employee->nid_no ?? 'N/A' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <strong>Joining Letter:</strong>
                                        @if($employee->joining_letter)
                                        <a href="{{ asset('storage/'.$employee->joining_letter) }}" target="_blank">View</a>
                                        @else N/A @endif
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Other Documents:</strong>
                                        @if($employee->other_documents)
                                        <a href="{{ asset('storage/'.$employee->other_documents) }}" target="_blank">View</a>
                                        @else N/A @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Security Deposit Type:</strong> {{ ucfirst($employee->security_deposit_type ?? 'N/A') }}</div>
                                    <div class="col-md-6">
                                        <strong>Security Deposit File:</strong>
                                        @if($employee->security_deposit_file)
                                        <a href="{{ asset('storage/'.$employee->security_deposit_file) }}" target="_blank">View</a>
                                        @else N/A @endif
                                    </div>
                                </div>
                            </div>

                            {{-- EMERGENCY CONTACT --}}
                            <div class="tab-pane fade" id="emergency">
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Contact Name:</strong> {{ $employee->emergency_contact_name ?? 'N/A' }}</div>
                                    <div class="col-md-6"><strong>Phone:</strong> {{ $employee->emergency_contact_phone ?? 'N/A' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Relation:</strong> {{ $employee->emergency_contact_relation ?? 'N/A' }}</div>
                                </div>
                            </div>

                            {{-- ACCOUNT SECURITY --}}
                            <div class="tab-pane fade" id="account">
                                <div class="row mb-2">
                                    <div class="col-md-6"><strong>Email:</strong> {{ $employee->personal_email }}</div>
                                    <div class="col-md-6"><strong>Password:</strong> ********</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection