@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row ">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Companies</h4>
                    <a href="{{ route('company.create') }}" class="btn btn-primary">Add Company</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Company Code</th>
                                    <th>Name</th>
                                    <th>District</th>
                                    <th>Currency</th>
                                    <th>Timezone</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companies as $company)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $company->company_code }}</td>
                                    <td>{{ $company->company_name }}</td>
                                    <td>{{ $company->district->name_en ?? '-' }}</td>
                                    <td>{{ $company->currency->code ?? '-' }}</td>
                                    <td>{{ $company->timezone->name ?? '-' }}</td>
                                    <td>{{ $company->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if($company->status == 1)
                                            <div class="badge badge-success">Active</div>
                                        @else
                                            <div class="badge badge-danger">Inactive</div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('company.edit', $company) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('company.destroy', $company) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                @if($companies->isEmpty())
                                <tr>
                                    <td colspan="9" class="text-center">No companies found.</td>
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
