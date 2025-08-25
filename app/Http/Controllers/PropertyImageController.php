<?php

namespace App\Http\Controllers;

use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PropertyImageController extends Controller
{
    public function destroy($id)
    {
        $image = PropertyImage::findOrFail($id);

        // Delete file from storage
        Storage::disk('public')->delete('property_images/' . $image->image_path);

        // Delete record
        $image->delete();

        return back()->with('success', 'Image removed successfully.');
    }
}

