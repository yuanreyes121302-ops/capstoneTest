<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;

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
    $request->validate([
        'title' => 'required|string|max:100',
        'description' => 'required|string',
        'location' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'room_count' => 'required|integer|min:1',
        'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048',
    ]);

    $data = $request->all();
    $data['user_id'] = auth()->id();

    // First, create the property
    $property = Property::create($data);

    // Then handle image uploads
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/property_images', $filename);

            $property->images()->create([
                'image_path' => $filename
            ]);
        }
    }

    return redirect()->route('landlord.properties.index')->with('success', 'Property added!');
}

    public function edit(Property $property)
    {
        $this->authorize('update', $property);
        return view('landlord.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $this->authorize('update', $property);

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'location' => 'required',
            'price' => 'required|numeric',
            'room_count' => 'required|integer|min:1',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        $data = $request->all();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/property_images', $filename);

                $property->images()->create([
                    'image_path' => $filename
                ]);
            }
        }


        $property->update($data);

        return redirect()->route('landlord.properties.index')->with('success', 'Property updated!');
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
        $property->load('rooms', 'rooms.images', 'rooms.reviews.tenant', 'user', 'images');
    // this should be correct
        return view('tenant.properties.show', compact('property'));
    }

}
