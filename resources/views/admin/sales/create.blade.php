@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Create Sale</h4>

                    <a href="{{ route('sales.index') }}"
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
                                            <strong>Strength:</strong>
                                            <span id="previewStrength"></span>
                                        </div>

                                        <div class="col-md-4">
                                            <strong>Stock:</strong>
                                            <span id="previewStock"></span>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    {{-- ALERTS --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('sales.store') }}"
                        method="POST">

                        @csrf

                        <div class="row">

                            {{-- SALE DATE --}}
                            <div class="col-md-4">
                                <label class="form-label">
                                    Sale Date
                                </label>

                                <input type="date"
                                    name="sale_date"
                                    class="form-control"
                                    value="{{ old('sale_date', date('Y-m-d')) }}">
                            </div>

                            {{-- CUSTOMER --}}
                            <div class="col-md-4">
                                <label class="form-label">Customer Type</label>

                                <select id="customer_type_id"
                                    class="form-control select2">

                                    <option value="">Select Type</option>

                                    @foreach($userTypes as $type)
                                    <option value="{{ $type->id }}">
                                        {{ $type->name }}
                                    </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Customer</label>

                                <select name="customer_id"
                                    id="customer_id"
                                    class="form-control select2">

                                    <option value="">
                                        Select Customer Type First
                                    </option>

                                </select>
                            </div>

                        </div>

                        <hr>

                        <div class="row mt-3">

                            {{-- COMPANY --}}
                            <div class="col-md-4">
                                <label class="form-label">
                                    Company
                                </label>

                                <select id="company_id"
                                    class="form-control select2">

                                    <option value="">
                                        Select Company
                                    </option>

                                    @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}">
                                        {{ $company->company_name }}
                                    </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- CATEGORY --}}
                            <div class="col-md-4">
                                <label class="form-label">
                                    Category
                                </label>

                                <select id="category_id"
                                    class="form-control select2"
                                    disabled>

                                    <option value="">
                                        Select Company First
                                    </option>

                                </select>
                            </div>

                            {{-- PRODUCT --}}
                            <div class="col-md-4">
                                <label class="form-label">
                                    Product
                                </label>

                                <select id="product_id"
                                    class="form-control select2"
                                    disabled>

                                    <option value="">
                                        Select Company First
                                    </option>

                                </select>
                            </div>

                            {{-- UNIT --}}
                            <div class="col-md-2">
                                <label class="form-label">
                                    Unit
                                </label>

                                <select id="unit_id"
                                    class="form-control">

                                    <option value="">
                                        Select Unit
                                    </option>

                                </select>
                            </div>

                            {{-- PRICE --}}
                            <div class="col-md-2">
                                <label class="form-label">
                                    Price
                                </label>

                                <input type="number"
                                    id="price"
                                    class="form-control">
                            </div>

                            {{-- QTY --}}
                            <div class="col-md-2">
                                <label class="form-label">
                                    Qty
                                </label>

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
                                        <th width="150">
                                            Action
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="cartTable">

                                    <tr>
                                        <td colspan="6"
                                            class="text-center">
                                            No items added
                                        </td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                        <div class="text-end mt-3">

                            <button type="submit"
                                class="btn btn-success">

                                Save Sale

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
<script>
    $(document).ready(function() {

        $('.select2').select2({
            width: '100%'
        });

        let rowIndex = 0;

        /*
        |--------------------------------------------------------------------------
        | Company Change
        |--------------------------------------------------------------------------
        */
        $('#company_id').change(function() {

            let companyId = $(this).val();

            if (!companyId) {
                return;
            }

            $('#category_id').html(
                '<option value="">Loading...</option>'
            );

            $.get(
                "{{ url('em_admin/sales/company-categories') }}/" + companyId,
                function(categories) {

                    let options =
                        '<option value="">Select Category</option>';

                    $.each(categories, function(i, item) {

                        options += `
                    <option value="${item.id}">
                        ${item.name}
                    </option>
                `;

                    });

                    $('#category_id')
                        .html(options)
                        .prop('disabled', false);

                    $('#product_id').html(
                        '<option value="">Select Category First</option>'
                    );

                }
            );

        });

        /*
        |--------------------------------------------------------------------------
        | Category Change
        |--------------------------------------------------------------------------
        */
        $('#category_id').change(function() {

            let categoryId = $(this).val();
            let companyId = $('#company_id').val();

            if (!companyId) {
                return;
            }

            $('#product_id').html(
                '<option value="">Loading...</option>'
            );

            let url =
                "{{ url('em_admin/sales/company-products') }}/" +
                companyId;

            if (categoryId) {
                url += '/' + categoryId;
            }

            $.get(url, function(products) {

                let options =
                    '<option value="">Select Product</option>';

                $.each(products, function(i, item) {

                    options += `
                <option value="${item.id}">
                    ${item.name} ${item.strength} 
                </option>
            `;

                });

                $('#product_id')
                    .html(options)
                    .prop('disabled', false);

            });

        });

        /*
        |--------------------------------------------------------------------------
        | Product Change
        |--------------------------------------------------------------------------
        */

        $('#product_id').change(function() {

            let productId = $(this).val();
            let customerTypeId = $('#customer_type_id').val();

            if (!productId) {
                return;
            }

            if (!customerTypeId) {
                alert('Please select customer type first');
                $('#product_id').val('').trigger('change');
                return;
            }

            $.get(
                "{{ url('em_admin/sales/product-details') }}/" + productId, {
                    user_type_id: customerTypeId
                },
                function(p) {

                    /* ================= UNIT ================= */

                    let unitHtml = '';

                    unitHtml += `
                <option value="${p.base_unit_id}">
                    ${p.base_unit_name}
                </option>
            `;

                    if (p.secondary_unit_id && p.secondary_unit_name) {

                        unitHtml += `
                    <option value="${p.secondary_unit_id}">
                        ${p.secondary_unit_name}
                    </option>
                `;

                    }

                    $('#unit_id').html(unitHtml);

                    /* ================= PRICE ================= */

                    $('#price').val(p.price ?? 0);

                    /* ================= PREVIEW ================= */

                    $('#previewName').text(p.product_name ?? '');

                    $('#previewPrice').text(p.price ?? 0);

                    $('#previewStrength').text(p.strength ?? '-');

                    $('#previewStock').text(p.stock ?? 0);

                    if (p.image) {
                        $('#previewImage').attr('src', '/storage/' + p.image);
                    } else {
                        $('#previewImage').attr('src', '');
                    }

                    $('#productPreview').removeClass('d-none');

                    // Unit load হওয়ার পরে আবার price load হবে
                    loadPrice();

                }
            );

        });

        $('#customer_type_id').change(function() {

            let typeId = $(this).val();

            $('#customer_id').html(
                '<option value="">Loading...</option>'
            );

            $.get(
                "{{ route('sales.customers-by-type') }}", {
                    type_id: typeId
                },
                function(customers) {

                    let options =
                        '<option value="">Select Customer</option>';

                    $.each(customers, function(i, customer) {

                        options += `
                    <option value="${customer.id}">
                        ${customer.name}
                    </option>
                `;

                    });

                    $('#customer_id').html(options);

                }
            );

        });

        $('#unit_id').change(function() {
            loadPrice();
        });

        /*
        |--------------------------------------------------------------------------
        | Add Item
        |--------------------------------------------------------------------------
        */
        $('#addItem').click(function() {

            let productId =
                $('#product_id').val();

            let productName =
                $('#product_id option:selected').text();

            let unitId =
                $('#unit_id').val();

            let unitName =
                $('#unit_id option:selected').text();

            let price =
                parseFloat($('#price').val()) || 0;

            let qty =
                parseFloat($('#qty').val()) || 0;

            if (!productId) {

                alert('Select Product');

                return;

            }

            if (qty <= 0) {

                alert('Enter Quantity');

                return;

            }

            let total = price * qty;

            if (
                $('#cartTable tr td[colspan="6"]').length
            ) {
                $('#cartTable').html('');
            }

            let row = `
        <tr>

            <td>

                ${productName}

                <input type="hidden"
                    name="items[${rowIndex}][product_id]"
                    value="${productId}">

            </td>

            <td>

                ${unitName}

                <input type="hidden"
                    name="items[${rowIndex}][unit_id]"
                    value="${unitId}">

            </td>

            <td>

                ${price}

                <input type="hidden"
                    name="items[${rowIndex}][price]"
                    value="${price}">

            </td>

            <td>

                ${qty}

                <input type="hidden"
                    name="items[${rowIndex}][quantity]"
                    value="${qty}">

            </td>

            <td class="line-total">

                ${total.toFixed(2)}

            </td>

            <td>

                <button type="button"
                    class="btn btn-danger btn-sm remove-row">

                    Remove

                </button>

            </td>

        </tr>
        `;

            $('#cartTable').append(row);

            rowIndex++;

            calculateGrandTotal();

        });

        /*
        |--------------------------------------------------------------------------
        | Remove Row
        |--------------------------------------------------------------------------
        */
        $(document).on(
            'click',
            '.remove-row',
            function() {

                $(this)
                    .closest('tr')
                    .remove();

                if (
                    $('#cartTable tr').length == 0
                ) {

                    $('#cartTable').html(`
                    <tr>
                        <td colspan="6"
                            class="text-center">
                            No items added
                        </td>
                    </tr>
                `);

                }

                calculateGrandTotal();

            }
        );

        /*
        |--------------------------------------------------------------------------
        | Grand Total
        |--------------------------------------------------------------------------
        */
        function calculateGrandTotal() {

            let grandTotal = 0;

            $('.line-total').each(function() {

                grandTotal +=
                    parseFloat($(this).text()) || 0;

            });

            $('#grandTotal').text(
                grandTotal.toFixed(2)
            );

        }

    });

    function loadPrice() {

        let productId = $('#product_id').val();
        let unitId = $('#unit_id').val();
        let userTypeId = $('#customer_type_id').val();

        if (!productId || !unitId || !userTypeId) {
            return;
        }

        $.get(
            "{{ route('sales.product-price') }}", {
                product_id: productId,
                unit_id: unitId,
                user_type_id: userTypeId
            },
            function(res) {

                $('#price').val(res.price);
                $('#previewPrice').text(res.price);

            }
        );

    }
</script>
@endpush