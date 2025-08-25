<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Property;
use App\Models\RoomImage;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    

public function index(Property $property)
{
    $this->authorize('view', $property); // optional if using policies

    // This ensures $rooms is a collection, not an int
    $rooms = Room::where('property_id', $property->id)->get();
    return view('landlord.rooms.index', compact('property', 'rooms'));
}

public function create(Property $property)
{
    return view('landlord.rooms.create', compact('property'));
}

public function store(Request $request, $propertyId)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'price' => 'required|numeric',
        'capacity' => 'required|integer',
        'available_slots' => 'required|integer',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $room = Room::create([
        'property_id' => $propertyId,
        'name' => $validated['name'],
        'price' => $validated['price'],
        'capacity' => $validated['capacity'],
        'available_slots' => $validated['available_slots'],
    ]);

    if ($request->hasFile('images')) {
    $existingCount = $room->images()->count();
    $newImages = $request->file('images');
    $maxImages = 5;
    $allowedUploads = $maxImages - $existingCount;

    if (count($newImages) > $allowedUploads) {
        return back()->with('error', "You can only upload $allowedUploads more image(s).");
    }

    foreach ($newImages as $image) {
        $path = $image->store('room_images', 'public');
        $room->images()->create(['image_path' => $path]);
    }
}

    return redirect()->route('landlord.rooms.index', $propertyId)->with('success', 'Room created with images.');
}


public function edit(Room $room)
{
    return view('landlord.rooms.edit', compact('room'));
}

public function update(Request $request, Room $room)
{
    $request->validate([
        'name' => 'required|string',
        'price' => 'required|numeric',
        'capacity' => 'required|integer|min:1',
        'available_slots' => 'required|integer|min:0|max:' . $request->capacity,
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $room->update([
        'name' => $request->name,
        'price' => $request->price,
        'capacity' => $request->capacity,
        'available_slots' => $request->available_slots,
    ]);

    if ($request->hasFile('images')) {
    $existingCount = $room->images()->count();
    $newImages = $request->file('images');
    $maxImages = 5;
    $allowedUploads = $maxImages - $existingCount;

    if (count($newImages) > $allowedUploads) {
        return back()->with('error', "You can only upload $allowedUploads more image(s).");
    }

    foreach ($newImages as $image) {
        $path = $image->store('room_images', 'public');
        $room->images()->create(['image_path' => $path]);
    }
}

    return redirect()->route('landlord.rooms.index', $room->property_id)
                     ->with('success', 'Room updated with new images.');
}


public function destroy(Room $room)
{
    $room->delete();
    return back()->with('success', 'Room deleted.');
}

public function deleteImage(RoomImage $image)
{
    // Check that the landlord owns the room (optional, for security)
    $room = $image->room;
    if ($room->property->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    // Delete the file from storage
    Storage::disk('public')->delete($image->image_path);

    // Delete the record from the database
    $image->delete();

    return back()->with('success', 'Room image deleted successfully.');
}

}
