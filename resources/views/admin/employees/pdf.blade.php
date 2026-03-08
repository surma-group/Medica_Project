<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Profile - {{ $employee->full_name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.4; }
        .header { text-align:center; margin-bottom:20px; }
        .profile-img { width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:10px; }
        h2, h3, h4 { margin: 5px 0; }
        table { width:100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border:1px solid #ccc; padding:5px; text-align:left; }
        th { background:#f2f2f2; }
        .section-title { background:#ddd; font-weight:bold; padding:5px; margin-top:15px; }
        .document a { text-decoration:none; color:blue; }
    </style>
</head>
<body>

{{-- ================= Header ================= --}}
<div class="header">
    @if($employee->photo)
        <img src="{{ public_path('storage/'.$employee->photo) }}" class="profile-img">
    @endif
    <h2>{{ $employee->full_name }}</h2>
    <p>{{ $employee->designation->name ?? '' }} | {{ $employee->department->name ?? '' }}</p>
    <small>Employee Code: {{ $employee->employee_code }}</small>
</div>

{{-- ================= Basic Information ================= --}}
<div class="section-title">Basic Information</div>
<table>
    <tr><th>Company</th><td>{{ $employee->company->company_name ?? '-' }}</td></tr>
    <tr><th>Branch</th><td>{{ $employee->branch->branch_name ?? '-' }}</td></tr>
    <tr><th>Department</th><td>{{ $employee->department->name ?? '-' }}</td></tr>
    <tr><th>Designation</th><td>{{ $employee->designation->name ?? '-' }}</td></tr>
    <tr><th>Employment Type</th><td>{{ $employee->employmentType->title ?? '-' }}</td></tr>
    <tr><th>Gender</th><td>{{ $employee->gender == 1 ? 'Male' : 'Female' }}</td></tr>
    <tr><th>Joining Date</th><td>{{ $employee->joining_date }}</td></tr>
    <tr><th>Date of Birth</th><td>{{ $employee->date_of_birth }}</td></tr>
    <tr><th>Full Name</th><td>{{ $employee->full_name }}</td></tr>
    <tr><th>First Name</th><td>{{ $employee->first_name }}</td></tr>
    <tr><th>Last Name</th><td>{{ $employee->last_name }}</td></tr>
</table>

{{-- ================= Contact Information ================= --}}
<div class="section-title">Contact Information</div>
<table>
    <tr><th>Personal Email</th><td>{{ $employee->personal_email }}</td></tr>
    <tr><th>Official Email</th><td>{{ $employee->official_email }}</td></tr>
    <tr><th>Mobile</th><td>{{ $employee->mobile }}</td></tr>
    <tr><th>Phone</th><td>{{ $employee->phone }}</td></tr>
    <tr><th>Permanent Address</th><td>{{ $employee->permanent_address }}</td></tr>
    <tr><th>Present Address</th><td>{{ $employee->present_address }}</td></tr>
    <tr><th>District</th><td>{{ $employee->districtData->name_en ?? '-' }}</td></tr>
</table>

{{-- ================= Documents & IDs ================= --}}
<div class="section-title">Documents & IDs</div>
<table>
    <tr><th>NID No</th><td>{{ $employee->nid_no }}</td></tr>
    <tr><th>Passport No</th><td>{{ $employee->passport_no }}</td></tr>
    <tr><th>Photo</th><td>
        @if($employee->photo)
            <img src="{{ public_path('storage/'.$employee->photo) }}" style="width:100px;height:100px;object-fit:cover;">
        @endif
    </td></tr>
    <tr><th>Resume</th><td class="document">@if($employee->resume)<a href="{{ public_path('storage/'.$employee->resume) }}" target="_blank">View</a>@endif</td></tr>
    <tr><th>Joining Letter</th><td class="document">@if($employee->joining_letter)<a href="{{ public_path('storage/'.$employee->joining_letter) }}" target="_blank">View</a>@endif</td></tr>
    <tr><th>Other Documents</th><td class="document">@if($employee->other_documents)<a href="{{ public_path('storage/'.$employee->other_documents) }}" target="_blank">View</a>@endif</td></tr>
    <tr><th>Security Deposit File</th><td class="document">@if($employee->security_deposit_file)<a href="{{ public_path('storage/'.$employee->security_deposit_file) }}" target="_blank">View</a>@endif</td></tr>
    <tr><th>Security Deposit Type</th><td>{{ $employee->security_deposit_type ?? '-' }}</td></tr>
</table>

{{-- ================= Emergency Contact ================= --}}
<div class="section-title">Emergency Contact</div>
<table>
    <tr><th>Name</th><td>{{ $employee->emergency_contact_name }}</td></tr>
    <tr><th>Phone</th><td>{{ $employee->emergency_contact_phone }}</td></tr>
    <tr><th>Relation</th><td>{{ $employee->emergency_contact_relation }}</td></tr>
</table>

</body>
</html>
