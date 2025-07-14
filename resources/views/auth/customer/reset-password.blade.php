@extends('layouts.auth')

@section('content')
@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<form class="card card-md" action="{{ route('customer.password.update') }}" method="POST" autocomplete="off" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="card-body">
        <h2 class="card-title text-center mb-4">
            Reset Password
        </h2>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" name="email" id="email" value="{{ old('email', $email) }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="Enter email" required>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="New password" required>
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   placeholder="Confirm new password" required>
            @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">
                Reset Password
            </button>
        </div>
    </div>
</form>
<div class="text-center text-secondary mt-3">
    Remembered? <a href="{{ route('customer.login') }}">Back to login</a>
</div>
@endsection 