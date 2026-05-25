<?php
$path = __DIR__ . '/resources/views/complaints/create.blade.php';

$content = <<<'EOF'
@extends('layouts.app')
@section('title', 'File Complaint')
@section('page-title', 'File a Complaint')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Submit New Complaint</div>
            <div class="card-body">
                <form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="barangay_id" class="form-label">Barangay <span class="text-danger">*</span></label>
                            <select class="form-select @error('barangay_id') is-invalid @enderror" id="barangay_id" name="barangay_id" required>
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $barangay)
                                <option value="{{ $barangay->id }}" {{ old('barangay_id') == $barangay->id ? 'selected' : '' }}>{{ $barangay->name }}</option>
                                @endforeach
                            </select>
                            @error('barangay_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="vulnerability_flag" class="form-label">Vulnerability Status (Optional)</label>
                        <select class="form-select" id="vulnerability_flag" name="vulnerability_flag">
                            <option value="none" {{ old('vulnerability_flag', 'none') == 'none' ? 'selected' : '' }}>None</option>
                            <option value="elderly" {{ old('vulnerability_flag') == 'elderly' ? 'selected' : '' }}>Elderly</option>
                            <option value="PWD" {{ old('vulnerability_flag') == 'PWD' ? 'selected' : '' }}>Person with Disability (PWD)</option>
                        </select>
                        <small class="text-muted">Select if you are a senior citizen or person with disability</small>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Attach Photo (Optional)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/gif">
                        <small class="text-muted">Upload a photo related to your complaint (JPEG, PNG, GIF, max 5MB)</small>
                        @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="imagePreview" class="mt-2 d-none">
                            <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required minlength="20" placeholder="Describe your complaint in detail (minimum 20 characters)">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Submit Complaint</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const img = preview.querySelector('img');
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(e.target.files[0]);
        } else {
            preview.classList.add('d-none');
        }
    });
</script>
@endsection
EOF;

file_put_contents($path, $content);
echo "Written " . strlen($content) . " bytes to " . $path;

