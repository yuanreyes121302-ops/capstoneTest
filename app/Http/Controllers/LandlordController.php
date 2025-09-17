<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Property;
use App\Models\Booking;

class LandlordController extends Controller
{



    public function showProfile()
    {
        $user = Auth::user();
        return view('landlord.profile', compact('user'));
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Property statistics
        $totalProperties = Property::where('user_id', $user->id)->count();
        $activeProperties = Property::where('user_id', $user->id)
            ->whereHas('bookings', function($query) {
                $query->where('contract_status', 'active');
            })->count();
        $pendingApprovals = Booking::where('landlord_id', $user->id)->where('status', 'pending')->count();
        $rejectedProperties = Booking::where('landlord_id', $user->id)->where('status', 'declined')->count();

        // Latest requests (bookings)
        $latestRequests = Booking::with(['tenant', 'property', 'room'])
            ->where('landlord_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent activity (latest bookings)
        $recentActivity = Booking::with(['tenant', 'property'])
            ->where('landlord_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('landlord.dashboard', compact(
            'totalProperties',
            'activeProperties',
            'pendingApprovals',
            'rejectedProperties',
            'latestRequests',
            'recentActivity'
        ));
    }



    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();

            // Check if this is a profile image update (from modal)
            if ($request->hasFile('profile_image')) {
                // DEBUG: Controller entry for image update
                Log::debug('LandlordController@updateProfile image update entered', [
                    'route' => $request->route() ? $request->route()->getName() : 'unknown',
                    'user_id' => auth()->id(),
                    'user_role' => auth()->user() ? auth()->user()->role : 'unknown',
                    'request_method' => $request->method(),
                    'request_keys' => array_keys($request->all()),
                    'has_profile_image' => $request->hasFile('profile_image'),
                    'timestamp' => now()
                ]);

                $request->validate([
                    'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                ], [
                    'profile_image.required' => 'Please select an image file.',
                    'profile_image.image' => 'Only image files (JPEG, PNG, JPG, GIF) are allowed.',
                    'profile_image.mimes' => 'Profile image must be in JPEG, PNG, JPG, or GIF format.',
                    'profile_image.max' => 'Profile image must be less than 2MB.',
                ]);

                // DEBUG: Validation passed
                Log::debug('LandlordController@updateProfile validation passed', [
                    'user_id' => auth()->id(),
                    'timestamp' => now()
                ]);

                // Use DB transaction to ensure atomicity
                $result = DB::transaction(function () use ($request, $user) {
                    // DEBUG: Transaction started
                    Log::debug('LandlordController@updateProfile transaction started', [
                        'user_id' => $user->id,
                        'timestamp' => now()
                    ]);

                    // Delete old image if exists
                    if ($user->profile_image && Storage::exists('public/profile_images/' . $user->profile_image)) {
                        Storage::delete('public/profile_images/' . $user->profile_image);
                    }

                    $filename = time() . '_' . uniqid() . '.' . $request->profile_image->extension();
                    $path = $request->profile_image->storeAs('public/profile_images', $filename);

                    if ($path && Storage::exists('public/profile_images/' . $filename)) {
                        $user->profile_image = $filename;
                        $user->save();

                        // DEBUG: Model saved
                        Log::debug('LandlordController@updateProfile model saved', [
                            'user_id' => $user->id,
                            'profile_image' => $filename,
                            'timestamp' => now()
                        ]);

                        // Log successful upload
                        Log::info('Profile image uploaded successfully for landlord', [
                            'file_key' => 'profile_image',
                            'stored_path' => $path,
                            'filename' => $filename,
                            'user_id' => $user->id
                        ]);

                        // DEBUG: Transaction about to commit
                        Log::debug('LandlordController@updateProfile transaction about to commit', [
                            'user_id' => $user->id,
                            'filename' => $filename,
                            'timestamp' => now()
                        ]);

                        return [
                            'filename' => $filename,
                            'path' => $path
                        ];
                    } else {
                        Log::error('Failed to store profile image for landlord', [
                            'filename' => $filename,
                            'user_id' => $user->id
                        ]);
                        throw new \Exception('Failed to upload profile image');
                    }
                });

                // DEBUG: Transaction committed successfully
                Log::debug('LandlordController@updateProfile transaction committed successfully', [
                    'user_id' => $user->id,
                    'filename' => $result['filename'],
                    'timestamp' => now()
                ]);

                return back()->with('success', 'Profile image updated successfully!');
            }

            // Regular profile update (without image)
            $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'gender' => 'required|in:male,female,other',
                'contact_number' => 'required|string|max:20',
            ]);

            $user->update($request->only('first_name', 'last_name', 'email', 'gender', 'contact_number'));

            return back()->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Landlord profile update failed for user ' . auth()->id() . ': ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update profile. Please try again.']);
        }
    }

}
