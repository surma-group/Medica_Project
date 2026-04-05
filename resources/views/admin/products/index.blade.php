@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- SUCCESS / ERROR --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Products</h4>

                    <div>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
                        <a href="{{ route('products.import') }}" class="btn btn-success">Import Product</a>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table id="productTable" class="table table-striped table-md w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Barcode</th>
                                    <th>Category</th>
                                    <th>Generic Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ================= UNIT MODAL ================= --}}
<div class="modal fade" id="setUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Set Units</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="setUnitForm" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label>Base Unit</label>
                        <select class="form-select" id="baseUnit" name="base_unit_id">
                            <option value="">-- Select --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Secondary Unit</label>
                        <select class="form-select" id="secondaryUnit" name="secondary_unit_id">
                            <option value="">-- Select --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Conversion Rate</label>
                        <input type="text" class="form-control" id="conversionRate" name="conversion_rate">
                    </div>

                    <button class="btn btn-primary w-100">Save</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

{{-- ================= SCRIPTS ================= --}}
@push('scripts')
<script>
$(document).ready(function () {

    // ================= DATATABLE (SERVER SIDE) =================
    $('#productTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('products.index') }}",
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },

            { data: 'image', name: 'image', orderable: false, searchable: false },

            { data: 'name', name: 'name' },

            { data: 'barcode', name: 'barcode' },

            { data: 'category', name: 'category.name' },

            { data: 'generic', name: 'generic.generic_name' },

            { data: 'status', name: 'status', orderable: false, searchable: false },

            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });


    // ================= UNIT MODAL =================
    const modal = document.getElementById('setUnitModal');
    const form = document.getElementById('setUnitForm');
    const baseSelect = document.getElementById('baseUnit');
    const secondarySelect = document.getElementById('secondaryUnit');
    const rateInput = document.getElementById('conversionRate');

    function validateUnits() {
        if (baseSelect.value && secondarySelect.value && baseSelect.value === secondarySelect.value) {
            alert("Base and Secondary cannot be same!");
            secondarySelect.value = '';
        }
    }

    baseSelect.addEventListener('change', validateUnits);
    secondarySelect.addEventListener('change', validateUnits);


    modal.addEventListener('show.bs.modal', function (event) {

        const btn = event.relatedTarget;

        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');

        form.action = btn.getAttribute('data-url');

        baseSelect.value = btn.getAttribute('data-base') || '';
        secondarySelect.value = btn.getAttribute('data-secondary') || '';
        rateInput.value = btn.getAttribute('data-rate') || '';

        modal.querySelector('.modal-title').textContent = "Set Units for " + name;
    });


    form.addEventListener('submit', function (e) {
        if (!baseSelect.value) {
            alert("Base Unit required!");
            e.preventDefault();
            return;
        }

        if (secondarySelect.value && !rateInput.value) {
            alert("Conversion rate required!");
            e.preventDefault();
        }
    });

});
</script>
@endpush