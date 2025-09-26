@extends('layouts.butcher')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Supplier Details</h5>
                    <div>
                        @can('update', $supplier)
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">Edit</a>
                        @endcan
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            @if($supplier->photo)
                                <img src="{{ Storage::url($supplier->photo) }}" alt="{{ $supplier->name }}" class="img-fluid rounded mb-3" style="max-height: 200px;">
                            @else
                                <img src="{{ asset('images/no-image.png') }}" alt="No Image" class="img-fluid rounded mb-3" style="max-height: 200px;">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $supplier->name }}</h4>
                            <p class="text-muted">{{ $supplier->shopname }}</p>
                            <p><strong>Type:</strong> {{ ucfirst($supplier->type_name) }}</p>


                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $supplier->status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($supplier->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Contact Information</h5>
                            <table class="table">
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $supplier->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $supplier->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>{{ $supplier->address }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Bank Details</h5>
                            <table class="table">
                                <tr>
                                    <th>Bank Name:</th>
                                    <td>{{ $supplier->bank_name ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Account Holder:</th>
                                    <td>{{ $supplier->account_holder ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Account Number:</th>
                                    <td>{{ $supplier->account_number ?? 'Not provided' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($products->count() > 0)
                    <div class="mt-4">
                        <h5>Supplied Products</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>{{ number_format($product->price, 2) }}</td>
                                        <td>{{ $product->stock }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @can('update', $supplier)
                    <div class="mt-4">
                        <h5>Assign Products</h5>
                        <form action="{{ route('suppliers.assign-products', $supplier) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <select name="product_ids[]" class="form-select" multiple>
                                    @foreach(\App\Models\Product::all() as $product)
                                        <option value="{{ $product->id }}" {{ $products->contains($product->id) ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Products</button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
