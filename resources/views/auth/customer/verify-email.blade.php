@extends('layouts.auth')

@section('content')
<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">
            Verify Your Email Address
        </h2>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <p class="mb-4">
            Before proceeding, please check your email for a verification link.<br>
            If you did not receive the email, you can request another below.
        </p>
        <form method="POST" action="{{ route('customer.verification.resend') }}">
            @csrf
            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">
                    Resend Verification Email
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 