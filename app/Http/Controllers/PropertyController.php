<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = auth()->user()->properties;
        return view('landlord.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('landlord.properties.create');
    }

   public function store(Request $request)
{
    // DEBUG: Controller entry
    Log::debug('PropertyController@store entered', [
        'route' => $request->route() ? $request->route()->getName() : 'unknown',
        'user_id' => auth()->id(),
        'user_role' => auth()->user() ? auth()->user()->role : 'unknown',
        'request_method' => $request->method(),
        'request_keys' => array_keys($request->all()),
        'has_images' => $request->hasFile('images'),
        'images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
        'timestamp' => now()
    ]);

    try {
        // Validate all required fields including images
        $rules = [
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'price' => 'required|numeric|min:0',
            'room_count' => 'required|integer|min:1',
            'images' => 'required|array|min:1|max:10', // Require at least 1 image, max 10
            'images.*' => 'required|image|mimes:jpg,jpeg,png,gif,webp',
        ];

        $messages = [
            'images.required' => 'Please upload at least one property image.',
            'images.min' => 'Please upload at least one property image.',
            'images.max' => 'You can upload a maximum of 10 images.',
            'images.*.required' => 'Each uploaded file is required.',
            'images.*.image' => 'Only image files (JPG, PNG, GIF, WebP) are allowed.',
            'images.*.mimes' => 'Images must be in JPG, PNG, GIF, or WebP format.',
        ];

        // Add room validation only if rooms are provided
        if ($request->has('rooms') && is_array($request->input('rooms', [])) && !empty($request->input('rooms.name', []))) {
            $rules = array_merge($rules, [
                'rooms.name' => 'required|array|min:1',
                'rooms.name.*' => 'required|string|max:100',
                'rooms.capacity' => 'required|array',
                'rooms.capacity.*' => 'required|integer|min:1',
                'rooms.available_slots' => 'required|array',
                'rooms.available_slots.*' => 'required|integer|min:0',
                'rooms.images' => 'nullable|array',
                'rooms.images.*' => 'nullable|array|max:5',
                'rooms.images.*.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
            ]);

            $messages = array_merge($messages, [
                'rooms.name.*.required' => 'Room name is required.',
                'rooms.capacity.*.required' => 'Room capacity is required.',
                'rooms.available_slots.*.required' => 'Available slots is required.',
                'rooms.images.*.max' => 'You can upload a maximum of 5 images per room.',
                'rooms.images.*.*.image' => 'Only image files (JPG, PNG, GIF, WebP) are allowed for rooms.',
                'rooms.images.*.*.mimes' => 'Room images must be in JPG, PNG, GIF, or WebP format.',
            ]);
        }

        $request->validate($rules, $messages);

        // DEBUG: Validation passed
        Log::debug('PropertyController@store validation passed', [
            'user_id' => auth()->id(),
            'request_data' => $request->except(['images']),
            'timestamp' => now()
        ]);

        $data = $request->only(['title', 'description', 'location', 'latitude', 'longitude', 'price', 'room_count']);
        $data['user_id'] = auth()->id();

        // Use DB transaction to ensure atomicity
        $result = DB::transaction(function () use ($request, $data) {
            // DEBUG: Transaction started
            Log::debug('PropertyController@store transaction started', [
                'user_id' => auth()->id(),
                'data_to_save' => $data,
                'timestamp' => now()
            ]);

            // Create property
            $property = Property::create($data);

            if (!$property || !$property->exists) {
                throw new \Exception('Failed to save property to database');
            }

            // Create rooms from form data
            $roomsData = $request->input('rooms', []);
            $roomImages = $request->file('rooms.images', []);

            if (!empty($roomsData['name']) && is_array($roomsData['name'])) {
                foreach ($roomsData['name'] as $index => $name) {
                    $room = \App\Models\Room::create([
                        'property_id' => $property->id,
                        'name' => $name,
                        'price' => $data['price'], // Use property price
                        'capacity' => $roomsData['capacity'][$index],
                        'available_slots' => $roomsData['available_slots'][$index],
                    ]);

                    if (!$room || !$room->exists) {
                        throw new \Exception('Failed to save room to database: ' . $name);
                    }

                    // Handle room images if uploaded
                    if (isset($roomImages[$index]) && is_array($roomImages[$index])) {
                        foreach ($roomImages[$index] as $imageFile) {
                            $filename = time() . '_' . uniqid() . '_' . $imageFile->getClientOriginalName();
                            $path = $imageFile->storeAs('public/room_images', $filename);

                            if ($path && Storage::exists('public/room_images/' . $filename)) {
                                $image = $room->images()->create([
                                    'image_path' => $filename
                                ]);

                                if (!$image || !$image->exists) {
                                    Storage::delete('public/room_images/' . $filename);
                                    throw new \Exception('Failed to save room image record: ' . $filename);
                                }
                            } else {
                                throw new \Exception('Failed to store room image file: ' . $filename);
                            }
                        }
                    }
                }
            } else {
                // Create default room if no rooms specified
                $room = \App\Models\Room::create([
                    'property_id' => $property->id,
                    'name' => 'Default Room',
                    'price' => $data['price'],
                    'capacity' => 1,
                    'available_slots' => 1,
                ]);

                if (!$room || !$room->exists) {
                    throw new \Exception('Failed to save default room to database');
                }
            }

            // DEBUG: Property created
            Log::debug('PropertyController@store property created', [
                'property_id' => $property->id,
                'property_attributes' => $property->toArray(),
                'timestamp' => now()
            ]);

            // Handle image uploads with verification
            $uploadedImages = 0;
            $uploadedFiles = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('public/property_images', $filename);

                    if ($path && Storage::exists('public/property_images/' . $filename)) {
                        $image = $property->images()->create([
                            'image_path' => $filename
                        ]);

                        if ($image && $image->exists) {
                            $uploadedImages++;
                            $uploadedFiles[] = [
                                'filename' => $filename,
                                'path' => $path,
                                'image_id' => $image->id
                            ];

                            // Log successful upload
                            Log::info('Property image uploaded successfully', [
                                'file_key' => 'images[' . $index . ']',
                                'stored_path' => $path,
                                'filename' => $filename,
                                'property_id' => $property->id,
                                'image_id' => $image->id,
                                'user_id' => auth()->id()
                            ]);
                        } else {
                            // Clean up file if DB save failed
                            Storage::delete('public/property_images/' . $filename);
                            Log::error('Failed to save image record to database', [
                                'filename' => $filename,
                                'property_id' => $property->id
                            ]);
                            throw new \Exception('Failed to save image record: ' . $filename);
                        }
                    } else {
                        Log::error('Failed to store property image file', [
                            'filename' => $filename,
                            'property_id' => $property->id
                        ]);
                        throw new \Exception('Failed to store image file: ' . $filename);
                    }
                }
            }

            // Verify that at least one image was uploaded successfully
            if ($uploadedImages === 0) {
                throw new \Exception('No images were uploaded successfully');
            }

            // DEBUG: Transaction about to commit
            Log::debug('PropertyController@store transaction about to commit', [
                'property_id' => $property->id,
                'uploaded_count' => $uploadedImages,
                'uploaded_files' => $uploadedFiles,
                'timestamp' => now()
            ]);

            return [
                'property' => $property,
                'uploaded_count' => $uploadedImages,
                'uploaded_files' => $uploadedFiles
            ];
        });

        // DEBUG: Transaction committed successfully
        Log::debug('PropertyController@store transaction committed successfully', [
            'property_id' => $result['property']->id,
            'uploaded_count' => $result['uploaded_count'],
            'timestamp' => now()
        ]);

        // Success - redirect with detailed confirmation
        $message = "Property '{$result['property']->title}' created successfully with {$result['uploaded_count']} image(s) uploaded!";
        return redirect()->route('landlord.properties.index')->with('success', $message);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // DEBUG: Validation failed
        Log::debug('PropertyController@store validation failed', [
            'user_id' => auth()->id(),
            'errors' => $e->errors(),
            'timestamp' => now()
        ]);
        // Handle validation errors specifically
        return back()->withInput()->withErrors($e->errors());
    } catch (\Exception $e) {
        Log::error('Property creation failed', [
            'error' => $e->getMessage(),
            'user_id' => auth()->id(),
            'request_data' => $request->except(['images']) // Don't log file data
        ]);
        return back()->withInput()->withErrors([
            'error' => 'Failed to create property. Please check all fields and try again.'
        ]);
    }
}

    public function edit(Property $property)
    {
        $this->authorize('update', $property);
        return view('landlord.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        try {
            $this->authorize('update', $property);

            $request->validate([
                'title' => 'required|string|max:100',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'price' => 'required|numeric|min:0',
                'room_count' => 'required|integer|min:1',
                'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
            ], [
                'images.*.image' => 'Only image files (JPG, PNG, GIF, WebP) are allowed.',
                'images.*.mimes' => 'Images must be in JPG, PNG, GIF, or WebP format.',
            ]);

            $data = $request->only(['title', 'description', 'location', 'latitude', 'longitude', 'price', 'room_count']);

            // Use DB transaction to ensure atomicity
            $result = DB::transaction(function () use ($request, $property, $data) {
                // Update property data
                $property->update($data);

                $uploadedImages = 0;
                $uploadedFiles = [];

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $file) {
                        $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('public/property_images', $filename);

                        if ($path && Storage::exists('public/property_images/' . $filename)) {
                            $image = $property->images()->create([
                                'image_path' => $filename
                            ]);

                            if ($image && $image->exists) {
                                $uploadedImages++;
                                $uploadedFiles[] = [
                                    'filename' => $filename,
                                    'path' => $path,
                                    'image_id' => $image->id
                                ];

                                // Log successful upload
                                Log::info('Property image uploaded successfully during update', [
                                    'file_key' => 'images[' . $index . ']',
                                    'stored_path' => $path,
                                    'filename' => $filename,
                                    'property_id' => $property->id,
                                    'image_id' => $image->id,
                                    'user_id' => auth()->id()
                                ]);
                            } else {
                                // Clean up file if DB save failed
                                Storage::delete('public/property_images/' . $filename);
                                Log::error('Failed to save image record to database during update', [
                                    'filename' => $filename,
                                    'property_id' => $property->id
                                ]);
                                throw new \Exception('Failed to save image record: ' . $filename);
                            }
                        } else {
                            Log::error('Failed to store property image file during update', [
                                'filename' => $filename,
                                'property_id' => $property->id
                            ]);
                            throw new \Exception('Failed to store image file: ' . $filename);
                        }
                    }
                }

                return [
                    'property' => $property,
                    'uploaded_count' => $uploadedImages,
                    'uploaded_files' => $uploadedFiles
                ];
            });

            $message = 'Property updated successfully!';
            if ($result['uploaded_count'] > 0) {
                $message .= " {$result['uploaded_count']} new image(s) uploaded.";
            }

            return redirect()->route('landlord.properties.index')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Property update failed', [
                'error' => $e->getMessage(),
                'property_id' => $property->id,
                'user_id' => auth()->id(),
                'request_data' => $request->except(['images'])
            ]);
            return back()->withInput()->withErrors(['error' => 'Failed to update property. Please try again.']);
        }
    }

    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);
        if ($property->image && \Storage::exists('public/property_images/' . $property->image)) {
            \Storage::delete('public/property_images/' . $property->image);
        }

        $property->delete();
        return back()->with('success', 'Property deleted.');
    }

    public function deleteImage(PropertyImage $image)
    {
        // Optional: check ownership
        $this->authorize('update', $image->property);

        // Delete image file from storage
        Storage::delete('public/property_images/' . $image->image_path);

        // Delete DB record
        $image->delete();

        return back()->with('success', 'Image deleted.');
    }

    public function indexForTenant()
    {
        $properties = Property::with('user')->latest()->get(); // show all landlord properties
        return view('tenant.properties.index', compact('properties'));
    }

    public function show(Property $property)
    {
        $property->load('rooms', 'rooms.images', 'user', 'images', 'reviews.tenant');

        // Log property details load
        Log::info('Property details viewed', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'user_role' => auth()->user() ? auth()->user()->role : 'unknown',
            'timestamp' => now()
        ]);

        return view('tenant.properties.show', compact('property'));
    }

    public function viewMap($id)
    {
        $property = Property::findOrFail($id);
        return view('tenant.properties.map', compact('property'));
    }

}


