@extends('layouts.app')
@section('title', 'Register')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header" style="background: var(--success);">
            <i class="fas fa-user-plus"></i>
            <h4>Register as Resident</h4>
        </div>
        <div class="auth-body">
            @if($errors->any())
            <div class="alert alert-danger mb-3" style="font-size: 14px;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required placeholder="Your full name" value="{{ old('name') }}" style="font-size: 16px;">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" required placeholder="Your email" value="{{ old('email') }}" style="font-size: 16px;">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Create password" style="font-size: 16px;">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm password" style="font-size: 16px;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="barangay_id" class="form-label">Barangay</label>
                        <select class="form-select @error('barangay_id') is-invalid @enderror" id="barangay_id" name="barangay_id" required>
                            <option value="">Select Barangay</option>
                            @foreach($barangays as $barangay)
                            <option value="{{ $barangay->id }}" {{ old('barangay_id') == $barangay->id ? 'selected' : '' }}>{{ $barangay->name }}</option>
                            @endforeach
                        </select>
                        @error('barangay_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" required placeholder="Your phone" maxlength="11" value="{{ old('contact_number') }}" style="font-size: 16px;">
                        @error('contact_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @else
                        <div class="invalid-feedback">Contact number must be exactly 11 digits (no letters or special characters).</div>
                        @enderror
                    </div>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-success">Register</button>
                </div>
            </form>
            <hr>
            <p class="text-center mb-0">Already have an account? <a href="{{ route('login') }}">Login</a></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const contactInput = document.getElementById('contact_number');

    function validateContact() {
        const value = contactInput.value.replace(/\D/g, '');
        contactInput.value = value;

        const isValid = value.length === 11 && /^\d{11}$/.test(value);

        if (isValid) {
            contactInput.classList.remove('is-invalid');
            contactInput.classList.add('is-valid');
        } else {
            contactInput.classList.remove('is-valid');
            // Don't auto-add is-invalid on load - let server validation handle it
            if (value.length > 0) {
                contactInput.classList.add('is-invalid');
            } else {
                contactInput.classList.remove('is-invalid');
            }
        }
    }

    // Run only if there's a value (page reload with old input)
    if (contactInput.value.length > 0) {
        validateContact();
    }

    contactInput.addEventListener('input', validateContact);
    contactInput.addEventListener('blur', validateContact);

    form.addEventListener('submit', function() {
        // Final cleanup before submit
        const value = contactInput.value.replace(/\D/g, '');
        contactInput.value = value;
    });
});
</script>
@endsection