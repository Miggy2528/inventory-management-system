@extends('layouts.auth')

@push('page-styles')
<style>
    /* Widen the auth card for the two-column registration page */
    .auth-card { max-width: 1200px; }
    @media (max-width: 991.98px) { /* Bootstrap lg breakpoint */
        .auth-card { max-width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center g-4">
    <!-- Admin/Staff Registration -->
    <div class="col-12 col-lg-6">
        <form class="card card-md" action="{{ route('register') }}" method="POST" autocomplete="off">
            @csrf

            <div class="card-body">
                <h2 class="card-title text-center mb-4">Staff/Admin Registration</h2>

                <x-input name="name" :value="old('name')" placeholder="Your name" required="true"/>

                <x-input name="email" :value="old('email')" placeholder="your@email.com" required="true"/>

                <x-input name="username" :value="old('username')" placeholder="Your username" required="true"/>

                <x-input name="password" :value="old('password')" placeholder="Password" required="true"/>

                <x-input name="password_confirmation" :value="old('password_confirmation')" placeholder="Password confirmation" required="true" label="Password Confirmation"/>

                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="terms-of-service" id="terms-of-service"
                               class="form-check-input @error('terms-of-service') is-invalid @enderror"
                        >
                        <span class="form-check-label">
                            Agree the <a href="./terms-of-service.html" tabindex="-1">
                                terms and policy</a>.
                        </span>
                    </label>
                </div>

                <div class="form-footer">
                    <x-button type="submit" class="w-100">
                        {{ __('Create Staff/Admin Account') }}
                    </x-button>
                </div>
            </div>
        </form>

        <div class="text-center text-secondary mt-3">
            Staff/Admin? Already have account? <a href="{{ route('login') }}" tabindex="-1">
                Sign in
            </a>
        </div>
    </div>

    <!-- Customer Registration -->
    <div class="col-12 col-lg-6">
        <form class="card card-md border-primary" action="{{ route('customer.register') }}" method="POST" autocomplete="off">
            @csrf

            <div class="card-header bg-primary text-white text-center">
                <h3 class="h3 mb-0">Customer Portal</h3>
            </div>

            <div class="card-body">
                <h2 class="card-title text-center mb-4">Customer Registration</h2>

                <x-input name="name" :value="old('name')" placeholder="Full Name" required="true"/>

                <x-input name="email" :value="old('email')" placeholder="your@email.com" required="true"/>

                <x-input name="username" :value="old('username')" placeholder="Username" required="true"/>

                <x-input name="password" :value="old('password')" placeholder="Password" required="true"/>

                <x-input name="password_confirmation" :value="old('password_confirmation')" placeholder="Confirm Password" required="true" label="Password Confirmation"/>

                <x-input name="phone" :value="old('phone')" placeholder="Phone Number" required="true"/>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Your address" required>{{ old('address') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="terms-of-service" id="terms-of-service-customer"
                               class="form-check-input @error('terms-of-service') is-invalid @enderror"
                        >
                        <span class="form-check-label">
                            Agree the <a href="./terms-of-service.html" tabindex="-1">
                                terms and policy</a>.
                        </span>
                    </label>
                </div>

                <div class="form-footer">
                    <x-button type="submit" class="w-100 btn-primary">
                        {{ __('Create Customer Account') }}
                    </x-button>
                </div>
            </div>
        </form>

        <div class="text-center text-secondary mt-3">
            Customer? Already have account? <a href="{{ route('customer.login') }}" tabindex="-1" class="text-primary font-weight-bold">
                Sign in as Customer
            </a>
        </div>
    </div>
</div>

<!-- Divider -->
<div class="row mt-4">
    <div class="col-12">
        <div class="text-center">
            <hr class="my-4">
            <p class="text-muted small">
                <strong>Staff/Admin:</strong> For employees who manage inventory, orders, and system settings<br>
                <strong>Customer:</strong> For clients who want to place orders and track deliveries
            </p>
        </div>
    </div>
</div>
@endsection
