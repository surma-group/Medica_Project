@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Products</h4>
                    <div>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
                        <a href="{{ route('products.import') }}" class="btn btn-success ml-2">Import Product</a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Barcode</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Status</th>
                                    <th style="width:220px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="50" height="50" class="rounded">
                                        @else
                                        <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->barcode }}</td>
                                    <td>{{ $product->category->name ?? '-' }}</td>
                                    <td>{{ $product->brand->name ?? '-' }}</td>
                                    <td>
                                        @if($product->status)
                                        <div class="badge badge-success">Active</div>
                                        @else
                                        <div class="badge badge-danger">Inactive</div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-primary">Edit</a>

                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>

                                        <!-- Set Unit Button -->
                                        <button type="button"
                                            class="btn btn-sm btn-info set-unit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#setUnitModal"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-base="{{ $product->base_unit_id ?? '' }}"
                                            data-secondary="{{ $product->secondary_unit_id ?? '' }}"
                                            data-rate="{{ $product->conversion_rate ?? '' }}">
                                            Set Unit
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No products found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Single Set Unit Modal -->
<div class="modal fade" id="setUnitModal" tabindex="-1" aria-labelledby="setUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="setUnitModalLabel">Set Units</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="setUnitForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="baseUnit" class="form-label">Base Unit</label>
                        <select class="form-select" id="baseUnit" name="base_unit_id">
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="secondaryUnit" class="form-label">Secondary Unit</label>
                        <select class="form-select" id="secondaryUnit" name="secondary_unit_id">
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="conversionRate" class="form-label">Conversion Rate</label>
                        <input type="text" class="form-control" id="conversionRate" name="conversion_rate">
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('setUnitModal');
        const form = document.getElementById('setUnitForm');
        const baseSelect = document.getElementById('baseUnit');
        const secondarySelect = document.getElementById('secondaryUnit');
        const conversionRateInput = document.getElementById('conversionRate');

        // Validate Base and Secondary units are not the same
        function validateUnits() {
            if (baseSelect.value && secondarySelect.value && baseSelect.value === secondarySelect.value) {
                alert("Base Unit and Secondary Unit cannot be the same!");
                secondarySelect.value = '';
            }
        }

        baseSelect.addEventListener('change', validateUnits);
        secondarySelect.addEventListener('change', validateUnits);

        // Modal populate
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const base = button.getAttribute('data-base');
            const secondary = button.getAttribute('data-secondary');
            const rate = button.getAttribute('data-rate');

            modal.querySelector('.modal-title').textContent = `Set Units for ${name}`;
            form.action = "{{ route('products.unit.set', ['product' => 'PRODUCT_ID']) }}".replace('PRODUCT_ID', id);

            baseSelect.value = base ?? '';
            secondarySelect.value = secondary ?? '';
            conversionRateInput.value = rate ?? '';
        });

        // Form validation on submit
        form.addEventListener('submit', function(e) {
            // Base Unit required
            if (!baseSelect.value) {
                alert("Base Unit is required!");
                baseSelect.focus();
                e.preventDefault();
                return false;
            }

            // If Secondary Unit is selected, Conversion Rate is required
            if (secondarySelect.value && !conversionRateInput.value) {
                alert("Conversion Rate is required when a Secondary Unit is selected!");
                conversionRateInput.focus();
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush