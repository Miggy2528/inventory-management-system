@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inventory Movements</h3>
                    <div class="card-tools">
                        <a href="{{ route('staff.inventory.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Movement
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Reference</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryMovements as $movement)
                                    <tr>
                                        <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $movement->product->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $movement->type === 'in' ? 'success' : 'danger' }}">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $movement->quantity }}</td>
                                        <td>
                                            {{ ucfirst($movement->reference_type) }}
                                            @if($movement->reference_id)
                                                #{{ $movement->reference_id }}
                                            @endif
                                        </td>
                                        <td>{{ $movement->notes }}</td>
                                        <td>
                                            @if($movement->created_at->diffInHours(now()) <= 24)
                                                <a href="{{ route('staff.inventory.edit', $movement) }}" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('staff.inventory.destroy', $movement) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to delete this movement?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">No actions available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No inventory movements found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $inventoryMovements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Low Stock Products</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('staff.inventory.reorder') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-exclamation-triangle"></i> View Low Stock Products
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pending Deliveries</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('staff.inventory.follow-up') }}" class="btn btn-info btn-block">
                        <i class="fas fa-truck"></i> Follow Up Deliveries
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expired/Damaged Products</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('staff.inventory.discard') }}" class="btn btn-danger btn-block">
                        <i class="fas fa-trash"></i> Manage Discards
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 