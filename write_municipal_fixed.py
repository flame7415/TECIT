close = '<' + '/div>'

content = r"""@extends('layouts.app')
@section('title', 'Municipal Dashboard')
@section('page-title', 'Municipal Admin Dashboard')

@section('content')
<!-- Action Bar -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                <i class="fas fa-users-cog me-2"></i> Manage Users
            </a>
        </div>
</div>

<!-- Stats Row -->
<div class="row stats-row mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stats-card primary">
            <h5>Total Complaints</h5>
            <h2>{{ $totalComplaints }}</h2>
            <div class="icon"><i class="fas fa-file-alt"></i></div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stats-card success">
            <h5>Resolved</h5>
            <h2>{{ $resolvedComplaints }}</h2>
            <div class="icon"><i class="fas fa-check"></i></div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stats-card warning">
            <h5>Pending</h5>
            <h2>{{ $pendingComplaints }}</h2>
            <div class="icon"><i class="fas fa-clock"></i></div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stats-card danger">
            <h5>Escalated</h5>
            <h2>{{ $escalatedComplaints }}</h2>
            <div class="icon"><i class="fas fa-exclamation"></i></div>
    </div>

<!-- Charts Row 1 -->
<div class="row mb-4">
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2 text-primary"></i> Complaints Per Barangay
            </div>
            <div class="card-body">
                <canvas id="barangayChart" height="200"></canvas>
            </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2 text-primary"></i> Complaints by Category
            </div>
            <div class="card-body">
                <canvas id="categoryChart" height="200"></canvas>
            </div>
    </div>

<!-- Charts Row 2 -->
<div class="row mb-4">
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-line me-2 text-primary"></i> Complaints (Last 7 Days)
            </div>
            <div class="card-body">
                <canvas id="dailyChart" height="150"></canvas>
            </div>
    </div>
    <div class="col-md-6">
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

<!-- Resolution Time -->
<div class="row">
    <div class="col-md-4">
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const complaintsPerBarangay = @json($complaintsPerBarangay);
    const complaintsByCategory = @json($complaintsByCategory);
    const complaintsByDay = @json($complaintsByDay);

    new Chart(document.getElementById('barangayChart'), {
        type: 'bar',
        data: {
            labels: complaintsPerBarangay.map(i => i.barangay),
            datasets: [{
                label: 'Complaints',
                data: complaintsPerBarangay.map(i => i.total),
                backgroundColor: '#4f46e5',
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: complaintsByCategory.map(i => i.name),
            datasets: [{
                data: complaintsByCategory.map(i => i.count),
                backgroundColor: ['#ef4444', '#3b82f6', '#f59e0b', '#10b981', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
            }
        }
    });

    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: complaintsByDay.map(i => i.date),
            datasets: [{
                label: 'Complaints',
                data: complaintsByDay.map(i => i.count),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>
@endsection""".replace(close, close)

with open('resources/views/dashboard/municipal.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("OK")
