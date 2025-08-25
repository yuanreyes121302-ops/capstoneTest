<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandlordController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user();
        return view('landlord.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->update($request->only('first_name', 'last_name', 'email', 'gender', 'dob'));

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image && \Storage::exists('public/profile_images/' . $user->profile_image)) {
                \Storage::delete('public/profile_images/' . $user->profile_image);
            }

            $filename = uniqid() . '.' . $request->profile_image->extension();
            $request->profile_image->storeAs('public/profile_images', $filename);
            $user->profile_image = $filename;
            $user->save();
        }

        return back()->with('success', 'Profile updated successfully!');
    }

}

