@extends('layouts.website')

@section('title', 'Order Request')

@section('content')

<!-- CSRF -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- PAGE HEADER -->
<div class="container-fluid page-header py-5"
    style="background: url('{{ asset('website/img/cart-page-header-img.jpg') }}') center/cover no-repeat;">
    <h1 class="text-center text-white display-6">Order Request</h1>
</div>

<div class="container py-5">

    {{-- FORM --}}
    <div class="bg-light p-4 rounded mb-4">
        <form id="orderForm">

            {{-- TYPE --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Type</label>
                    <select id="type" class="form-select" onchange="toggleType()">
                        <option value="general">General</option>
                        <option value="prescription">Prescription</option>
                    </select>
                </div>
            </div>

            {{-- GENERAL --}}
            <div class="row g-3 general-field">

                <div class="col-md-3">
                    <input type="text" name="name" placeholder="Product name" class="form-control">
                </div>

                <div class="col-md-3">
                    <input type="text" name="strength" placeholder="Strength" class="form-control">
                </div>

                <div class="col-md-3">
                    <select name="unit" class="form-select">
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ ucfirst($unit->title) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="number" name="quantity" value="1" min="1" class="form-control">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" id="addBtn" class="btn btn-primary w-100">+</button>
                </div>

            </div>

            {{-- PRESCRIPTION --}}
            <div class="row g-3 prescription-field d-none mt-2">

                <div class="col-md-6">
                    <input type="file" name="prescription" class="form-control">
                </div>

                <div class="col-md-6">
                    <textarea name="description" placeholder="Description" class="form-control"></textarea>
                </div>

            </div>

        </form>
    </div>

    {{-- TABLE --}}
    <div id="generalSection">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Strength</th>
                    <th>Unit</th>
                    <th>Qty</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>

        <div class="text-end">
            <button class="btn btn-primary" id="continueBtn">Continue</button>
        </div>
    </div>

    {{-- PRESCRIPTION BTN --}}
    <div id="prescriptionCheckout" class="d-none text-center">
        <button class="btn btn-primary" id="continuePrescriptionBtn">Continue</button>
    </div>

</div>

{{-- MODAL --}}
<div class="modal fade" id="customerModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">

            <h5>Customer Info</h5>

            <input id="customerName" class="form-control mb-2" placeholder="Name">
            <input id="customerPhone" class="form-control mb-2" placeholder="Phone">
            <input id="customerEmail" class="form-control mb-2" placeholder="Email">
            <textarea id="customerAddress" class="form-control mb-2" placeholder="Address"></textarea>

            <button class="btn btn-success" id="sendRequestBtn">Submit</button>

        </div>
    </div>
</div>

@endsection


@push('scripts')

<script>

let cart = [];

/* ================= TYPE TOGGLE ================= */
function toggleType() {
    let type = document.getElementById('type').value;

    document.querySelector('.general-field').classList.toggle('d-none', type !== 'general');
    document.querySelector('.prescription-field').classList.toggle('d-none', type !== 'prescription');

    document.getElementById('generalSection').classList.toggle('d-none', type !== 'general');
    document.getElementById('prescriptionCheckout').classList.toggle('d-none', type !== 'prescription');
}

/* ================= ADD ITEM ================= */
document.getElementById('addBtn').addEventListener('click', function () {

    let name = document.querySelector('input[name="name"]').value.trim();
    let strength = document.querySelector('input[name="strength"]').value.trim();
    let unit = document.querySelector('select[name="unit"]').value;
    let unitText = document.querySelector('select[name="unit"] option:checked').text;
    let qty = parseInt(document.querySelector('input[name="quantity"]').value);

    if (!name || !unit || qty < 1) {
        alert("All fields required");
        return;
    }

    cart.push({ name, strength, unit, unitText, qty });
    renderTable();

    document.getElementById('orderForm').reset();
    document.querySelector('input[name="quantity"]').value = 1;
});

/* ================= TABLE ================= */
function renderTable() {
    let tbody = document.getElementById('tableBody');

    if (cart.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center">No items</td></tr>`;
        return;
    }

    tbody.innerHTML = "";

    cart.forEach((item, index) => {
        tbody.innerHTML += `
        <tr>
            <td>${item.name}</td>
            <td>${item.strength || '-'}</td>
            <td>${item.unitText}</td>
            <td>${item.qty}</td>
            <td><button onclick="removeItem(${index})" class="btn btn-danger btn-sm">X</button></td>
        </tr>`;
    });
}

/* ================= REMOVE ================= */
function removeItem(index) {
    cart.splice(index, 1);
    renderTable();
}

/* ================= MODAL ================= */
document.getElementById('continueBtn').onclick = () => {
    if (cart.length === 0) return alert("Add items first");
    new bootstrap.Modal(document.getElementById('customerModal')).show();
};

document.getElementById('continuePrescriptionBtn').onclick = () => {
    new bootstrap.Modal(document.getElementById('customerModal')).show();
};

/* ================= SUBMIT ================= */
document.getElementById('sendRequestBtn').addEventListener('click', function () {

    let type = document.getElementById('type').value;

    let customer = {
        name: document.getElementById('customerName').value.trim(),
        phone: document.getElementById('customerPhone').value.trim(),
        email: document.getElementById('customerEmail').value.trim(),
        address: document.getElementById('customerAddress').value.trim(),
    };

    if (!customer.name || !customer.phone) {
        alert("Name & phone required");
        return;
    }

    let formData = new FormData();

    formData.append('type', type);
    formData.append('customer', JSON.stringify(customer));
    formData.append('cart', JSON.stringify(cart));

    let fileInput = document.querySelector('input[name="prescription"]');
    let description = document.querySelector('textarea[name="description"]').value;

    if (type === 'prescription') {
        if (!fileInput.files.length) {
            alert("Upload prescription");
            return;
        }

        formData.append('prescription', fileInput.files[0]);
        formData.append('description', description);
    }

    fetch("{{ route('order.request.store') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);

        if (data.success) {
            cart = [];
            renderTable();
            document.getElementById('orderForm').reset();
            bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
        }
    })
    .catch(() => alert("Error occurred"));
});

/* INIT */
document.addEventListener("DOMContentLoaded", toggleType);

</script>

@endpush