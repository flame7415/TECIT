@extends('layouts.app')
@section('title', 'Escalated Complaints')
@section('page-title', 'Escalated Complaints')

@section('content')
<div class="card">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
        <span><i class="fas fa-exclamation-triangle me-2"></i> Escalated Complaints</span>
        <span class="badge bg-white text-danger">{{ $complaints->total() }} Total</span>
    </div>
    <div class="card-body p-0">
        @if($complaints->isEmpty())
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <h5>No Escalated Complaints</h5>
            <p>There are no escalated complaints at the moment.</p>
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
                        <th>Days Open</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
@foreach($complaints->sortBy('created_at') as $complaint)
                    @php
                        $daysOpen = $complaint->created_at->diffInDays(now());
                        $urgencyClass = '';
                        if ($daysOpen > 14) {
                            $urgencyClass = 'table-danger';
                        } elseif ($daysOpen > 7) {
                            $urgencyClass = 'table-warning';
                        }
                    @endphp
                    <tr class="{{ $urgencyClass }}">
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
                            @if($daysOpen > 14)
                                <span class="badge bg-danger">{{ $daysOpen }} days</span>
                            @elseif($daysOpen > 7)
                                <span class="badge bg-warning">{{ $daysOpen }} days</span>
                            @else
                                <span class="badge bg-secondary">{{ $daysOpen }} days</span>
                            @endif
                        </td>
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

