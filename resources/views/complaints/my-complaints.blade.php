@extends('layouts.app')
@section('title', 'My Complaints')
@section('page-title', 'My Complaints')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>My Complaints</span>
        <a href="{{ route('complaints.create') }}" class="btn btn-primary btn-sm">File New Complaint</a>
    </div>
    <div class="card-body p-0">
        @if($complaints->isEmpty())
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h5>No Complaints Filed</h5>
            <p>You haven't filed any complaints yet.</p>
            <a href="{{ route('complaints.create') }}" class="btn btn-primary">File Your First Complaint</a>
        </div>
        @else
        <div class="table-responsive recent-complaints-table">
            <table class="table mb-0">
            <thead>
                <tr>
                    <th>Complaint ID</th>
                    <th>Barangay</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Filed</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($complaints as $complaint)
                <tr>
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
