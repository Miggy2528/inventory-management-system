@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-drumstick-bite me-2"></i>Meat Cuts Management
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Manage Meat Cuts</h3>
                    <a href="{{ route('meat-cuts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Cut
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Animal Type</th>
                                    <th>Cut Type</th>
                                    <th>Price/kg</th>
                                    <th>Price/pkg</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Min. Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($meatCuts as $cut)
                                    <tr>
                                        <td>
                                            @if($cut->image_path)
                                                <img src="{{ Storage::url($cut->image_path) }}" 
                                                     alt="{{ $cut->name }}" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 50px;">
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                        <td>{{ $cut->name }}</td>
                                        <td>{{ $cut->animal_type ? ucfirst($cut->animal_type) : 'N/A' }}</td>
                                        <td>{{ $cut->cut_type ? ucfirst($cut->cut_type) : 'N/A' }}</td>
                                        <td>
                                            <div>
                                                <strong>Price:</strong>
                                                ₱{{ number_format(($cut->is_packaged ? ($cut->package_price ?? 0) : ($cut->default_price_per_kg ?? 0)), 2) }}
                                            </div>
                                            <div class="text-muted small">{{ $cut->is_packaged ? 'Sold per package' : 'Sold by kg' }}</div>
                                        </td>
                                        <td>{{ $cut->quantity ?? 0 }}</td>
                                        <td>
                                            <span class="badge {{ $cut->is_available ? 'bg-success' : 'bg-danger' }}">
                                                {{ $cut->is_available ? 'Available' : 'Not Available' }}
                                            </span>
                                        </td>
                                        <td>{{ $cut->minimum_stock_level }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('meat-cuts.edit', $cut) }}" 
                                                   class="btn btn-sm btn-info me-2">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('meat-cuts.destroy', $cut) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this cut?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No meat cuts found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $meatCuts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-styles')
<style>
    .card-header {
        border-bottom: none;
    }
    .bg-danger {
        background-color: var(--primary-color) !important;
    }
</style>
@endpush 