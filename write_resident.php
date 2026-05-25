<?php
$path = __DIR__ . '/resources/views/dashboard/resident.blade.php';

$content = <<<'EOF'
@extends('layouts.app')
@section('title', 'My Dashboard')
@section('page-title', 'Resident Dashboard')

@section('content')
<!-- Stats Row -->
<div class="row stats-row mb-4">
    <div class="col-md-4">
        <div class="stats-card primary">
            <h5>Total Complaints</h5>
            <h2>{{ $totalComplaints }}</h2>
            <div class="icon"><i class="fas fa-file-alt"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card success">
            <h5>Resolved</h5>
            <h2>{{ $resolvedComplaints }}</h2>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card warning">
            <h5>Pending</h5>
            <h2>{{ $pendingComplaints }}</h2>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left: Complaints Table -->
    <div class="col-lg-8 mb-4 mb-lg-0">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2 text-muted"></i>My Recent Complaints</span>
                <a href="{{ route('complaints.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>New Complaint
                </a>
            </div>
            <div class="card-body p-0">
                @if($complaints->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon-wrapper mb-3">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h5>No Complaints Yet</h5>
                    <p class="text-muted mb-4">You haven't filed any complaints yet. Get started by submitting your first complaint.</p>
                    <a href="{{ route('complaints.create') }}" class="btn btn-primary px-4">
                        <i class="fas fa-plus-circle me-2"></i>File a Complaint
                    </a>
                </div>
                @else
                <div class="table-responsive recent-complaints-table">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($complaints as $complaint)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">{{ $complaint->complaint_id }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                        {{ $complaint->category->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
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
                                </td>
                                <td>
                                    <span class="badge priority-{{ $complaint->priority_level }}">
                                        {{ ucfirst($complaint->priority_level) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $complaint->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('complaints.show', $complaint->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($complaints->count() >= 5)
                <div class="card-footer bg-white text-center py-3">
                    <a href="{{ route('complaints.my') }}" class="text-decoration-none">
                        <small>View All Complaints <i class="fas fa-arrow-right ms-1"></i></small>
                    </a>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Right: Sidebar Cards -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
            </div>
            <div class="card-body">
                <a href="{{ route('complaints.create') }}" class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-plus-circle me-2"></i>File a Complaint
                </a>
                <a href="{{ route('complaints.my') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-folder-open me-2"></i>View All My Complaints
                </a>
            </div>
        </div>

        <!-- Tips -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <i class="fas fa-lightbulb me-2"></i>Tips for Filing
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-3">
                        <i class="fas fa-map-marker-alt text-info mt-1 me-3"></i>
                        <small>Provide accurate location details within your barangay</small>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="fas fa-align-left text-info mt-1 me-3"></i>
                        <small>Be specific about the incident and what happened</small>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="fas fa-calendar-alt text-info mt-1 me-3"></i>
                        <small>Include date and time when the incident occurred</small>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="fas fa-camera text-info mt-1 me-3"></i>
                        <small>Attach a clear photo for better documentation</small>
                    </li>
                    <li class="d-flex mb-0">
                        <i class="fas fa-phone text-info mt-1 me-3"></i>
                        <small>Provide a working contact number for follow-up</small>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Response Time -->
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-clock me-2"></i>Expected Response Time
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Response times vary based on complaint priority level:</p>
                <div class="d-flex align-items-center mb-2">
                    <span class="badge priority-critical me-2" style="min-width: 70px;">Critical</span>
                    <small>Within 24 hours</small>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <span class="badge priority-high me-2" style="min-width: 70px;">High</span>
                    <small>2 - 3 days</small>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <span class="badge priority-medium me-2" style="min-width: 70px;">Medium</span>
                    <small>Within 1 week</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge priority-low me-2" style="min-width: 70px;">Low</span>
                    <small>Within 2 weeks</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--gray-500);
    }
    .empty-icon-wrapper {
        width: 80px;
        height: 80px;
        background: var(--gray-100);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .empty-icon-wrapper i {
        font-size: 32px;
        color: var(--gray-400);
    }
    .recent-complaints-table table tbody td {
        padding: 14px 16px;
        font-size: 13px;
    }
    .recent-complaints-table .badge {
        font-size: 11px;
        padding: 5px 10px;
        font-weight: 500;
    }
    .card-footer a {
        color: var(--primary);
        font-weight: 500;
    }
    .card-footer a:hover {
        color: var(--primary-hover);
    }
</style>
@endsection
EOF;

file_put_contents($path, $content);
echo "Written " . strlen($content) . " bytes to " . $path;

