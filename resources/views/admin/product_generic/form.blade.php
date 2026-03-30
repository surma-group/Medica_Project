@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ isset($product_generic) ? 'Product Generic Edit' : 'Product Generic Add' }}</h1>
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

    <form action="{{ isset($product_generic) ? route('product_generic.update',$product_generic->id) : route('product_generic.store') }}"
          method="POST">
        @csrf

        @if(isset($product_generic))
            @method('PUT')
        @endif

        <div class="row">

            {{-- LEFT SIDE --}}
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Generic Name <span class="text-danger">*</span></label>
                    <input type="text"
                           name="generic_name"
                           class="form-control"
                           value="{{ old('generic_name',$product_generic->generic_name ?? '') }}"
                           required>
                </div>

                <div class="form-group">
                    <label>Pregnancy Category</label>
                    <input type="text"
                           name="pregnancy_category_id"
                           class="form-control"
                           value="{{ old('pregnancy_category_id',$product_generic->pregnancy_category_id ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Precaution</label>
                    <textarea name="precaution"
                              class="form-control"
                              rows="3">{{ old('precaution',$product_generic->precaution ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Indication</label>
                    <textarea name="indication"
                              class="form-control"
                              rows="3">{{ old('indication',$product_generic->indication ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Contra Indication</label>
                    <textarea name="contra_indication"
                              class="form-control"
                              rows="3">{{ old('contra_indication',$product_generic->contra_indication ?? '') }}</textarea>
                </div>

            </div>


            {{-- RIGHT SIDE --}}
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label>Dose</label>
                    <textarea name="dose"
                              class="form-control"
                              rows="3">{{ old('dose',$product_generic->dose ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Side Effect</label>
                    <textarea name="side_effect"
                              class="form-control"
                              rows="3">{{ old('side_effect',$product_generic->side_effect ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Mode Of Action</label>
                    <textarea name="mode_of_action"
                              class="form-control"
                              rows="3">{{ old('mode_of_action',$product_generic->mode_of_action ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Interaction</label>
                    <textarea name="interaction"
                              class="form-control"
                              rows="3">{{ old('interaction',$product_generic->interaction ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control select2" required>
                        <option value="1" {{ old('status',$product_generic->status ?? 1) == 1 ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="0" {{ old('status',$product_generic->status ?? 1) == 0 ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>

            </div>

        </div>

        <div class="card-footer text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($product_generic) ? 'Update' : 'Submit' }}
            </button>
        </div>

    </form>
</section>
@endsection