@extends('layouts.admin')

@section('content')

<section class="section">
    <div class="row">
        <div class="col-12">

            <div class="card">

                {{-- HEADER --}}
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">System Settings</h5>
                </div>

                {{-- SUCCESS MESSAGE --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3 mb-0 py-2">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                {{-- VALIDATION ERRORS --}}
                @if($errors->any())
                <div class="alert alert-danger m-3 mb-0 py-2">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- TABLE --}}
                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-striped table-sm mb-0">

                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th>Title</th>
                                    <th>Value</th>
                                    <th>Type</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                    <th width="100">Action</th>
                                </tr>
                            </thead>


                            <tbody>
                                @forelse($settings as $setting)
                                <tr>
                                    <td>
                                        {{ $settings->firstItem() + $loop->index }}
                                    </td>

                                    <td>{{ $setting->title }}</td>

                                    <td>
                                        <form action="{{ route('settings.update', $setting->id) }}"
                                            method="POST"
                                            class="d-flex align-items-center gap-2">

                                            @csrf
                                            @method('PUT')

                                            @if(in_array($setting->slug, ['deliveryMan', 'transitOfficer']))
                                            <select name="value"
                                                class="form-select form-select-sm"
                                                required>

                                                <option value="">Select Designation</option>

                                                @foreach($designations as $designation)
                                                <option value="{{ $designation->id }}"
                                                    @selected(
                                                    (string) old('value', $setting->value)
                                                    === (string) $designation->id
                                                    )>
                                                    {{ $designation->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @else
                                            <input type="text"
                                                name="value"
                                                class="form-control form-control-sm"
                                                value="{{ old('value', $setting->value) }}"
                                                placeholder="Enter value">
                                            @endif

                                            <input type="hidden"
                                                name="status"
                                                value="{{ $setting->status ? 1 : 0 }}">

                                            <button type="submit"
                                                class="btn btn-success btn-sm text-nowrap">
                                                Update
                                            </button>
                                        </form>
                                    </td>

                                    <td>{{ $setting->type }}</td>

                                    <td>{{ $setting->group ?? '-' }}</td>

                                    <td>
                                        @if($setting->status)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>

                                    <td>
                                        <form action="{{ route('settings.update', $setting->id) }}"
                                            method="POST">

                                            @csrf
                                            @method('PUT')

                                            <input type="hidden"
                                                name="value"
                                                value="{{ $setting->value }}">

                                            <input type="hidden"
                                                name="status"
                                                value="{{ $setting->status ? 0 : 1 }}">

                                            <button type="submit"
                                                class="btn btn-sm {{ $setting->status ? 'btn-warning' : 'btn-success' }} py-0 px-2">
                                                {{ $setting->status ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-3">
                                        No settings found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>


                        </table>

                    </div>

                </div>

                {{-- PAGINATION --}}
                <div class="card-footer py-2 text-end">
                    {{ $settings->links() }}
                </div>

            </div>

        </div>
    </div>
</section>

@endsection