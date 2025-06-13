@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Low Stock Products</h3>
                    <div class="card-tools">
                        <a href="{{ route('staff.inventory.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Inventory
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($lowStockProducts->isEmpty())
                        <div class="alert alert-info">
                            No products are currently low in stock.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Meat Cut</th>
                                        <th>Current Stock</th>
                                        <th>Minimum Level</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name }}</td>
                                            <td>{{ $product->meatCut->name }}</td>
                                            <td>{{ $product->quantity }}</td>
                                            <td>{{ $product->minimum_stock_level }}</td>
                                            <td>
                                                @if($product->quantity <= 0)
                                                    <span class="badge badge-danger">Out of Stock</span>
                                                @else
                                                    <span class="badge badge-warning">Low Stock</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('staff.inventory.create', ['product_id' => $product->id, 'type' => 'in']) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> Restock
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 