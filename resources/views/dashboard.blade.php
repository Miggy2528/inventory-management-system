@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-chart-line me-2"></i>Dashboard Overview
            </h1>
        </div>
    </div>

    <!-- Meat Inventory Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-drumstick-bite me-2"></i>Meat Inventory Overview
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="bg-danger text-white p-3 rounded">
                                                <i class="fas fa-meat fa-2x"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h3 class="mb-0">{{ $totalMeatCuts }}</h3>
                                            <div class="text-muted">Total Meat Cuts</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="bg-success text-white p-3 rounded">
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h3 class="mb-0">{{ $availableMeatCuts }}</h3>
                                            <div class="text-muted">Available Cuts</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="bg-warning text-white p-3 rounded">
                                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h3 class="mb-0">{{ $lowStockMeatCuts }}</h3>
                                            <div class="text-muted">Low Stock Items</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="bg-info text-white p-3 rounded">
                                                <i class="fas fa-shopping-cart fa-2x"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h3 class="mb-0">{{ $todayOrders }}</h3>
                                            <div class="text-muted">Today's Orders</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Overview Section -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>Meat by Animal Type
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Animal Type</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($meatByAnimalType as $type => $count)
                                <tr>
                                    <td>{{ ucfirst($type) }}</td>
                                    <td>{{ $count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks me-2"></i>Orders Overview
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>Today's Orders</div>
                        <div class="badge bg-info">{{ $todayOrders }}</div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Completed Orders</div>
                        <div class="badge bg-success">{{ $completedOrders }}</div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Total Orders</div>
                        <div class="badge bg-primary">{{ $orders }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-styles')
<style>
    .card-header {
        border-bottom: none;
    }
    .bg-danger {
        background-color: var(--primary-color) !important;
    }
</style>
@endpush
