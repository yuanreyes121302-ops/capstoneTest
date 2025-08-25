<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function showAddAdminForm()
    {
        return view('admin.add-admin');
    }

    public function storeNewAdmin(Request $request)
    {
        $validated = $request->validate([
            'user_id'    => 'required|unique:users,user_id',
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'email'      => 'required|email|unique:users,email',
            'gender'     => 'required|in:male,female,other',
            'dob'        => 'required|date',
            'password'   => 'required|min:6|confirmed',
        ]);

        $admin = new User();
        $admin->user_id = $validated['user_id'];
        $admin->first_name = $validated['first_name'];
        $admin->last_name = $validated['last_name'];
        $admin->email = $validated['email'];
        $admin->gender = $validated['gender'];
        $admin->dob = $validated['dob'];
        $admin->password = Hash::make($validated['password']);
        $admin->role = 'admin';
        $admin->is_approved = true;
        $admin->save();

        return redirect()->route('admin.users.all')->with('success', 'New admin added successfully.');
    }
}
