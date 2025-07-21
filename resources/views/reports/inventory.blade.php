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
                                    <th>Current Stock</th>
                                    <th>Unit Price</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->meatCut->name }}</td>
                                    <td>{{ $product->current_stock }}</td>
                                    <td>₱{{ number_format($product->price_per_kg, 2) }}</td>
                                    <td>₱{{ number_format($product->current_stock * $product->price_per_kg, 2) }}</td>
                                    <td>
                                        @if($product->current_stock <= $product->minimum_stock_level)
                                            <span class="badge badge-danger">Low Stock</span>
                                        @elseif($product->current_stock == 0)
                                            <span class="badge badge-dark">Out of Stock</span>
                                        @else
                                            <span class="badge badge-success">In Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No products found.</td>
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