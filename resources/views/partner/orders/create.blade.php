
@extends('layouts.partner')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Create Order</h4>

                    <a href="{{ route('partner.orders') }}"
                        class="btn btn-secondary btn-sm">
                        Back
                    </a>
                </div>

                <div class="card-body">

                    {{-- PRODUCT PREVIEW --}}
                    <div id="productPreview" class="card mb-4 d-none">
                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-md-3 text-center">
                                    <img id="previewImage"
                                        src=""
                                        class="img-fluid rounded border"
                                        style="max-height:180px">
                                </div>

                                <div class="col-md-9">

                                    <h4 id="previewName"></h4>

                                    <div class="row">

                                        <div class="col-md-4">
                                            <strong>Price:</strong>
                                            <span id="previewPrice"></span>
                                        </div>

                                        <div class="col-md-4">
                                            <strong>Barcode:</strong>
                                            <span id="previewBarcode"></span>
                                        </div>

                                        <div class="col-md-4">
                                            <strong>Strength:</strong>
                                            <span id="previewStrength"></span>
                                        </div>

                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <strong>Manufacturer:</strong>
                                            <span id="previewManufacturer"></span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    <form id="orderForm"
                        action="{{ route('partner.orders.store') }}"
                        method="POST">

                        @csrf

                        <div class="row">

                            {{-- COMPANY --}}
                            <div class="col-md-3">
                                <label class="form-label">Company</label>

                                <select id="company_id"
                                    name="company_id"
                                    class="form-control select2">

                                    <option value="">Select Company</option>

                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- PRODUCT --}}
                            <div class="col-md-3">
                                <label class="form-label">Product</label>

                                <select id="product_id"
                                    class="form-control select2"
                                    disabled>

                                    <option value="">Select Company First</option>

                                </select>
                            </div>

                            {{-- UNIT --}}
                            <div class="col-md-2">
                                <label class="form-label">Unit</label>

                                <select id="unit_id"
                                    class="form-control"
                                    disabled>

                                    <option value="">Select Unit</option>

                                </select>
                            </div>

                            {{-- PRICE --}}
                            <div class="col-md-2">
                                <label class="form-label">Price</label>

                                <input type="number"
                                    id="price"
                                    class="form-control"
                                    readonly>
                            </div>

                            {{-- QTY --}}
                            <div class="col-md-2">
                                <label class="form-label">Qty</label>

                                <input type="number"
                                    id="qty"
                                    class="form-control"
                                    value="1"
                                    min="1">
                            </div>

                        </div>

                        <div class="mt-3">
                            <button type="button"
                                id="addItem"
                                class="btn btn-primary">

                                + Add Product

                            </button>
                        </div>

                        <hr>

                        {{-- CART TABLE --}}
                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th width="150">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="cartTable">

                                    <tr>
                                        <td colspan="6" class="text-center">
                                            No items added
                                        </td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                        <div class="text-end mt-3">

                            <button type="submit"
                                class="btn btn-success">

                                Place Order

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {

    $('.select2').select2({
        width: '100%'
    });

});

let cart = [];
let editModeIndex = null;
let availableUnits = [];


/* ================= RESET PRODUCT ================= */

function resetProduct() {

    availableUnits = [];

    $('#unit_id')
        .html('<option value="">Select Unit</option>')
        .prop('disabled', true);

    $('#price').val('');

    $('#productPreview').addClass('d-none');
}


/* ================= COMPANY PRODUCTS ================= */

$('#company_id').on('change', function () {

    let companyId = $(this).val();

    resetProduct();

    if (!companyId) {

        $('#product_id')
            .html('<option value="">Select Company First</option>')
            .prop('disabled', true);

        return;
    }

    $.ajax({

        url: '/partner/orders/company-products/' + companyId,
        type: 'GET',

        beforeSend: function () {

            $('#product_id')
                .html('<option value="">Loading Products...</option>')
                .prop('disabled', true);

        },

        success: function (products) {

            let html = '<option value="">Select Product</option>';

            products.forEach(p => {

                html += `
                    <option value="${p.id}">
                        ${p.name} (${p.strength ?? ''})
                    </option>
                `;

            });

            $('#product_id')
                .html(html)
                .prop('disabled', false);

        },

        error: function () {

            alert('Unable to load products.');

        }

    });

});


/* ================= PRODUCT DETAILS ================= */

$('#product_id').on('change', function () {

    let productId = $(this).val();

    resetProduct();

    if (!productId) {
        return;
    }

    $.ajax({

        url: '/partner/orders/product-details/' + productId,
        type: 'GET',

        beforeSend: function () {

            $('#unit_id')
                .html('<option value="">Loading Units...</option>')
                .prop('disabled', true);

        },

        success: function (p) {

            // Product Preview
            $('#previewImage').attr('src', p.image);
            $('#previewName').text(p.name);

            $('#previewBarcode').text(p.barcode ?? '-');
            $('#previewStrength').text(p.strength ?? '-');
            $('#previewManufacturer').text(p.manufacturer_name ?? '-');

            $('#productPreview').removeClass('d-none');

            // Available Units
            availableUnits = p.units || [];

            let html = '<option value="">Select Unit</option>';

            availableUnits.forEach(unit => {

                html += `
                    <option value="${unit.id}">
                        ${unit.name}
                    </option>
                `;

            });

            $('#unit_id')
                .html(html)
                .prop('disabled', availableUnits.length === 0);

            // Automatically select first available unit
            if (availableUnits.length > 0) {

                $('#unit_id')
                    .val(availableUnits[0].id)
                    .trigger('change');

            } else {

                $('#previewPrice').text('No price available');

                alert('No active price available for this product.');

            }

        },

        error: function () {

            alert('Unable to load product details.');

            resetProduct();

        }

    });

});


