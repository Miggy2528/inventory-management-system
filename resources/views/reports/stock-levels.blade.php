@extends('layouts.butcher')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Stock Levels Report</h2>
                <div class="text-muted mt-1">Current inventory levels by category</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('reports.export-inventory') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                        <path d="M7 11l5 5l5 -5"></path>
                        <path d="M12 4l0 12"></path>
                    </svg>
                    Export to CSV
                </a>
            </div>
        </div>
    </div>

    <div class="page-body">
        @forelse($stockLevels as $category => $products)
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">{{ $category }}</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Meat Cut</th>
                                <th>Current Stock</th>
                                <th>Unit</th>
                                <th>Storage Location</th>
                                <th>Unit Price</th>
                                <th>Stock Value</th>
                                <th>Status</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->meatCut->name ?? 'N/A' }}</td>
                                <td>{{ $product->current_stock }}</td>
                                <td>{{ $product->unit->name ?? 'N/A' }}</td>
                                <td>{{ $product->storage_location ?? 'N/A' }}</td>
                                <td>₱{{ number_format($product->price_per_kg, 2) }}</td>
                                <td>₱{{ number_format($product->current_stock * $product->price_per_kg, 2) }}</td>
                                <td>
                                    @if($product->current_stock <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif($product->current_stock <= ($product->minimum_stock_level ?? 5))
                                        <span class="badge bg-warning">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">In Stock</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-end"><strong>Category Total:</strong></td>
                                <td colspan="2">
                                    <strong>₱{{ number_format($products->sum(function($product) {
                                        return $product->current_stock * $product->price_per_kg;
                                    }), 2) }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body">
                <div class="empty">
                    <div class="empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"></path>
                            <path d="M12 12l8 -4.5"></path>
                            <path d="M12 12l0 9"></path>
                            <path d="M12 12l-8 -4.5"></path>
                        </svg>
                    </div>
                    <p class="empty-title">No products found</p>
                    <p class="empty-subtitle text-muted">
                        Try adding some products to see their stock levels here.
                    </p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection 