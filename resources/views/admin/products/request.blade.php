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

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Products Request</h4>

                    <div>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
                        <a href="{{ route('products.import') }}" class="btn btn-success">Import Product</a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table id="productTable" class="table table-striped table-md w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Strength</th>
                                    <th>Category</th>
                                    <th>Generic Name</th>
                                    <th>Company</th>
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
                        <label>Conversion Quantity</label>
                        <input type="text" class="form-control" id="conversionRate" name="conversion_rate">
                    </div>

                    <button class="btn btn-primary w-100">Save</button>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- ================= PRICE MODAL ================= --}}
<div class="modal fade" id="setPriceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Set Price - <span id="productName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="setPriceForm" method="POST">
                    @csrf

                    @foreach($userTypes as $type)
                    <div class="mb-3">
                        <label>{{ $type->name }} Price</label>

                        <input type="hidden" name="user_type_id[]" value="{{ $type->id }}">

                        <input type="number"
                            step="0.01"
                            name="price[]"
                            class="form-control"
                            placeholder="Enter {{ $type->name }} price">
                    </div>
                    @endforeach

                    <div class="mb-3">
                        <label>Currency</label>
                        <select class="form-select" name="currency">
                            <option value="1">BDT</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100">Save</button>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- ================= COMMISSION MODAL ================= --}}
<div class="modal fade" id="setCommissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Set Commission - <span id="commissionProductName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="setCommissionForm" method="POST">
                    @csrf

                    @foreach($userTypes as $type)
                    <div class="border rounded p-3 mb-3">

                        <label class="fw-bold">{{ $type->name }}</label>

                        <input type="hidden" name="user_type_id[]" value="{{ $type->id }}">

                        <input type="number"
                            step="0.01"
                            name="commission[]"
                            class="form-control mt-2"
                            placeholder="Commission">

                        <select name="commission_type[]" class="form-select mt-2">
                            <option value="{{ COMMISSION_TYPE_FLAT }}">Flat</option>
                            <option value="{{ COMMISSION_TYPE_PERCENTAGE }}">Percentage (%)</option>
                        </select>

                    </div>
                    @endforeach

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
document.addEventListener('DOMContentLoaded', function () {

    // ================= PRICE MODAL =================
    const priceModal = document.getElementById('setPriceModal');

    priceModal.addEventListener('show.bs.modal', function (event) {

        const btn = event.relatedTarget;

        const name = btn.getAttribute('data-name');
        const url = btn.getAttribute('data-url');
        let prices = btn.getAttribute('data-prices');

        document.getElementById('setPriceForm').action = url;
        document.getElementById('productName').innerText = name;

        document.getElementById('setPriceForm').reset();

        try {
            prices = prices ? JSON.parse(prices) : [];
        } catch (e) {
            prices = [];
        }

        const inputs = document.querySelectorAll('#setPriceForm input[name="price[]"]');

        prices.forEach((item, i) => {
            if (inputs[i]) inputs[i].value = item.price ?? '';
        });
    });

    // ================= COMMISSION MODAL =================
    const commissionModal = document.getElementById('setCommissionModal');

    commissionModal.addEventListener('show.bs.modal', function (event) {

        const btn = event.relatedTarget;

        const name = btn.getAttribute('data-name');
        const url = btn.getAttribute('data-url');
        let commissions = btn.getAttribute('data-commissions');

        document.getElementById('setCommissionForm').action = url;
        document.getElementById('commissionProductName').innerText = name;

        document.getElementById('setCommissionForm').reset();

        try {
            commissions = commissions ? JSON.parse(commissions) : [];
        } catch (e) {
            commissions = [];
        }

        const values = document.querySelectorAll('#setCommissionForm input[name="commission[]"]');
        const types = document.querySelectorAll('#setCommissionForm select[name="commission_type[]"]');

        commissions.forEach((item, i) => {

            if (values[i]) {
                values[i].value = item.commission ?? '';
            }

            if (types[i]) {
                types[i].value = item.commission_type ?? "{{ COMMISSION_TYPE_FLAT }}";
            }
        });
    });

    // ================= UNIT MODAL =================
    const modal = document.getElementById('setUnitModal');
    const form = document.getElementById('setUnitForm');

    const baseSelect = document.getElementById('baseUnit');
    const secondarySelect = document.getElementById('secondaryUnit');
    const rateInput = document.getElementById('conversionRate');

    modal.addEventListener('show.bs.modal', function (event) {

        const btn = event.relatedTarget;
        if (!btn) return;

        form.action = btn.getAttribute('data-url');

        baseSelect.value = btn.getAttribute('data-base') || '';
        secondarySelect.value = btn.getAttribute('data-secondary') || '';
        rateInput.value = btn.getAttribute('data-rate') || '';

        modal.querySelector('.modal-title').innerText =
            "Set Units for " + (btn.getAttribute('data-name') || '');
    });

    // ================= DATATABLE =================
    $('#productTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('products.request') }}",
        pageLength: 25,
        stateSave: true,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: 'Excel',
            exportOptions: { columns: ':not(:last-child)' }
        }],
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'image', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'strength' },
            { data: 'category' },
            { data: 'generic' },
            { data: 'company' },
            { data: 'status', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

});
</script>
@endpush