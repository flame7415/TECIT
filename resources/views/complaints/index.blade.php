@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<!-- Status Filters for Admin -->
@php
$statusCounts = [
    'all' => $complaints->total(),
    'pending' => \App\Models\Complaint::where('status', 'pending')->count(),
    'acknowledged' => \App\Models\Complaint::where('status', 'acknowledged')->count(),
    'in_progress' => \App\Models\Complaint::where('status', 'in_progress')->count(),
    'resolved' => \App\Models\Complaint::where('status', 'resolved')->count(),
    'closed' => \App\Models\Complaint::where('status', 'closed')->count(),
];
@endphp

        @if(!Auth::user()->isResident())
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12">
                <div class="mb-2">Filters</div>

                @php

                    $selectedStatuses = request()->input('statuses', []);
                    if (!is_array($selectedStatuses)) {
                        $selectedStatuses = [$selectedStatuses];
                    }
                    $selectedStatuses = array_values(array_filter($selectedStatuses, fn ($s) => is_string($s) && $s !== ''));

                    $toggle = function (string $status) use ($selectedStatuses) {
                        if (in_array($status, $selectedStatuses, true)) {
                            $new = array_values(array_filter($selectedStatuses, fn ($s) => $s !== $status));
                        } else {
                            $new = array_values(array_unique(array_merge($selectedStatuses, [$status])));
                        }
                        return $new;
                    };
                @endphp

                <div class="d-flex flex-wrap gap-2 justify-content-start align-items-center">
                    <a href="{{ route('complaints.index') }}" class="btn {{ empty($selectedStatuses) ? 'btn-primary' : 'btn-outline-secondary' }}">
                        All <span class="badge bg-white text-dark ms-1">{{ $statusCounts['all'] }}</span>
                    </a>


                    <a href="{{ route('complaints.index', ['statuses' => $toggle('pending')]) }}"
                       class="btn {{ in_array('pending', $selectedStatuses, true) ? 'btn-warning' : 'btn-outline-warning' }}">
                        Pending <span class="badge bg-white text-dark ms-1">{{ $statusCounts['pending'] }}</span>
                    </a>

                    <a href="{{ route('complaints.index', ['statuses' => $toggle('acknowledged')]) }}"
                       class="btn {{ in_array('acknowledged', $selectedStatuses, true) ? 'btn-info' : 'btn-outline-info' }}">
                        Acknowledged <span class="badge bg-white text-dark ms-1">{{ $statusCounts['acknowledged'] }}</span>
                    </a>

                    <a href="{{ route('complaints.index', ['statuses' => $toggle('in_progress')]) }}"
                       class="btn {{ in_array('in_progress', $selectedStatuses, true) ? 'btn-primary' : 'btn-outline-primary' }}">
                        In Progress <span class="badge bg-white text-dark ms-1">{{ $statusCounts['in_progress'] }}</span>
                    </a>

                    <a href="{{ route('complaints.index', ['statuses' => $toggle('resolved')]) }}"
                       class="btn {{ in_array('resolved', $selectedStatuses, true) ? 'btn-success' : 'btn-outline-success' }}">
                        Resolved <span class="badge bg-white text-dark ms-1">{{ $statusCounts['resolved'] }}</span>
                    </a>
                    <a href="{{ route('complaints.index', ['statuses' => $toggle('closed')]) }}"
                       class="btn {{ in_array('closed', $selectedStatuses, true) ? 'btn-secondary' : 'btn-outline-secondary' }}">
                        Closed <span class="badge bg-white text-dark ms-1">{{ $statusCounts['closed'] }}</span>
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Reports</span>
        @if(!Auth::user()->isResident())
            @php
                $exportStatuses = request()->input('statuses', []);
                if (!is_array($exportStatuses)) {
                    $exportStatuses = [$exportStatuses];
                }
                $exportStatuses = array_values(array_filter($exportStatuses, fn ($s) => is_string($s) && $s !== ''));

                $hasFilters = count($exportStatuses) > 0;
                $exportUrl = $hasFilters ? route('complaints.exportCsv', ['statuses' => $exportStatuses]) : '#';
            @endphp
            <a href="{{ $exportUrl }}" class="btn btn-outline-success btn-sm {{ $hasFilters ? '' : 'disabled' }}" {{ $hasFilters ? '' : 'aria-disabled="true" tabindex="-1" style="pointer-events:none"' }}>
                Export CSV
            </a>
        @endif
        @if(Auth::user()->isResident())
        <a href="{{ route('complaints.create') }}" class="btn btn-primary btn-sm">New Complaint</a>
        @endif
    </div>
    <div class="card-body p-0">
        @if($complaints->isEmpty())
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h5>No Complaints</h5>
            <p>There are no complaints to display.</p>
        </div>
        @else
        <div class="table-responsive recent-complaints-table">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Complaint ID</th>
                        <th>Barangay</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Escalated</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
@foreach($complaints->sortBy('created_at') as $complaint)
                    <tr>
                        <td><strong>#{{ $loop->iteration }}</strong></td>
                        <td><strong>{{ $complaint->complaint_id }}</strong></td>
                        <td><span class="badge bg-secondary">{{ $complaint->barangay->name ?? 'N/A' }}</span></td>
                        <td><span class="badge bg-info">{{ $complaint->category->name ?? 'N/A' }}</span></td>
                        <td>
                            @switch($complaint->status)
                                @case('pending')
                                    <span class="badge bg-warning">Pending</span>
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
                        <td><span class="badge priority-{{ $complaint->priority_level }}">{{ ucfirst($complaint->priority_level) }}</span></td>
                        <td>
                            @if($complaint->escalated_to_municipal)
                            <span class="badge bg-danger">Yes</span>
                            @else
                            <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                        <td><a href="{{ route('complaints.show', $complaint->id) }}" class="btn btn-sm btn-info">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-body text-center">
            {{ $complaints->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

