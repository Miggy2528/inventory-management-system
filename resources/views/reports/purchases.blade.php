@extends('layouts.butcher')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Purchases Report</h2>
                <div class="text-muted mt-1">Purchase orders and inventory movements</div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('reports.purchases') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Total Purchases</h3>
                                <p class="h2 mb-0">₱{{ number_format($totalPurchases, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Total Orders</h3>
                                <p class="h2 mb-0">{{ $totalOrders }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Average Order Value</h3>
                                <p class="h2 mb-0">₱{{ number_format($averageOrderValue, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Orders</th>
                                <th>Total Purchases</th>
                                <th>Average Order Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->date }}</td>
                                <td>{{ $purchase->total_orders }}</td>
                                <td>₱{{ number_format($purchase->total_purchases, 2) }}</td>
                                <td>₱{{ $purchase->total_orders > 0 ? number_format($purchase->total_purchases / $purchase->total_orders, 2) : '0.00' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No purchase data found for the selected period.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 