/* ================= UNIT CHANGE ================= */

$('#unit_id').on('change', function () {

    let unitId = $(this).val();

    let selectedUnit = availableUnits.find(
        unit => unit.id == unitId
    );

    if (!selectedUnit) {

        $('#price').val('');
        $('#previewPrice').text('');

        return;
    }

    // Unit-wise price
    $('#price').val(selectedUnit.price);

    $('#previewPrice').text(selectedUnit.price);

});


/* ================= ADD ITEM ================= */

$('#addItem').on('click', function () {

    let productId = $('#product_id').val();

    let productName = $('#product_id option:selected').text().trim();

    let unitId = $('#unit_id').val();

    let selectedUnit = availableUnits.find(
        unit => unit.id == unitId
    );

    let price = selectedUnit
        ? parseFloat(selectedUnit.price)
        : 0;

    let qty = parseInt($('#qty').val());

    if (!productId) {

        alert('Please select a product.');

        return;
    }

    if (!selectedUnit || !unitId) {

        alert('Please select a unit.');

        return;
    }

    if (isNaN(qty) || qty < 1) {

        alert('Please enter a valid quantity.');

        return;
    }

    // Same product + same unit
    let existing = cart.find(item =>
        item.id == productId && item.unit_id == unitId
    );

    if (existing) {

        existing.qty += qty;

    } else {

        cart.push({

            id: productId,

            name: productName,

            unit_id: unitId,

            unit_name: selectedUnit.name,

            price: price,

            qty: qty

        });

    }

    renderCart();

    // Reset selection
    $('#product_id').val('').trigger('change');

    resetProduct();

    $('#qty').val(1);

});


/* ================= RENDER CART ================= */

function renderCart() {

    let html = '';
    let total = 0;

    if (cart.length === 0) {

        html = `
            <tr>
                <td colspan="6" class="text-center">
                    No items added
                </td>
            </tr>
        `;

    } else {

        cart.forEach((item, index) => {

            let rowTotal = item.price * item.qty;

            total += rowTotal;

            html += `
                <tr>

                    <td>${item.name}</td>

                    <td>${item.unit_name}</td>

                    <td>${item.price}</td>

                    <td>

                        ${
                            editModeIndex === index
                            ? `
                                <div class="d-flex align-items-center gap-1">

                                    <button type="button"
                                        class="btn btn-sm btn-secondary"
                                        onclick="changeQty(${index}, -1)">
                                        -
                                    </button>

                                    <input type="number"
                                        class="form-control form-control-sm text-center"
                                        style="width:60px"
                                        value="${item.qty}"
                                        min="1"
                                        onchange="setQty(${index}, this.value)">

                                    <button type="button"
                                        class="btn btn-sm btn-secondary"
                                        onclick="changeQty(${index}, 1)">
                                        +
                                    </button>

                                </div>
                            `
                            : `<span>${item.qty}</span>`
                        }

                    </td>

                    <td>${rowTotal}</td>

                    <td>

                        <button type="button"
                            class="btn btn-warning btn-sm me-1"
                            onclick="editItem(${index})">

                            ${editModeIndex === index ? 'Close' : 'Edit'}

                        </button>

                        <button type="button"
                            class="btn btn-danger btn-sm"
                            onclick="removeItem(${index})">

                            X

                        </button>

                    </td>

                </tr>
            `;

        });

        html += `
            <tr>

                <td colspan="4" class="text-end">
                    <b>Total</b>
                </td>

                <td colspan="2">
                    <b>${total.toFixed(2)}</b>
                </td>

            </tr>
        `;

    }

    $('#cartTable').html(html);

    // Update hidden cart input
    $('#orderForm').find('input[name="cart"]').remove();

    $('<input>')
        .attr({
            type: 'hidden',
            name: 'cart',
            value: JSON.stringify(cart)
        })
        .appendTo('#orderForm');

}


/* ================= EDIT MODE ================= */

function editItem(index) {

    editModeIndex = (editModeIndex === index)
        ? null
        : index;

    renderCart();

}


/* ================= REMOVE ================= */

function removeItem(index) {

    cart.splice(index, 1);

    if (editModeIndex === index) {

        editModeIndex = null;

    }

    renderCart();

}


/* ================= QTY CONTROL ================= */

function changeQty(index, val) {

    if (editModeIndex !== index) return;

    cart[index].qty += val;

    if (cart[index].qty < 1) {

        cart[index].qty = 1;

    }

    renderCart();

}


function setQty(index, val) {

    if (editModeIndex !== index) return;

    let qty = parseInt(val);

    if (isNaN(qty) || qty < 1) {

        qty = 1;

    }

    cart[index].qty = qty;

    renderCart();

}


/* ================= FORM SUBMIT ================= */

$('#orderForm').on('submit', function (e) {

    if (cart.length === 0) {

        e.preventDefault();

        alert('Please add at least one product.');

        return false;

    }

});

</script>

@endpush
