@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Designations</h4>
                    <a href="{{ route('designation.create') }}" class="btn btn-primary">
                        Add Designation
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Status</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($designations as $designation)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $designation->name }}</td>
                                        <td>{{ $designation->code }}</td>
                                        <td>
                                            @if($designation->status == 1)
                                                <div class="badge badge-success">Active</div>
                                            @else
                                                <div class="badge badge-danger">Inactive</div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('designation.edit', $designation->id) }}"
                                               class="btn btn-sm btn-primary">
                                                Edit
                                            </a>

                                            <form action="{{ route('designation.destroy', $designation->id) }}"
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
                                        <td colspan="5" class="text-center">
                                            No designations found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    {{ $designations->links() }}
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
