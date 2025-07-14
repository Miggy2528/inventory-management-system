<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - {{ $product->name ?? 'Product' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #8B0000;
            --secondary-color: #4A0404;
            --accent-color: #FF4136;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
        }

        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .price {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }

        .quantity-input {
            width: 100px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
                <i class="fas fa-drumstick-bite me-2"></i>
                Yannis Meatshop - Customer Portal
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('customer.products') }}">
                    <i class="fas fa-store me-1"></i>Products
                </a>
                <a class="nav-link" href="{{ route('customer.cart') }}">
                    <i class="fas fa-shopping-cart me-1"></i>Cart
                    <span class="badge bg-danger ms-1">{{ \Gloudemans\Shoppingcart\Facades\Cart::instance('customer')->count() }}</span>
                </a>
                <a class="nav-link" href="{{ route('customer.orders') }}">
                    <i class="fas fa-shopping-bag me-1"></i>My Orders
                </a>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ auth()->user()->name ?? 'Customer' }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.profile') }}">
                            <i class="fas fa-user-edit me-2"></i>My Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.products') }}">Products</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $product->name ?? 'Product' }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Product Image -->
                            <div class="col-md-6 mb-4">
                                <div class="position-relative">
                                    @if($product->product_image)
                                        <img src="{{ Storage::url($product->product_image) }}" 
                                             alt="{{ $product->name ?? 'Product' }}" class="product-image w-100">
                                    @else
                                        <div class="product-image d-flex align-items-center justify-content-center bg-light" style="height: 300px;">
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    @if($product->quantity <= 5)
                                        <span class="badge bg-warning stock-badge">Low Stock</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="col-md-6">
                                <h2 class="mb-3">{{ $product->name ?? 'Unnamed Product' }}</h2>
                                
                                <div class="price mb-3">
                                    ₱{{ number_format($product->selling_price ?? 0, 2) }}/kg
                                </div>
                                
                                <div class="mb-3">
                                    <p class="text-muted mb-1">
                                        <strong>Product Code:</strong> {{ $product->code }}
                                    </p>
                                    <p class="text-muted mb-1">
                                        <strong>Category:</strong> {{ $product->category->name ?? 'Uncategorized' }}
                                    </p>
                                    <p class="text-muted mb-1">
                                        <strong>Unit:</strong> {{ $product->unit->name ?? 'kg' }}
                                    </p>
                                    <p class="text-muted mb-3">
                                        <strong>Stock:</strong> {{ $product->quantity }} available
                                    </p>
                                </div>
                                
                                @if($product->notes)
                                    <div class="mb-3">
                                        <h6>Description:</h6>
                                        <p class="text-muted">{{ $product->notes }}</p>
                                    </div>
                                @endif
                                
                                @if($product->quantity > 0)
                                    <form action="{{ route('customer.cart.add') }}" method="POST" class="mb-3">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <div class="row align-items-end">
                                            <div class="col-md-4">
                                                <label for="quantity" class="form-label">Quantity (kg)</label>
                                                <input type="number" class="form-control quantity-input" 
                                                       id="quantity" name="quantity" value="1" 
                                                       min="1" max="{{ $product->quantity }}">
                                            </div>
                                            <div class="col-md-8">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        This product is currently out of stock.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Related Products -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-thumbs-up me-2"></i>Related Products
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($relatedProducts->count() > 0)
                            @foreach($relatedProducts as $relatedProduct)
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    <div class="flex-shrink-0 me-3">
                                        @if($relatedProduct->product_image)
                                            <img src="{{ Storage::url($relatedProduct->product_image) }}" 
                                                 alt="{{ $relatedProduct->name ?? 'Related Product' }}" 
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                        @else
                                            <div style="width: 60px; height: 60px; background-color: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $relatedProduct->name ?? 'Related Product' }}</h6>
                                        <p class="text-muted mb-1 small">₱{{ number_format($relatedProduct->selling_price ?? 0, 2) }}/kg</p>
                                        <a href="{{ route('customer.products.show', $relatedProduct) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No related products found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html> 