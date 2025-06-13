@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Inventory Movement</h3>
                    <div class="card-tools">
                        <a href="{{ route('staff.inventory.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.inventory.update', $movement) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror" required>
                                <option value="">Select a product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            {{ (old('product_id', $movement->product_id) == $product->id) ? 'selected' : '' }}>
                                        {{ $product->name }} (Current Stock: {{ $product->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="type">Movement Type</label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Select type</option>
                                <option value="in" {{ old('type', $movement->type) == 'in' ? 'selected' : '' }}>Stock In</option>
                                <option value="out" {{ old('type', $movement->type) == 'out' ? 'selected' : '' }}>Stock Out</option>
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" 
                                   name="quantity" 
                                   id="quantity" 
                                   class="form-control @error('quantity') is-invalid @enderror" 
                                   value="{{ old('quantity', $movement->quantity) }}" 
                                   min="1" 
                                   step="0.01" 
                                   required>
                            @error('quantity')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reference_type">Reference Type</label>
                            <select name="reference_type" id="reference_type" class="form-control @error('reference_type') is-invalid @enderror" required>
                                <option value="">Select reference type</option>
                                <option value="order" {{ old('reference_type', $movement->reference_type) == 'order' ? 'selected' : '' }}>Order</option>
                                <option value="purchase" {{ old('reference_type', $movement->reference_type) == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                <option value="adjustment" {{ old('reference_type', $movement->reference_type) == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                            </select>
                            @error('reference_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group reference-id-group" style="display: none;">
                            <label for="reference_id">Reference ID</label>
                            <input type="number" 
                                   name="reference_id" 
                                   id="reference_id" 
                                   class="form-control @error('reference_id') is-invalid @enderror" 
                                   value="{{ old('reference_id', $movement->reference_id) }}" 
                                   min="1">
                            @error('reference_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" 
                                      id="notes" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3">{{ old('notes', $movement->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Movement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceTypeSelect = document.getElementById('reference_type');
        const referenceIdGroup = document.querySelector('.reference-id-group');
        const referenceIdInput = document.getElementById('reference_id');

        function toggleReferenceId() {
            const selectedType = referenceTypeSelect.value;
            if (selectedType === 'order' || selectedType === 'purchase') {
                referenceIdGroup.style.display = 'block';
                referenceIdInput.required = true;
            } else {
                referenceIdGroup.style.display = 'none';
                referenceIdInput.required = false;
                referenceIdInput.value = '';
            }
        }

        referenceTypeSelect.addEventListener('change', toggleReferenceId);
        toggleReferenceId(); // Initial state
    });
</script>
@endpush
@endsection 