@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Create Purchase</h4>

                    <a href="{{ route('purchases.index') }}"
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
                                            <strong>Stock:</strong>
                                            <span id="previewStock"></span>
                                        </div>

                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <strong>Category:</strong>
                                            <span id="previewCategory"></span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    {{-- ১. ফর্মের উপরে গ্লোবাল সাকসেস বা এরর মেসেজ দেখানোর ব্যবস্থা --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    {{-- যদি কার্ট বা অন্য কোনো গ্লোবাল ভ্যালিডেশন এরর থাকে --}}
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('purchases.store') }}" method="POST">
                        @csrf

                        <div class="row">

                            {{-- PURCHASE DATE --}}
                            <div class="col-md-4">
                                <label class="form-label">Purchase Date</label>
                                <input type="date"
                                    name="purchase_date"
                                    class="form-control"
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            {{-- SUPPLIER --}}
                            <div class="col-md-4">
                                <label class="form-label">Supplier</label>

                                <select name="supplier_id"
                                    class="form-control select2">

                                    <option value="">Select Supplier</option>

                                    @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}">
                                        {{ $supplier->code }} - {{ $supplier->name }}
                                    </option>
                                    @endforeach

                                </select>
                            </div>

                        </div>

                        <hr>

                        <div class="row mt-3">

                            {{-- COMPANY --}}
                            <div class="col-md-4">
                                <label class="form-label">Company</label>

                                <select id="company_id"
                                    class="form-control select2">

                                    <option value="">Select Company</option>

                                    @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}">
                                        {{ $company->company_name }}
                                    </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Category</label>

                                <select id="category_id"
                                    class="form-control select2"
                                    disabled>

                                    <option value="">Select Company First</option>

                                </select>
                            </div>

                            {{-- PRODUCT --}}
                            <div class="col-md-4">
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

                                <select id="unit_id" class="form-control">
                                    <option value="">Select Unit</option>
                                </select>
                            </div>

                            {{-- PRICE --}}
                            <div class="col-md-2">
                                <label class="form-label">Price</label>
                                <input type="number"
                                    id="price"
                                    class="form-control">
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
                                        <td colspan="5" class="text-center">
                                            No items added
                                        </td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                        <div class="text-end mt-3">
                            <button type="submit"
                                class="btn btn-success">
                                Save Purchase
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
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
    });

    let cart = [];

    /* ================= RESET FUNCTIONS ================= */
    function resetProduct() {
        $('#product_id').html('<option value="">Select Category First</option>').prop('disabled', true);
        $('#unit_id').html('<option value="">Select Unit</option>');
        $('#price').val('');
        $('#qty').val(1);
    }

    /* ================= COMPANY → CATEGORY ================= */
    $('#company_id').on('change', function() {
        let companyId = $(this).val();

        $('#productPreview').addClass('d-none');

        $('#category_id').html('<option value="">Loading...</option>').prop('disabled', true);
        resetProduct();

        if (!companyId) return;

        let url = "{{ route('purchases.company-categories', ':id') }}";
        url = url.replace(':id', companyId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(categories) {

                let html = '<option value="">Select Category</option>';

                categories.forEach(c => {
                    html += `<option value="${c.id}">${c.name}</option>`;
                });

                $('#category_id')
                    .html(html)
                    .prop('disabled', false)
                    .trigger('change.select2');
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });


    /* ================= CATEGORY → PRODUCT ================= */
    $('#category_id').on('change', function() {
        let companyId = $('#company_id').val();
        let categoryId = $(this).val();

        $('#productPreview').addClass('d-none');
        resetProduct();

        if (!categoryId) return;

        let url = "{{ route('purchases.company-products', [':company', ':category']) }}";
        url = url.replace(':company', companyId).replace(':category', categoryId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(products) {

                let html = '<option value="">Select Product</option>';

                products.forEach(p => {
                    html += `<option value="${p.id}">
                            ${p.name} ${p.strength ? '- ' + p.strength : ''}
                         </option>`;
                });

                $('#product_id')
                    .html(html)
                    .prop('disabled', false)
                    .trigger('change.select2');
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });


    /* ================= PRODUCT DETAILS ================= */
    $('#product_id').on('change', function() {
        let productId = $(this).val();

        if (!productId) {
            $('#productPreview').addClass('d-none');
            return;
        }

        let url = "{{ route('purchases.product-details', ':id') }}";
        url = url.replace(':id', productId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(p) {

                $('#price').val(p.price);

                $('#previewImage').attr('src', p.image);
                $('#previewName').text(p.name);
                $('#previewPrice').text(p.price);
                $('#previewBarcode').text(p.barcode ?? '-');
                $('#previewStock').text('-');
                $('#previewCategory').text(p.manufacturer_name ?? '-');

                /* ================= UNIT ================= */
                let unitHtml = '';

                if (p.secondary_unit_id && p.secondary_unit_name) {
                    unitHtml += `<option value="${p.base_unit_id}">
                                ${p.base_unit_name}
                             </option>`;

                    unitHtml += `<option value="${p.secondary_unit_id}">
                                ${p.secondary_unit_name}
                             </option>`;
                } else {
                    unitHtml += `<option value="${p.base_unit_id}">
                                ${p.base_unit_name}
                             </option>`;
                }

                $('#unit_id').html(unitHtml);

                $('#productPreview').removeClass('d-none');
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });


    /* ================= ADD ITEM ================= */
    $('#addItem').on('click', function() {

        let productId = $('#product_id').val();
        let productName = $('#product_id option:selected').text();

        let price = Number($('#price').val() || 0);
        let qty = Number($('#qty').val() || 0);

        let unitId = $('#unit_id').val();
        let unitText = $('#unit_id option:selected').text();

        if (!productId) return alert('Please select product');
        if (!unitId) return alert('Please select unit');
        if (price <= 0) return alert('Invalid price');
        if (qty <= 0) return alert('Invalid quantity');

        let existing = cart.find(i =>
            i.product_id == productId && i.unit_id == unitId
        );

        if (existing) {
            existing.quantity += qty;
            existing.price = price;
        } else {
            cart.push({
                product_id: productId,
                name: productName,
                price: price,
                quantity: qty,
                unit_id: unitId,
                unit_name: unitText
            });
        }

        renderCart();

        $('#product_id').val('').trigger('change');
        $('#price').val('');
        $('#qty').val(1);
        $('#unit_id').html('<option value="">Select Unit</option>');
    });


    /* ================= CART RENDER ================= */
    function renderCart() {

        let html = '';
        let total = 0;

        if (cart.length === 0) {
            html = `<tr><td colspan="6" class="text-center">No items added</td></tr>`;
        } else {

            cart.forEach((item, index) => {

                let rowTotal = item.price * item.quantity;
                total += rowTotal;

                html += `
            <tr>
                <td>${item.name}</td>
                <td>${item.unit_name}</td>
                <td>${item.price}</td>
                <td>${item.quantity}</td>
                <td>${rowTotal.toFixed(2)}</td>
                <td>
                    <button type="button"
                        class="btn btn-danger btn-sm"
                        onclick="removeItem(${index})">
                        X
                    </button>
                </td>
            </tr>`;
            });

            html += `
        <tr>
            <td colspan="4" class="text-end"><b>Total</b></td>
            <td colspan="2"><b>${total.toFixed(2)}</b></td>
        </tr>`;
        }

        $('#cartTable').html(html);

        $('form').find('input[name="items"]').remove();

        $('<input>')
            .attr({
                type: 'hidden',
                name: 'items',
                value: JSON.stringify(cart)
            })
            .appendTo('form');
    }


    /* ================= REMOVE ITEM ================= */
    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }
</script>

@endpush