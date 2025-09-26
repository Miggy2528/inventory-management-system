@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inventory Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.export-inventory') }}" class="btn btn-success">
                            <i class="fas fa-file-export"></i> Export to CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Products</h5>
                                    <h2 class="mb-0">{{ $products->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Low Stock Items</h5>
                                    <h2 class="mb-0">{{ $lowStockProducts->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Stock Value</h5>
                                    <h2 class="mb-0">₱{{ number_format($stockValue, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Alert -->
                    @if($lowStockProducts->count() > 0)
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h5>
                        <p>The following products are running low on stock:</p>
                        <ul>
                            @foreach($lowStockProducts as $product)
                            <li>{{ $product->name }} (Current Stock: {{ $product->current_stock }})</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Inventory Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Meat Cut</th>
                                    <th>Processing Date</th>
                                    <th>Expiration Date</th>
                                    <th>Current Stock</th>
                                    <th>Unit Price</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->meatCut->name }}</td>
                                    <td>
                                        @if($product->processing_date)
                                            {{ $product->processing_date->format('Y-m-d') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->expiration_date)
                                            {{ $product->expiration_date->format('Y-m-d') }}
                                            @php
                                                $daysToExpire = now()->diffInDays($product->expiration_date, false);
                                            @endphp
                                            @if($daysToExpire < 0)
                                                <span class="badge badge-danger ml-2 text-dark">Expired {{ abs($daysToExpire) }}d</span>
                                            @elseif($daysToExpire <= 7)
                                                <span class="badge badge-warning ml-2 text-dark">Expiring in {{ $daysToExpire }}d</span>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->current_stock }}</td>
                                    <td>₱{{ number_format($product->price_per_kg, 2) }}</td>
                                    <td>₱{{ number_format($product->current_stock * $product->price_per_kg, 2) }}</td>
                                    <td>
                                        @if($product->current_stock <= $product->minimum_stock_level)
                                            <span class="badge badge-danger  text-dark">Low Stock</span>
                                        @elseif($product->current_stock == 0)
                                            <span class="badge badge-dark">Out of Stock</span>
                                        @else
                                            <span class="badge badge-success  text-dark">In Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No products found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 