@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($money) ? 'Edit Money' : 'Add Money' }}</h1>
    </div>

    {{-- Display Validation Errors --}}
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

    <form action="{{ isset($money) ? route('add_money.update', $money->id) : route('add_money.store') }}"
        method="POST">
        @csrf
        @if(isset($money))
        @method('PUT')
        @endif

        <div class="row">
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Amount <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0"
                        value="{{ old('amount', $money->amount ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Note</label>
                    <textarea name="note" class="form-control">{{ old('note', $money->note ?? '') }}</textarea>
                </div>

                <!-- Submit button inside col-md-6 -->
                <div class="mt-3 text-start">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        {{ isset($money) ? 'Update' : 'Submit' }}
                    </button>
                </div>

            </div>
        </div>

    </form>
</section>
@endsection