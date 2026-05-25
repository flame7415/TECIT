@extends('layouts.app')
@section('title', 'Reset Password')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header" style="background: var(--success);">
            <i class="fas fa-lock"></i>
            <h4>New Password</h4>
        </div>
        <div class="auth-body">
            @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ $email }}" disabled style="font-size: 16px;">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Minimum 8 characters" style="font-size: 16px;">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Repeat new password" style="font-size: 16px;">
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-success">Reset Password</button>
                </div>
            </form>
            <hr>
            <p class="text-center mb-0"><a href="{{ route('login') }}">Back to Login</a></p>
        </div>
    </div>
</div>
@endsection

