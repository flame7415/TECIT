<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminUserController extends Controller
{
    /**
     * Display a listing of users (for municipal admin).
     */
    public function index(Request $request)
    {
        $query = User::with('barangay')
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Barangay filter
        if ($request->filled('barangay_id')) {
            $query->where('barangay_id', $request->input('barangay_id'));
        }

        $users = $query->paginate(20)->withQueryString();
        $barangays = Barangay::all();

        // Summary counts
        $totalUsers = User::count();
        $municipalAdmins = User::where('role', 'municipal_admin')->count();
        $residents = User::where('role', 'resident')->count();

        return view('admin.users.index', compact(
            'users',
            'barangays',
            'totalUsers',
            'municipalAdmins',
            'residents'
        ));
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent deleting municipal admin
        if ($user->role === 'municipal_admin') {
            return back()->with('error', 'Cannot delete Municipal Admin.');
        }

        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
