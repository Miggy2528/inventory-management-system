<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - {{ $category->name ?? 'Category' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .product-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .filter-sidebar {
            background: #f8f9fa;
            border-radius: 8px;
        }
        .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
                <i class="fas fa-store me-2"></i>{{ config('app.name', 'Inventory System') }}
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.dashboard') }}">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('customer.products') }}">
                            <i class="fas fa-box me-1"></i>Products
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
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
                <li class="breadcrumb-item active" aria-current="page">{{ $category->name ?? 'Category' }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="filter-sidebar p-3">
                    <h5 class="mb-3">
                        <i class="fas fa-filter me-2"></i>Filters
                    </h5>
                    
                    <!-- Search -->
                    <form method="GET" action="{{ route('customer.products.category', $category) }}">
                        <div class="mb-3">
                            <label for="search" class="form-label">Search Products</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search products...">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>

                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $cat->id == $category->id ? 'selected' : '' }}>
                                        {{ $cat->name ?? 'Uncategorized' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                                <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Price</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="direction" class="form-label">Order</label>
                            <select class="form-select" id="direction" name="direction">
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Apply Filters
                        </button>
                        
                        @if(request('search') || request('sort') != 'name' || request('direction') != 'asc')
                            <a href="{{ route('customer.products.category', $category) }}" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-times me-1"></i>Clear Filters
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="fas fa-tags me-2"></i>{{ $category->name ?? 'Category' }} Products
                    </h2>
                    <div class="text-muted">
                        {{ $products->total() }} products found
                    </div>
                </div>

                @if($products->count() > 0)
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card product-card">
                                    <div class="position-relative">
                                        @if($product->product_image)
                                            <img src="{{ Storage::url($product->product_image) }}" 
                                                 alt="{{ $product->name ?? 'Product' }}" class="product-image">
                                        @else
                                            <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                        
                                        @if($product->quantity <= 5)
                                            <span class="badge bg-warning stock-badge">Low Stock</span>
                                        @endif
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">{{ $product->name ?? 'Unnamed Product' }}</h5>
                                        <p class="card-text text-muted small">
                                            {{ $product->code ?? 'N/A' }} • {{ $product->category->name ?? 'Uncategorized' }}
                                        </p>
                                        
                                        <div class="price mb-2">
                                            ₱{{ number_format($product->selling_price ?? 0, 2) }}/kg
                                        </div>
                                        
                                        <div class="text-muted small mb-3">
                                            <i class="fas fa-box me-1"></i>
                                            {{ $product->quantity ?? 0 }} {{ $product->unit->name ?? 'units' }} available
                                        </div>
                                        
                                        <div class="mt-auto">
                                            @if($product->quantity > 0)
                                                <form action="{{ route('customer.cart.add') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary btn-sm w-100" disabled>
                                                    <i class="fas fa-times me-1"></i>Out of Stock
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Products Found in {{ $category->name ?? 'this category' }}</h5>
                        <p class="text-muted">Try adjusting your search criteria or browse all products.</p>
                        <a href="{{ route('customer.products') }}" class="btn btn-primary">
                            <i class="fas fa-store me-1"></i>Browse All Products
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html> 