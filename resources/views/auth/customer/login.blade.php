@extends('layouts.auth')

@section('content')
<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">
            Customer Login
        </h2>
        {{-- Error and session message display --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form action="{{ route('customer.login.store') }}" method="POST" autocomplete="off">
            @csrf
            <x-input name="login" :value="old('login')" placeholder="Email or Username" required="true"/>
            <x-input type="password" name="password" placeholder="Your password" required="true"/>
            <div class="mb-2">
                <label for="remember" class="form-check">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input"/>
                    <span class="form-check-label">Remember me on this device</span>
                </label>
            </div>
            <div class="form-footer">
                <x-button type="submit" class="w-100">
                    {{ __('Sign in') }}
                </x-button>
            </div>
        </form>
    </div>
</div>
<div class="text-center mt-3 text-gray-600">
    <p>Don't have a customer account yet?
        <a href="{{ route('customer.register') }}" class="text-blue-500 hover:text-blue-700 focus:outline-none focus:underline" tabindex="-1">
            Sign up
        </a>
    </p>
    <hr class="my-4">
    <p class="text-muted small">
        <strong>Customer:</strong> Place orders, track deliveries, and manage your account<br>
        <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark focus:outline-none focus:underline">
            Staff/Admin? Click here to login
        </a>
    </p>
</div>
@endsection 