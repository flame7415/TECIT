@extends('layouts.app')
@section('title', 'Forgot Password')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header" style="background: var(--warning);">
            <i class="fas fa-key"></i>
            <h4>Reset Password</h4>
        </div>
        <div class="auth-body">
            @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <p class="text-muted mb-4">Enter your email address and we'll send you a link to reset your password.</p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email" style="font-size: 16px;">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-warning text-dark">Send Reset Link</button>
                </div>
            </form>
            <hr>
            <p class="text-center mb-0"><a href="{{ route('login') }}">Back to Login</a></p>
        </div>
    </div>
</div>
@endsection

