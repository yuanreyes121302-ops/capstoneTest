<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{

    public function showProfile()
    {
        $tenant = Auth::user();
        return view('tenant.profile', compact('tenant'));
    }

    public function updateProfile(Request $request)
    {
        try {
            $tenant = Auth::user();

            // Check if this is a profile image update (from modal)
            if ($request->hasFile('profile_image')) {
                // DEBUG: Controller entry for image update
                Log::debug('TenantController@updateProfile image update entered', [
                    'route' => $request->route() ? $request->route()->getName() : 'unknown',
                    'user_id' => Auth::id(),
                    'user_role' => Auth::user() ? Auth::user()->role : 'unknown',
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
                Log::debug('TenantController@updateProfile validation passed', [
                    'user_id' => Auth::id(),
                    'timestamp' => now()
                ]);

                // Use DB transaction to ensure atomicity
                $result = DB::transaction(function () use ($request, $tenant) {
                    // DEBUG: Transaction started
                    Log::debug('TenantController@updateProfile transaction started', [
                        'user_id' => $tenant->id,
                        'timestamp' => now()
                    ]);

                    // Delete old image if exists
                    if ($tenant->profile_image && Storage::exists('public/profile_images/' . $tenant->profile_image)) {
                        Storage::delete('public/profile_images/' . $tenant->profile_image);
                    }

                    $filename = time() . '_' . uniqid() . '.' . $request->profile_image->extension();
                    $path = $request->profile_image->storeAs('public/profile_images', $filename);

                    if ($path && Storage::exists('public/profile_images/' . $filename)) {
                        $tenant->profile_image = $filename;
                        $tenant->save();

                        // DEBUG: Model saved
                        Log::debug('TenantController@updateProfile model saved', [
                            'user_id' => $tenant->id,
                            'profile_image' => $filename,
                            'timestamp' => now()
                        ]);

                        // Log successful upload
                        Log::info('Profile image uploaded successfully for tenant', [
                            'file_key' => 'profile_image',
                            'stored_path' => $path,
                            'filename' => $filename,
                            'user_id' => $tenant->id
                        ]);

                        // DEBUG: Transaction about to commit
                        Log::debug('TenantController@updateProfile transaction about to commit', [
                            'user_id' => $tenant->id,
                            'filename' => $filename,
                            'timestamp' => now()
                        ]);

                        return [
                            'filename' => $filename,
                            'path' => $path
                        ];
                    } else {
                        Log::error('Failed to store profile image for tenant', [
                            'filename' => $filename,
                            'user_id' => $tenant->id
                        ]);
                        throw new \Exception('Failed to upload profile image');
                    }
                });

                // DEBUG: Transaction committed successfully
                Log::debug('TenantController@updateProfile transaction committed successfully', [
                    'user_id' => $tenant->id,
                    'filename' => $result['filename'],
                    'timestamp' => now()
                ]);

                return back()->with('success', 'Profile image updated successfully!');
            }

            // Regular profile update (without image)
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $tenant->id,
                'gender' => 'required',
                'dob' => 'required|date',
                'location' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            $data = $request->only('first_name', 'last_name', 'email', 'gender', 'dob');

            // Only update location and coordinates if location is provided
            if ($request->filled('location')) {
                $data['location'] = $request->location;
                $data['latitude'] = $request->latitude;
                $data['longitude'] = $request->longitude;
            }

            $tenant->update($data);

            return back()->with('success', 'Profile updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Tenant profile update failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->except(['profile_image'])
            ]);
            return back()->withInput()->withErrors(['error' => 'Failed to update profile. Please try again.']);
        }
    }

}
