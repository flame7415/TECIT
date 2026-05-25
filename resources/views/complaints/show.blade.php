@extends('layouts.app')
@section('title', 'Complaint Details')
@section('page-title', 'Complaint Details')

@section('content')
<div class="row">
    <!-- Main Complaint Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-file-alt me-2 text-muted"></i>Complaint <strong>#{{ $complaint->complaint_id }}</strong></span>
                <div>
                    @if($complaint->escalated_to_municipal)
                    <span class="badge bg-danger me-1">Escalated</span>
                    @endif
                    @switch($complaint->status)
                        @case('pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                            @break
                        @case('acknowledged')
                            <span class="badge bg-info">Acknowledged</span>
                            @break
                        @case('in_progress')
                            <span class="badge bg-primary">In Progress</span>
                            @break
                        @case('resolved')
                            <span class="badge bg-success">Resolved</span>
                            @break
                        @case('closed')
                            <span class="badge bg-secondary">Closed</span>
                            @break
                        @default
                            <span class="badge bg-secondary">{{ ucfirst($complaint->status) }}</span>
                    @endswitch
                </div>
            </div>
            <div class="card-body">
                <!-- Details Grid -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted text-uppercase d-block mb-1"><i class="fas fa-map-marker-alt me-1 text-primary"></i>Barangay</small>
                            <p class="mb-0 fw-semibold">{{ $complaint->barangay->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted text-uppercase d-block mb-1"><i class="fas fa-tag me-1 text-primary"></i>Category</small>
                            <p class="mb-0"><span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">{{ $complaint->category->name ?? 'N/A' }}</span></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted text-uppercase d-block mb-1"><i class="fas fa-exclamation-circle me-1 text-primary"></i>Priority</small>
                            <p class="mb-0"><span class="badge priority-{{ $complaint->priority_level }}">{{ ucfirst($complaint->priority_level) }}</span></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted text-uppercase d-block mb-1"><i class="fas fa-shield-alt me-1 text-primary"></i>Vulnerability</small>
                            <p class="mb-0 fw-semibold">{{ ucfirst($complaint->vulnerability_flag ?? 'None') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted text-uppercase d-block mb-1"><i class="fas fa-calendar-alt me-1 text-primary"></i>Date Filed</small>
                            <p class="mb-0 fw-semibold">{{ $complaint->created_at->format('M d, Y') }}</p>
                            <small class="text-muted">{{ $complaint->created_at->format('h:i A') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <small class="text-muted text-uppercase d-block mb-1"><i class="fas fa-clock me-1 text-primary"></i>Last Updated</small>
                            <p class="mb-0 fw-semibold">{{ $complaint->updated_at->format('M d, Y') }}</p>
                            <small class="text-muted">{{ $complaint->updated_at->format('h:i A') }}</small>
                        </div>
                    </div>
                </div>

                @if($complaint->image_path)
                <hr class="my-4">
                <div class="mb-3">
                    <small class="text-muted text-uppercase d-block mb-2"><i class="fas fa-image me-1 text-primary"></i>Attached Photo</small>
                    <div class="text-center bg-light rounded p-3">
                        <img src="{{ asset('storage/' . $complaint->image_path) }}" alt="Complaint Photo" class="img-fluid rounded shadow-sm" style="max-height: 400px; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                        <p class="text-muted small mt-2 mb-0"><i class="fas fa-search-plus me-1"></i>Click image to view full size</p>
                    </div>
                </div>
                @endif

                <hr class="my-4">
                <div class="mb-2">
                    <small class="text-muted text-uppercase d-block mb-2"><i class="fas fa-align-left me-1 text-primary"></i>Description</small>
                    <div class="description-box p-3 bg-light rounded border">
                        <p class="mb-0" style="white-space: pre-wrap; line-height: 1.7;">{{ $complaint->description }}</p>
                    </div>
                </div>

                @if($complaint->resolution_notes)
                <hr class="my-4">
                <div class="mb-2">
                    <small class="text-muted text-uppercase d-block mb-2"><i class="fas fa-check-circle me-1 text-success"></i>Resolution Notes</small>
                    <div class="resolution-box p-3 bg-success bg-opacity-10 rounded border border-success border-opacity-25">
                        <p class="mb-0 text-success" style="white-space: pre-wrap; line-height: 1.7;">{{ $complaint->resolution_notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if(!Auth::user()->isResident())
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-edit me-2 text-primary"></i>Update Status
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('complaints.updateStatus', $complaint->id) }}">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="pending" {{ $complaint->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="acknowledged" {{ $complaint->status === 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                                <option value="in_progress" {{ $complaint->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $complaint->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Resolution Notes</label>
                            <textarea class="form-control" name="resolution_notes" rows="3" placeholder="Add notes about the resolution...">{{ $complaint->resolution_notes }}</textarea>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user me-2 text-muted"></i>Complainant Info
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted text-uppercase d-block mb-1">Full Name</small>
                    <p class="mb-0 fw-semibold">{{ $complaint->user->name ?? 'N/A' }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted text-uppercase d-block mb-1">Email Address</small>
                    <p class="mb-0"><a href="mailto:{{ $complaint->user->email }}" class="text-decoration-none">{{ $complaint->user->email ?? 'N/A' }}</a></p>
                </div>
                @if($complaint->user && $complaint->user->contact_number)
                <div class="mb-3">
                    <small class="text-muted text-uppercase d-block mb-1">Contact Number</small>
                    <p class="mb-0"><a href="tel:{{ $complaint->user->contact_number }}" class="text-decoration-none">{{ $complaint->user->contact_number }}</a></p>
                </div>
                @endif
                <div class="mb-0">
                    <small class="text-muted text-uppercase d-block mb-1">Barangay</small>
                    <p class="mb-0 fw-semibold">{{ $complaint->user->barangay->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .detail-item {
        padding: 12px 16px;
        background: var(--gray-50);
        border-radius: 10px;
        height: 100%;
    }
    .detail-item small {
        font-size: 11px;
        letter-spacing: 0.5px;
    }
    .description-box {
        background: #fff;
        border: 1px solid var(--gray-200);
    }
    .resolution-box {
        background: rgba(16, 185, 129, 0.05);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
</style>
@endsection

