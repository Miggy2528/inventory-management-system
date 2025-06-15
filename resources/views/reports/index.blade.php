@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Reports & Analytics</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Inventory Reports -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-boxes"></i> Inventory Reports
                                    </h5>
                                    <p class="card-text">View and analyze your current inventory status, stock levels, and value.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('reports.inventory') }}" class="btn btn-primary">
                                            <i class="fas fa-chart-bar"></i> View Inventory Report
                                        </a>
                                        {{--
                                        <a href="{{ route('reports.export.inventory') }}" class="btn btn-success">
                                            <i class="fas fa-file-export"></i> Export to CSV
                                        </a>
                                        --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Reports -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-chart-line"></i> Sales Reports
                                    </h5>
                                    <p class="card-text">Analyze sales data, track revenue, and monitor sales trends.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('reports.sales') }}" class="btn btn-primary">
                                            <i class="fas fa-chart-bar"></i> View Sales Report
                                        </a>
                                        {{--
                                        <a href="{{ route('reports.export.sales') }}" class="btn btn-success">
                                            <i class="fas fa-file-export"></i> Export to CSV
                                        </a>
                                        --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Purchase Reports -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-shopping-cart"></i> Purchase Reports
                                    </h5>
                                    <p class="card-text">Track purchase orders, supplier performance, and procurement costs.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('reports.purchases') }}" class="btn btn-primary">
                                            <i class="fas fa-chart-bar"></i> View Purchase Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Levels -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-layer-group"></i> Stock Levels
                                    </h5>
                                    <p class="card-text">Monitor stock levels by category and identify items that need attention.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('reports.stock-levels') }}" class="btn btn-primary">
                                            <i class="fas fa-chart-bar"></i> View Stock Levels
                                        </a>
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
@endsection 