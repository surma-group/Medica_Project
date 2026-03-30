@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Product Generics</h4>
                    <div>
                        <a href="{{ route('product_generic.create') }}" class="btn btn-primary">Add Generic</a>
                        <a href="{{ route('product_generic.import') }}" class="btn btn-success ml-2">Import Generic</a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Generic Name</th>
                                    <th>Pregnancy Category</th>
                                    <th>Status</th>
                                    <th style="width:200px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($generics as $generic)

                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $generic->generic_name }}</td>

                                    <td>{{ $generic->pregnancy_category_id ?? '-' }}</td>

                                    <td>
                                        @if($generic->status)
                                        <div class="badge badge-success">Active</div>
                                        @else
                                        <div class="badge badge-danger">Inactive</div>
                                        @endif
                                    </td>

                                    <td>

                                        <a href="{{ route('product_generic.edit',$generic->id) }}"
                                            class="btn btn-sm btn-primary">
                                            Edit
                                        </a>

                                        <form action="{{ route('product_generic.destroy',$generic->id) }}"
                                            method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Are you sure you want to delete this generic?');">

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
                                        No generics found.
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    <div class="p-3">
                        {{ $generics->links() }}
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>
@endsection