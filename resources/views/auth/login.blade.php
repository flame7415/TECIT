@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-shield-alt"></i>
            <h4>Login to MCIMS</h4>
        </div>
        <div class="auth-body">
            @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required autofocus placeholder="Enter your email" style="font-size: 16px;">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                </div>
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot Password?</a>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
            <hr>
            <p class="text-center mb-0">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
        </div>
</div>
@endsection
