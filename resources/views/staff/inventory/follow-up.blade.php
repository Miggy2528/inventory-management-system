@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pending Deliveries</h3>
                    <div class="card-tools">
                        <a href="{{ route('staff.inventory.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Inventory
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($pendingDeliveries->isEmpty())
                        <div class="alert alert-info">
                            No pending deliveries found.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Supplier</th>
                                        <th>Quantity</th>
                                        <th>Reference</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingDeliveries as $delivery)
                                        <tr>
                                            <td>{{ $delivery->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $delivery->product->name }}</td>
                                            <td>{{ $delivery->supplier->name }}</td>
                                            <td>{{ $delivery->quantity }}</td>
                                            <td>
                                                Purchase #{{ $delivery->reference_id }}
                                            </td>
                                            <td>
                                                <span class="badge badge-warning">Pending</span>
                                            </td>
                                            <td>
                                                <form action="{{ route('staff.inventory.receive', $delivery) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" 
                                                            class="btn btn-success btn-sm"
                                                            onclick="return confirm('Confirm receipt of this delivery?')">
                                                        <i class="fas fa-check"></i> Receive
                                                    </button>
                                                </form>
                                                <a href="{{ route('staff.inventory.edit', $delivery) }}" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
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