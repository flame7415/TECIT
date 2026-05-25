@extends('layouts.app')
@section('title', 'Municipal Dashboard')
@section('page-title', 'Municipal Dashboard')

@section('content')
<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                <i class="fas fa-users-cog me-2"></i> Manage Users
            </a>
            <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i> Reports
            </a>
            <a href="{{ route('complaints.escalated') }}" class="btn btn-danger">
                <i class="fas fa-exclamation-triangle me-2"></i> Escalated
            </a>
        </div>
    </div>
</div>



<!-- Non-chart: High-Risk Barangays -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle me-2 text-danger"></i> High-Risk Barangays
            </div>
            <div class="card-body p-0">
                @if($highRiskBarangays->isEmpty())
                    <div class="empty-state py-5">
                        <i class="fas fa-check-circle text-success"></i>
                        <h5>All Clear</h5>
                        <p>No high-priority complaints across any barangay.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Barangay</th>
                                    <th class="pe-4 text-end">High Priority Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($highRiskBarangays as $item)
                                    <tr>
                                        <td class="ps-4 fw-medium">{{ $item['barangay'] }}</td>
                                        <td class="pe-4 text-end">
                                            <span class="badge bg-danger">{{ $item['high_priority_count'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


<!-- Metrics Row -->
<div class="row">
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:var(--primary);color:#fff;">
                    <i class="fas fa-stopwatch"></i>
                </div>
                <div>
                    <div class="text-muted small">Average Resolution Time</div>
                    <div class="fw-bold fs-4">{{ $avgResolutionDays }} <span class="fs-6 fw-normal text-muted">days</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:var(--info);color:#fff;">
                    <i class="fas fa-tag"></i>
                </div>
                <div>
                    <div class="text-muted small">Most Common Type</div>
                    <div class="fw-bold fs-5">{{ $mostCommonType ? $mostCommonType->category->name : 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:var(--success);color:#fff;">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <div class="text-muted small">Reports</div>
                    <div class="fw-bold fs-4">{{ $totalComplaints }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reports Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2 text-primary"></i> All Complaints


    </div>
    <div class="card-body p-0">
        @if($allComplaints->isEmpty())
            <div class="empty-state py-5">
                <i class="fas fa-check-circle text-success"></i>
                <h5>No Complaints</h5>
                <p>There are currently no complaints in the system.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Complaint ID</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allComplaints->sortBy('created_at') as $complaint)
                            <tr>
                                <td><strong>#{{ $loop->iteration }}</strong></td>
                                <td class="fw-bold">{{ $complaint->complaint_id }}</td>
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
                                    <span class="badge priority-{{ $complaint->priority_level }}">{{ ucfirst($complaint->priority_level) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $complaint->created_at->format('M d, Y') }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection




