@extends('layouts.auth')

@section('content')
<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">
            Create Customer Account
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
        <form action="{{ route('customer.register') }}" method="POST" autocomplete="off">
            @csrf
            <x-input name="name" :value="old('name')" placeholder="Full Name" required="true"/>
            <x-input name="email" :value="old('email')" placeholder="your@email.com" required="true"/>
            <x-input name="username" :value="old('username')" placeholder="Username" required="true"/>
            <x-input type="password" name="password" placeholder="Password" required="true"/>
            <x-input type="password" name="password_confirmation" placeholder="Confirm Password" required="true"/>
            <x-input name="phone" :value="old('phone')" placeholder="Phone Number" required="true"/>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3" placeholder="Your address" required>{{ old('address') }}</textarea>
            </div>
            <div class="form-footer">
                <x-button type="submit" class="w-100">
                    {{ __('Create Account') }}
                </x-button>
            </div>
        </form>
    </div>
</div>
<div class="text-center mt-3 text-gray-600">
    <p>Already have an account?
        <a href="{{ route('customer.login') }}" class="text-blue-500 hover:text-blue-700 focus:outline-none focus:underline" tabindex="-1">
            Sign in
        </a>
    </p>
</div>
@endsection 