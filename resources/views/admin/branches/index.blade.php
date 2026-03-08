@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Branches</h4>
                    <a href="{{ route('branch.create') }}" class="btn btn-primary">
                        Add Branch
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Branch Name</th>
                                    <th>Branch Code</th>
                                    <th>Company</th>
                                    <th>District</th>
                                    <th>Status</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branches as $branch)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $branch->branch_name }}</td>
                                        <td>{{ $branch->branch_code }}</td>
                                        <td>{{ $branch->company->company_name ?? 'N/A' }}</td>
                                        <td>{{ $branch->districtInfo->name_en ?? 'N/A' }}</td>
                                        <td>
                                            @if($branch->status == 1)
                                                <div class="badge badge-success">Active</div>
                                            @else
                                                <div class="badge badge-danger">Inactive</div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('branch.edit', $branch->id) }}"
                                               class="btn btn-sm btn-primary">
                                                Edit
                                            </a>

                                            <form action="{{ route('branch.destroy', $branch->id) }}"
                                                  method="POST"
                                                  style="display:inline-block;"
                                                  onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            No branches found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    {{ $branches->links() }}
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
