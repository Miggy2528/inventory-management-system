@extends('layouts.butcher')

@section('content')
<div class="container py-4">
    <div class="row">
        @forelse($meatCuts as $cut)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    @if($cut->image_path)
                        <img src="{{ Storage::url($cut->image_path) }}" alt="{{ $cut->name }}" class="card-img-top" style="object-fit:cover;max-height:180px;">
                    @else
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height:180px;">
                            <span class="text-muted">No image</span>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title mb-1">{{ $cut->name }}</h5>
                        <div class="mb-2">
                            <span class="badge bg-secondary">{{ ucfirst($cut->animal_type) }}</span>
                            <span class="badge bg-info text-dark">{{ ucfirst($cut->cut_type) }}</span>
                        </div>
                        <p class="mb-1"><strong>Price:</strong> ₱{{ number_format(($cut->is_packaged ? ($cut->package_price ?? 0) : ($cut->default_price_per_kg ?? 0)), 2) }}</p>
                        <div class="text-muted small mb-2">
                            {{ $cut->is_packaged ? 'Sold per package' : 'Sold by kg' }}
                        </div>
                        <p class="mb-1"><strong>Quantity:</strong> {{ $cut->quantity ?? 0 }}</p>
                        <p class="mb-1"><strong>Status:</strong> <span class="badge {{ $cut->is_available ? 'bg-success' : 'bg-danger' }}">{{ $cut->is_available ? 'Available' : 'Not Available' }}</span></p>
                        <p class="mb-1"><strong>Min. Stock:</strong> {{ $cut->minimum_stock_level }}</p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        @if($cut->is_packaged)
                            <form action="{{ route('meat-cuts.update-quantity', $cut) }}" method="POST" class="mb-3">
                                @csrf
                                @method('PATCH')
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <label for="quantity_{{ $cut->id }}" class="form-label small mb-1">Update Quantity:</label>
                                        <input type="number" 
                                               class="form-control form-control-sm" 
                                               id="quantity_{{ $cut->id }}" 
                                               name="quantity" 
                                               value="{{ $cut->quantity ?? 0 }}" 
                                               min="0" 
                                               step="1">
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-save me-1"></i> Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.create', ['meat_cut_id' => $cut->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Add to Inventory
                            </a>
                            <a href="{{ route('meat-cuts.edit', $cut) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <span class="fs-4">😕</span>
                <p class="mt-2">No meat cuts found.</p>
                <a href="{{ route('meat-cuts.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-1"></i> Add your first Meat Cut
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
