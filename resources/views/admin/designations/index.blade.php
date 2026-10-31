@extends('layouts.admin')

@section('content')

<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Designations</h4>

                <a href="{{ route('designation.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Designation
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-md mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Designation</th>
                                <th>Code</th>
                                <th>Parent Designation</th>
                                <th>Status</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($designations as $designation)
                                <tr>
                                    <td>{{ $designations->firstItem() + $loop->index }}</td>

                                    <td>
                                        <strong>{{ $designation->name }}</strong>
                                    </td>

                                    <td>
                                        <span class="badge badge-light">
                                            {{ $designation->code }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($designation->parent)
                                            {{ $designation->parent->name }}
                                        @else
                                            <span class="text-muted">Top Level</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($designation->status == 1)
                                            <div class="badge badge-success">
                                                Active
                                            </div>
                                        @else
                                            <div class="badge badge-danger">
                                                Inactive
                                            </div>
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

                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        No designations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($designations->hasPages())
                <div class="card-footer">
                    {{ $designations->links() }}
                </div>
            @endif

        </div>
    </div>
</div>

</section>
@endsection
