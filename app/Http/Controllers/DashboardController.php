<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Barangay;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isMunicipalAdmin()) {
            return $this->municipalDashboard();
        }

        return $this->residentDashboard();
    }

    public function residentDashboard()
    {
        $user = Auth::user();
        $complaints = Complaint::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $totalComplaints = Complaint::where('user_id', $user->id)->count();
        $resolvedComplaints = Complaint::where('user_id', $user->id)
            ->where('status', 'resolved')
            ->count();
        $pendingComplaints = Complaint::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'acknowledged', 'in_progress'])
            ->count();

        return view('dashboard.resident', compact(
            'complaints',
            'totalComplaints',
            'resolvedComplaints',
            'pendingComplaints'
        ));
    }

    public function municipalDashboard()
    {
        // Average resolution time
        $resolvedComplaints = Complaint::whereNotNull('resolved_at')->get();
        $avgResolutionDays = 0;
        if ($resolvedComplaints->count() > 0) {
            $totalDays = $resolvedComplaints->sum(function ($complaint) {
                return $complaint->created_at->diffInDays($complaint->resolved_at);
            });
            $avgResolutionDays = round($totalDays / $resolvedComplaints->count(), 1);
        }

        // Most common complaint type
        $mostCommonType = Complaint::select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->with('category')
            ->first();

        // High-risk barangays (barangays with most high-priority complaints)
        $highRiskBarangays = Complaint::select('barangay_id', DB::raw('count(*) as total'))
            ->whereIn('priority_level', ['high', 'critical'])
            ->groupBy('barangay_id')
            ->orderByDesc('total')
            ->with('barangay')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'barangay' => $item->barangay->name,
                    'high_priority_count' => $item->total
                ];
            });

        $allComplaints = Complaint::with(['category', 'barangay'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalComplaints = $allComplaints->count();

        return view('dashboard.municipal', compact(
            'avgResolutionDays',
            'mostCommonType',
            'highRiskBarangays',
            'allComplaints',
            'totalComplaints'
        ));
    }

}
