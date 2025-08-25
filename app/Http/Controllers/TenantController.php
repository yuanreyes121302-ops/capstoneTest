<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{

    public function showProfile()
    {
        $tenant = Auth::user();
        return view('tenant.profile', compact('tenant'));
    }

    public function updateProfile(Request $request)
    {
        $tenant = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $tenant->id,
            'gender' => 'required',
            'dob' => 'required|date',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        $data = $request->only('first_name', 'last_name', 'email', 'gender', 'dob');

        // Handle image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/profile_images', $filename);

            // Optional: delete old image
            if ($tenant->profile_image) {
                Storage::delete('public/profile_images/' . $tenant->profile_image);
            }

            $data['profile_image'] = $filename;
        }

        $tenant->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

}
