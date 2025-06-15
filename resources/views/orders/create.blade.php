@extends('layouts.butcher')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow border-0">
                <div class="card-header text-white" style="background-color: var(--primary-color); border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <h4 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-file-invoice me-2"></i> Create Order
                    </h4>
                </div>
                <div class="card-body">
                    <x-alert/>
                    <form action="{{ route('invoice.create') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label for="date" class="form-label required">Order Date</label>
                                <input name="date" id="date" type="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') ?? now()->format('Y-m-d') }}" required>
                                @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <x-tom-select label="Customers" id="customer_id" name="customer_id" placeholder="Select Customer" :data="$customers" />
                            </div>
                            <div class="col-md-4">
                                <label for="reference" class="form-label required">Reference</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="ORDR" readonly>
                                @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <livewire:order-form :cart-instance="'order'" />
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn text-white" style="background-color: var(--primary-color); border-radius: 8px; font-size: 1.1rem; padding: 0.6rem 2rem;">
                                <i class="fas fa-plus me-2"></i> Create Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
