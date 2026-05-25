@extends('layouts.app')
@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- Summary Cards -->
<div class="row stats-row mb-4">
    <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
        <div class="stats-card primary">
            <h5>Total Users</h5>
            <h2>{{ $totalUsers }}</h2>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
        <div class="stats-card danger">
            <h5>Municipal Admins</h5>
            <h2>{{ $municipalAdmins }}</h2>
            <div class="icon"><i class="fas fa-user-shield"></i></div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="stats-card info">
            <h5>Residents</h5>
            <h2>{{ $residents }}</h2>
            <div class="icon"><i class="fas fa-user"></i></div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Name or email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="municipal_admin" {{ request('role') === 'municipal_admin' ? 'selected' : '' }}>Municipal Admin</option>
                    <option value="resident" {{ request('role') === 'resident' ? 'selected' : '' }}>Resident</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Barangay</label>
                <select name="barangay_id" class="form-select">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                    <option value="{{ $barangay->id }}" {{ request('barangay_id') == $barangay->id ? 'selected' : '' }}>{{ $barangay->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
        @if(request()->hasAny(['search', 'role', 'barangay_id']))
        <div class="mt-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-times me-1"></i> Clear Filters
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Users</span>
        <span class="text-muted small">{{ $users->total() }} total</span>
    </div>
    <div class="card-body p-0">
        @if($users->isEmpty())
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <h5>No Users Found</h5>
            <p>No users match your current filters.</p>
            @if(request()->hasAny(['search', 'role', 'barangay_id']))
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
            @endif
        </div>
        @else
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Barangay</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td><small>{{ $user->email }}</small></td>
                        <td>
                            @if($user->role === 'municipal_admin')
                            <span class="badge bg-danger">Municipal</span>
                            @else
                            <span class="badge bg-info">Resident</span>
                            @endif
                        </td>
                        <td>{{ $user->barangay ? $user->barangay->name : '-' }}</td>
                        <td><small>{{ $user->created_at->format('M d, Y') }}</small></td>
                        <td>
                            @if($user->role !== 'municipal_admin' && $user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-body text-center">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

