<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Barangay;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isMunicipalAdmin()) {
            $query = Complaint::with(['user', 'barangay', 'category'])
                ->orderBy('priority_score', 'desc');
        } elseif ($user->isBarangayAdmin()) {
            $query = Complaint::with(['user', 'barangay', 'category'])
                ->where('barangay_id', $user->barangay_id)
                ->orderBy('priority_score', 'desc');
        } else {
            $query = Complaint::with(['barangay', 'category'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc');
        }

        // Status filter for admin users (supports multi-select)
        if (!$user->isResident() && $request->filled('statuses')) {
            $statuses = $request->input('statuses');
            if (!is_array($statuses)) {
                $statuses = [$statuses];
            }

            $statuses = array_values(array_filter($statuses, fn($s) => is_string($s) && $s !== ''));

            if (!empty($statuses)) {
                $query->whereIn('status', $statuses);
            }
        }

        // Optional additional filters for admin reports
        if (!$user->isResident()) {
            $barangayId = $request->input('barangay_id');
            if (!empty($barangayId) && is_numeric($barangayId)) {
                $query->where('barangay_id', $barangayId);
            }

            $categoryId = $request->input('category_id');
            if (!empty($categoryId) && is_numeric($categoryId)) {
                $query->where('category_id', $categoryId);
            }

            $priorityLevels = $request->input('priority_levels', []);
            if (!empty($priorityLevels)) {
                if (!is_array($priorityLevels)) {
                    $priorityLevels = [$priorityLevels];
                }
                $priorityLevels = array_values(array_filter($priorityLevels, fn($p) => is_string($p) && $p !== ''));

                if (!empty($priorityLevels)) {
                    $query->whereIn('priority_level', $priorityLevels);
                }
            }

            $fromDate = $request->input('from_date');
            if (!empty($fromDate)) {
                $query->whereDate('created_at', '>=', $fromDate);
            }

            $toDate = $request->input('to_date');
            if (!empty($toDate)) {
                $query->whereDate('created_at', '<=', $toDate);
            }
        }


        $complaints = $query->paginate(20)->withQueryString();

        return view('complaints.index', compact('complaints'));
    }

    public function create()
    {
        $categories = Category::all();
        $barangays = Barangay::all();

        return view('complaints.create', compact('categories', 'barangays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barangay_id' => 'required|exists:barangays,id',
            // "Other" options come as a special marker and must be mapped to the real categories.id
            'category_id' => 'required',
            'description' => 'required|string|min:20',
            'vulnerability_flag' => 'nullable|in:elderly,PWD,none',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $categoryId = $request->input('category_id');

        if ($categoryId === '__other__') {
            // Some deployments may not have the `description` column on `categories`.
            // Create the "Other" category using only columns that are guaranteed to exist.
            $otherCategory = Category::firstOrCreate(
                ['name' => 'Other'],
                ['weight' => 3]
            );
            $categoryId = $otherCategory->id;
        } else {

            // For non-"Other" values, enforce existence
            if (!Category::whereKey($categoryId)->exists()) {
                return back()->withErrors(['category_id' => 'Selected category is invalid.'])->withInput();
            }
        }

        // Generate complaint ID: COMP-YYYYMMDD-XXXX
        $date = Carbon::now()->format('Ymd');
        $countToday = Complaint::whereDate('created_at', Carbon::today())->count() + 1;
        $complaintId = 'COMP-' . $date . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('complaints', 'public');
        }

        $complaint = Complaint::create([
            'complaint_id' => $complaintId,
            'user_id' => Auth::id(),
            'barangay_id' => $request->barangay_id,
            'category_id' => $categoryId,
            'description' => $request->description,
            'image_path' => $imagePath,
            'vulnerability_flag' => $request->vulnerability_flag ?? 'none',
            'status' => 'pending',
        ]);

        // Calculate priority score
        $complaint->calculatePriorityScore();
        $complaint->save();

        return redirect()->route('complaints.show', $complaint->id)
            ->with('success', 'Complaint submitted successfully!');
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['user', 'barangay', 'category']);

        return view('complaints.show', compact('complaint'));
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:pending,acknowledged,in_progress,resolved,closed',
            'resolution_notes' => 'nullable|string',
        ]);

        $complaint->status = $request->status;

        if ($request->status === 'resolved') {
            $complaint->resolved_at = Carbon::now();
        }

        if ($request->resolution_notes) {
            $complaint->resolution_notes = $request->resolution_notes;
        }

        $complaint->save();

        // Check for escalation
        $complaint->checkEscalation();
        $complaint->save();

        return back()->with('success', 'Complaint status updated!');
    }

    public function escalated()
    {
        $complaints = Complaint::with(['user', 'barangay', 'category'])
            ->where('escalated_to_municipal', true)
            ->orderBy('priority_score', 'desc')
            ->paginate(20);

        return view('complaints.escalated', compact('complaints'));
    }

    public function myComplaints()
    {
        $complaints = Complaint::with(['barangay', 'category'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('complaints.my-complaints', compact('complaints'));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();

        // Build same base query as index, but without pagination
        if ($user->isMunicipalAdmin()) {
            $query = Complaint::with(['user', 'barangay', 'category'])
                ->orderBy('priority_score', 'desc');
        } elseif ($user->isBarangayAdmin()) {
            $query = Complaint::with(['user', 'barangay', 'category'])
                ->where('barangay_id', $user->barangay_id)
                ->orderBy('priority_score', 'desc');
        } else {
            // residents should not export municipal reports
            abort(403);
        }

        // Only export filtered results
        $statuses = $request->input('statuses', []);
        if (!is_array($statuses)) {
            $statuses = [$statuses];
        }
        $statuses = array_values(array_filter($statuses, fn($s) => is_string($s) && $s !== ''));
        if (empty($statuses)) {
            abort(403, 'Filtered export only. Please select at least one status filter.');
        }

        $query->whereIn('status', $statuses);

        // Optional additional filters
        $barangayId = $request->input('barangay_id');
        if (!empty($barangayId) && is_numeric($barangayId)) {
            $query->where('barangay_id', $barangayId);
        }

        $categoryId = $request->input('category_id');
        if (!empty($categoryId) && is_numeric($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        $priorityLevels = $request->input('priority_levels', []);
        if (!is_array($priorityLevels)) {
            $priorityLevels = [$priorityLevels];
        }
        $priorityLevels = array_values(array_filter($priorityLevels, fn($p) => is_string($p) && $p !== ''));
        if (!empty($priorityLevels)) {
            $query->whereIn('priority_level', $priorityLevels);
        }

        $fromDate = $request->input('from_date');
        if (!empty($fromDate)) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        $toDate = $request->input('to_date');
        if (!empty($toDate)) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $complaints = $query->orderBy('created_at', 'asc')->get();

        $filename = 'municipal-complaints-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = [
            '#',
            'Complaint ID',
            'Barangay',
            'Category',
            'Status',
            'Priority',
            'Escalated',
            'Date',
        ];

        $callback = function () use ($complaints, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);

            $i = 0;
            foreach ($complaints as $complaint) {
                $i++;
                fputcsv($out, [
                    $i,
                    $complaint->barangay->name ?? 'N/A',
                    $complaint->category->name ?? 'N/A',
                    ucfirst($complaint->status),
                    ucfirst($complaint->priority_level),
                    $complaint->escalated_to_municipal ? 'Yes' : 'No',
                    optional($complaint->created_at)->format('M d, Y'),
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}

