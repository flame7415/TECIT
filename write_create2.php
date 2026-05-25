<?php
$path = __DIR__ . '/resources/views/complaints/create.blade.php';

$content = <<<'EOF'
@extends('layouts.app')
@section('title', 'File Complaint')
@section('page-title', 'File a Complaint')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <!-- Progress indicator -->
        <div class="d-flex align-items-center mb-4 px-2">
            <div class="step-indicator active">1</div>
            <div class="step-line"></div>
            <div class="step-indicator active">2</div>
            <div class="step-line"></div>
            <div class="step-indicator">3</div>
        </div>

        <div class="card complaint-form-card">
            <div class="card-header complaint-form-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Submit New Complaint</h5>
                        <small class="text-muted">Please fill in all required fields marked with <span class="text-danger">*</span></small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data" id="complaintForm">
                    @csrf

                    <!-- Section: Location & Category -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title">
                            <span class="section-number">1</span> Location & Category
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="barangay_id" class="form-label fw-semibold">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>Barangay <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg @error('barangay_id') is-invalid @enderror" id="barangay_id" name="barangay_id" required>
                                    <option value="" disabled selected>-- Select your barangay --</option>
                                    @foreach($barangays as $barangay)
                                    <option value="{{ $barangay->id }}" {{ old('barangay_id') == $barangay->id ? 'selected' : '' }}>{{ $barangay->name }}</option>
                                    @endforeach
                                </select>
                                @error('barangay_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label fw-semibold">
                                    <i class="fas fa-tags text-primary me-1"></i>Category <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="" disabled selected>-- Select category --</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="form-divider">

                    <!-- Section: Complainant Details -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title">
                            <span class="section-number">2</span> Complainant Details
                        </h6>
                        <div class="mb-3">
                            <label for="vulnerability_flag" class="form-label fw-semibold">
                                <i class="fas fa-heart text-info me-1"></i>Vulnerability Status <span class="text-muted fw-normal">(Optional)</span>
                            </label>
                            <select class="form-select @error('vulnerability_flag') is-invalid @enderror" id="vulnerability_flag" name="vulnerability_flag">
                                <option value="none" {{ old('vulnerability_flag', 'none') == 'none' ? 'selected' : '' }}>None — I am not part of a vulnerable group</option>
                                <option value="elderly" {{ old('vulnerability_flag') == 'elderly' ? 'selected' : '' }}>Senior Citizen (60 years old and above)</option>
                                <option value="PWD" {{ old('vulnerability_flag') == 'PWD' ? 'selected' : '' }}>Person with Disability (PWD)</option>
                            </select>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Select if applicable. This helps us prioritize your complaint appropriately.
                            </div>
                            @error('vulnerability_flag')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="form-divider">

                    <!-- Section: Evidence -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title">
                            <span class="section-number">3</span> Evidence & Documentation
                        </h6>
                        <div class="mb-3">
                            <label for="image" class="form-label fw-semibold">
                                <i class="fas fa-camera text-success me-1"></i>Attach Photo <span class="text-muted fw-normal">(Optional)</span>
                            </label>
                            <div class="file-upload-area" id="dropZone">
                                <input type="file" class="form-control d-none @error('image') is-invalid @enderror" id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/gif">
                                <div class="file-upload-placeholder" id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-1"><strong>Click to upload</strong> or drag and drop</p>
                                    <small class="text-muted">JPEG, PNG, GIF (max 5MB)</small>
                                </div>
                                <div class="file-upload-preview d-none" id="uploadPreview">
                                    <img src="" alt="Preview" class="img-thumbnail mb-2" style="max-height: 200px;">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeImage">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="form-divider">

                    <!-- Section: Complaint Details -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title">
                            <span class="section-number">4</span> Complaint Details
                        </h6>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">
                                <i class="fas fa-align-left text-warning me-1"></i>Description <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required minlength="20" maxlength="2000" placeholder="Describe your complaint in detail. Include:&#10;&#10;- What happened?&#10;- When did it occur? (date and time)&#10;- Where exactly did it happen?&#10;- Who was involved?&#10;- Any other relevant details...">{{ old('description') }}</textarea>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Minimum 20 characters required
                                </div>
                                <small class="text-muted" id="charCount">0 / 2000</small>
                            </div>
                            @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg submit-btn">
                            <i class="fas fa-paper-plane me-2"></i>Submit Complaint
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Progress Steps */
    .step-indicator {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--gray-200);
        color: var(--gray-500);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        flex-shrink: 0;
    }
    .step-indicator.active {
        background: var(--primary);
        color: #fff;
    }
    .step-line {
        flex: 1;
        height: 3px;
        background: var(--gray-200);
        margin: 0 8px;
        border-radius: 2px;
    }
    .step-indicator.active + .step-line {
        background: var(--primary);
    }

    /* Form Card */
    .complaint-form-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .complaint-form-header {
        background: linear-gradient(135deg, var(--primary) 0%, #6366f1 100%);
        color: #fff;
        border-radius: 16px 16px 0 0 !important;
        padding: 24px;
        border: none;
    }
    .complaint-form-header h5 {
        color: #fff;
        font-weight: 600;
    }
    .complaint-form-header small {
        color: rgba(255,255,255,0.8) !important;
    }
    .complaint-form-header .text-danger {
        color: #ffcdd2 !important;
    }
    .form-icon {
        width: 48px;
        height: 48px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* Form Sections */
    .form-section-title {
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-number {
        width: 28px;
        height: 28px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
    }
    .form-divider {
        border-color: var(--gray-200);
        margin: 24px 0;
        opacity: 0.6;
    }

    /* Form Controls */
    .form-label {
        font-size: 14px;
        margin-bottom: 8px;
    }
    .form-label i {
        font-size: 12px;
    }
    .form-select, .form-control {
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.2s;
    }
    .form-select:focus, .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }
    .form-select.is-invalid, .form-control.is-invalid {
        border-color: var(--danger);
    }
    .form-text {
        font-size: 12px;
        margin-top: 6px;
    }

    /* File Upload */
    .file-upload-area {
        border: 2px dashed var(--gray-300);
        border-radius: 12px;
        padding: 32px 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--gray-50);
    }
    .file-upload-area:hover {
        border-color: var(--primary);
        background: rgba(79, 70, 229, 0.03);
    }
    .file-upload-area.has-file {
        border-style: solid;
        border-color: var(--success);
        background: rgba(16, 185, 129, 0.05);
    }
    .file-upload-placeholder p {
        color: var(--gray-600);
        font-size: 14px;
    }

    /* Submit Button */
    .submit-btn {
        padding: 14px 24px;
        font-weight: 600;
        font-size: 16px;
        border-radius: 10px;
    }

    /* Character counter */
    #charCount {
        font-size: 12px;
    }
    #charCount.near-limit {
        color: var(--warning);
        font-weight: 600;
    }

    /* Mobile adjustments */
    @media (max-width: 768px) {
        .complaint-form-header {
            padding: 16px;
        }
        .card-body {
            padding: 20px !important;
        }
        .step-indicator {
            width: 30px;
            height: 30px;
            font-size: 12px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // File upload handling
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('image');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const uploadPreview = document.getElementById('uploadPreview');
    const previewImg = uploadPreview.querySelector('img');
    const removeBtn = document.getElementById('removeImage');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = 'var(--primary)';
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.style.borderColor = '';
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '';
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFile(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            handleFile(e.target.files[0]);
        }
    });

    function handleFile(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            uploadPlaceholder.classList.add('d-none');
            uploadPreview.classList.remove('d-none');
            dropZone.classList.add('has-file');
        };
        reader.readAsDataURL(file);
    }

    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        uploadPlaceholder.classList.remove('d-none');
        uploadPreview.classList.add('d-none');
        dropZone.classList.remove('has-file');
    });

    // Character counter
    const textarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');

    function updateCharCount() {
        const len = textarea.value.length;
        charCount.textContent = len + ' / 2000';
        if (len > 1800) {
            charCount.classList.add('near-limit');
        } else {
            charCount.classList.remove('near-limit');
        }
    }

    textarea.addEventListener('input', updateCharCount);
    updateCharCount();
</script>
@endsection
EOF;

file_put_contents($path, $content);
echo "Written " . strlen($content) . " bytes to " . $path;

