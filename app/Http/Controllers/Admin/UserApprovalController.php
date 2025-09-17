<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    public function index()
    {
        $pendingUsers = User::where('is_approved', false)->get();
        return view('admin.user-approvals', compact('pendingUsers'));
    }

     public function approve(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_approved = true;
        $user->save();

        return back()->with('success', 'User approved.');
    }

    public function deny(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User denied and deleted.');
    }

    
    public function allUsers(Request $request)
    {
        $query = User::where('is_approved', true);

        // Role filter
        if ($request->has('role') && in_array($request->role, ['admin', 'landlord', 'tenant'])) {
            $query->where('role', $request->role);
        }

        // Search filter
        if ($request->has('search') && $request->search !== null) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                ->orWhere('last_name', 'like', "%{$searchTerm}%")
                ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Apply pagination (10 users per page)
        $users = $query->paginate(10)->withQueryString(); // preserves filters in pagination links

        // Count totals for summary (not paginated)
        $admins = User::where('is_approved', true)->where('role', 'admin')->count();
        $landlords = User::where('is_approved', true)->where('role', 'landlord')->count();
        $tenants = User::where('is_approved', true)->where('role', 'tenant')->count();
        $total = User::where('is_approved', true)->count();
        $pendingUsers = User::where('is_approved', false)->get();

        return view('admin.all-users', compact('users', 'total', 'admins', 'landlords', 'tenants', 'pendingUsers'));
    }

    public function destroy(User $user)
    {
        // Optional: prevent admin from deleting themselves
        if (auth()->id() === $user->id) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    public function dashboard()
    {
        $totalUsers = User::where('is_approved', true)->count();
        $pendingApprovals = User::where('is_approved', false)->count();
        $activeAdmins = User::where('role', 'admin')->where('is_approved', true)->count();

        return view('admin.dashboard', compact('totalUsers', 'pendingApprovals', 'activeAdmins'));
    }

    public function pendingCount()
    {
        $count = User::where('is_approved', false)->count();
        return response()->json(['count' => $count]);
    }


}

