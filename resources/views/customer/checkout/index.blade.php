<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Checkout</title>

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

        .checkout-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .checkout-item:last-child {
            border-bottom: none;
        }

        .summary-card {
            position: sticky;
            top: 20px;
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

        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-credit-card me-2"></i>Checkout Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('customer.checkout.place-order') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Customer Info -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3"><i class="fas fa-user me-2"></i>Customer Information</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" value="{{ $customer->name ?? '' }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="{{ $customer->email }}" readonly>
                                </div>
                            </div>

                            <!-- Delivery Info -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3"><i class="fas fa-truck me-2"></i>Delivery Information</h6>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Delivery Address *</label>
                                    <textarea class="form-control @error('delivery_address') is-invalid @enderror" name="delivery_address" rows="3" placeholder="Enter your complete delivery address">{{ old('delivery_address', $customer->address) }}</textarea>
                                    @error('delivery_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Phone *</label>
                                    <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" name="contact_phone" value="{{ old('contact_phone', $customer->phone) }}" placeholder="Enter your contact number">
                                    @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Method</h6>
                                </div>
                                <div class="col-12">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_type" id="cash" value="cash" checked>
                                        <label class="form-check-label" for="cash"><i class="fas fa-money-bill me-2"></i>Cash on Delivery</label>
                                    </div>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_type" id="gcash" value="gcash">
                                        <label class="form-check-label" for="gcash"><i class="fas fa-mobile-alt me-2"></i>GCash</label>
                                    </div>

                                    <!-- GCash Upload Section -->
                                    <div id="gcash-upload-section" class="mt-3 d-none">
                                        <label class="form-label">GCash Reference Number *</label>
                                        <input type="text" class="form-control mb-2" name="gcash_reference" placeholder="Enter GCash Reference Number">
                                        
                                        <label class="form-label">Upload GCash Receipt *</label>
                                        <input type="file" class="form-control" name="gcash_receipt" accept="image/*">
                                    </div>

                                    @error('payment_type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label">Delivery Notes (Optional)</label>
                                    <textarea class="form-control @error('delivery_notes') is-invalid @enderror" name="delivery_notes" rows="3" placeholder="Any special instructions for delivery">{{ old('delivery_notes') }}</textarea>
                                    @error('delivery_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('customer.cart') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Cart
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check me-1"></i>Place Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="card summary-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-receipt me-2"></i>Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($cartItems as $item)
                            <div class="checkout-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $item->name ?? 'Product' }}</h6>
                                        <small class="text-muted">{{ $item->qty }} x ₱{{ number_format($item->price, 2) }}</small>
                                    </div>
                                    <div class="text-end"><strong>₱{{ number_format($item->subtotal, 2) }}</strong></div>
                                </div>
                            </div>
                        @endforeach

                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span><span>₱{{ number_format($cartSubtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (12%):</span><span>₱{{ number_format($cartTax, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong><strong class="text-primary">₱{{ number_format($cartTotal, 2) }}</strong>
                        </div>

                        <div class="alert alert-info"><small><i class="fas fa-info-circle me-1"></i><strong>Estimated Delivery:</strong> 3-5 business days</small></div>
                        <div class="alert alert-warning"><small><i class="fas fa-exclamation-triangle me-1"></i><strong>Note:</strong> Orders are processed during business hours (8 AM - 6 PM)</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const gcashRadio = document.getElementById("gcash");
            const cashRadio = document.getElementById("cash");
            const gcashSection = document.getElementById("gcash-upload-section");

            function toggleGCashSection() {
                if (gcashRadio.checked) {
                    gcashSection.classList.remove("d-none");
                } else {
                    gcashSection.classList.add("d-none");
                }
            }

            toggleGCashSection();

            document.querySelectorAll("input[name='payment_type']").forEach(input => {
                input.addEventListener("change", toggleGCashSection);
            });
        });
    </script>
</body>
</html>
