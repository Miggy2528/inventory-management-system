@extends('layouts.tabler')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">
                            {{ __('Order Details') }}
                        </h3>
                    </div>

                    <div class="card-actions btn-actions">
                        <div class="dropdown">
                            <a href="#" class="btn-action dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                    <path d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                    <path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                </svg>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end">
                                @if ($order->order_status === \App\Enums\OrderStatus::PENDING)
                                    <form action="{{ route('orders.update', $order) }}" method="POST">
                                        @csrf
                                        @method('put')
                                        <button type="submit" class="dropdown-item text-success" onclick="return confirm('Are you sure you want to approve this order?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>
                                            {{ __('Approve Order') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <x-action.close route="{{ route('orders.index') }}"/>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Order Basic Info --}}
                    <div class="row row-cards mb-3">
                        <div class="col">
                            <label for="order_date" class="form-label required">{{ __('Order Date') }}</label>
                            <input type="text" id="order_date" class="form-control" value="{{ $order->order_date->format('d-m-Y') }}" disabled>
                        </div>

                        <div class="col">
                            <label for="invoice_no" class="form-label required">{{ __('Invoice No.') }}</label>
                            <input type="text" id="invoice_no" class="form-control" value="{{ $order->invoice_no }}" disabled>
                        </div>

                        <div class="col">
                            <label for="customer" class="form-label required">{{ __('Customer') }}</label>
                            <input type="text" id="customer" class="form-control" value="{{ $order->customer->name }}" disabled>
                        </div>

                        <div class="col">
                            <label for="payment_type" class="form-label required">{{ __('Payment Type') }}</label>
                            <input type="text" id="payment_type" class="form-control" value="{{ $order->payment_type }}" disabled>
                        </div>
                    </div>

                    {{-- Delivery & GCash Info --}}
                    <div class="row row-cards mb-3">
                        <div class="col">
                            <label for="delivery_notes" class="form-label">{{ __('Delivery Note') }}</label>
                            <textarea id="delivery_notes" class="form-control" rows="2" disabled>{{ $order->delivery_notes }}</textarea>
                        </div>

                        <div class="col">
                            <label for="delivery_address" class="form-label">{{ __('Delivery Address') }}</label>
                            <input type="text" id="delivery_address" class="form-control" value="{{ $order->delivery_address }}" disabled>
                        </div>

                        <div class="col">
                            <label for="contact_phone" class="form-label">{{ __('Contact Number') }}</label>
                            <input type="text" id="contact_phone" class="form-control" value="{{ $order->contact_phone }}" disabled>
                        </div>
                    </div>

                    <div class="row row-cards mb-3">
                        <div class="col">
                            <label for="gcash_reference" class="form-label">{{ __('GCash Reference Number') }}</label>
                            <input type="text" id="gcash_reference" class="form-control" value="{{ $order->gcash_reference }}" disabled>
                        </div>

                        <div class="col">
                            <label for="proof_of_payment" class="form-label">{{ __('Proof of Payment (GCash)') }}</label>
                            <div>
                                @if ($order->proof_of_payment)
                                    <img src="{{ asset('storage/' . $order->proof_of_payment) }}" alt="Proof of Payment" style="max-height: 200px;" class="img-fluid border">
                                @else
                                    <span class="text-muted">{{ __('No image uploaded') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Product Details Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Photo</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Product Code</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($order->details as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        <img class="img-fluid" style="max-height: 80px; max-width: 80px;" src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/img/products/default.webp') }}">
                                    </td>
                                    <td class="text-center">{{ $item->product->name }}</td>
                                    <td class="text-center">{{ $item->product->code }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-center">{{ number_format($item->unitcost, 2) }}</td>
                                    <td class="text-center">{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="6" class="text-end">Payed amount</td>
                                <td class="text-center">{{ number_format($order->pay, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end">Due</td>
                                <td class="text-center">{{ number_format($order->due, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end">VAT</td>
                                <td class="text-center">{{ number_format($order->vat, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end">Total</td>
                                <td class="text-center">{{ number_format($order->total, 2) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    @if ($order->order_status === \App\Enums\OrderStatus::PENDING)
                        <form action="{{ route('orders.update', $order) }}" method="POST">
                            @method('put')
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to complete this order?')">
                                {{ __('Complete Order') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
