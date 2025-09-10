<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandlordUpdatedProfileController extends Controller
{
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|max:2048',
        ]);

        $user = auth()->user();

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '.' . $file->getClientOriginalExtension();

            // Save file to storage
            $file->storeAs('public/profile_images', $filename);

            // Update user record
            $user->profile_image = $filename;
            $user->save();

            // 🔥 Fix: reload the user in the session
            auth()->setUser($user->fresh());
        }

        return back()->with('success', 'Profile picture updated successfully!');
    }
}
