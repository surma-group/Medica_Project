@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($holiday) ? 'Holiday Edit' : 'Holiday Add' }}</h1>
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

    <form action="{{ isset($holiday) ? route('holiday.update', $holiday->id) : route('holiday.store') }}"
        method="POST">
        @csrf
        @if(isset($holiday))
        @method('PUT')
        @endif

        <div class="row">
            <div class="col-12 col-md-6">
                <!-- Holiday Title -->
                <div class="form-group">
                    <label>Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control"
                        value="{{ old('title', $holiday->title ?? '') }}" required>
                    <div class="invalid-feedback">Title is required.</div>
                </div>

                <!-- To Date -->
                <div class="form-group">
                    <label>To <span class="text-danger">*</span></label>

                    <input type="date" name="to" class="form-control"
                        value="{{ old('to', isset($holiday->to) ? $holiday->to->format('Y-m-d') : '') }}"
                        required>

                    <div class="invalid-feedback">To date is required.</div>
                </div>

            </div>

            <div class="col-12 col-md-6">
                <!-- From Date -->
                <div class="form-group">
                    <label>From <span class="text-danger">*</span></label>

                    <input type="date" name="from" class="form-control"
                        value="{{ old('from', isset($holiday->from) ? $holiday->from->format('Y-m-d') : '') }}"
                        required>

                    <div class="invalid-feedback">From date is required.</div>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control select2">
                        <option value="1" {{ old('status', $holiday->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $holiday->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($holiday) ? 'Update' : 'Submit' }}
            </button>
        </div>
    </form>
</section>
@endsection