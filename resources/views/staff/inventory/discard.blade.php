@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expired and Damaged Products</h3>
                    <div class="card-tools">
                        <a href="{{ route('staff.inventory.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Inventory
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($expiredProducts->isEmpty())
                        <div class="alert alert-info">
                            No expired or damaged products found.
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
                                        <th>Expiration Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiredProducts as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name }}</td>
                                            <td>{{ $product->meatCut->name }}</td>
                                            <td>{{ $product->quantity }}</td>
                                            <td>
                                                @if($product->expiration_date)
                                                    {{ $product->expiration_date->format('Y-m-d') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->expiration_date && $product->expiration_date->isPast())
                                                    <span class="badge badge-danger">Expired</span>
                                                @elseif($product->status === 'damaged')
                                                    <span class="badge badge-warning">Damaged</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('staff.inventory.discard', $product) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to discard this product? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i> Discard
                                                    </button>
                                                </form>
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