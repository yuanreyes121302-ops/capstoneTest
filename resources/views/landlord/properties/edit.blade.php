@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('landlord.properties.index') }}" class="btn btn-secondary mb-3">← Back</a>

    <h3 class="mb-4">Edit Property</h3>

    <form action="{{ route('landlord.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $property->title) }}" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" required>{{ old('description', $property->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" class="form-control" value="{{ old('location', $property->location) }}" required>
        </div>

        <div class="mb-3 row">
            <div class="col-md-6">
                <label>Price</label>
                <input type="number" name="price" class="form-control" value="{{ old('price', $property->price) }}" required>
            </div>
            <div class="col-md-6">
                <label>Rooms</label>
                <input type="number" name="room_count" class="form-control" value="{{ old('rooms', $property->room_count) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Update Property</button>
    </form>

    <h5 class="m-3">Property Images</h5>       
    <div class="d-flex flex-wrap gap-3 mt-1">
        
        @foreach($property->images as $img)
            <div class="position-relative" style="display: inline-block;">
                <img src="{{ asset('storage/property_images/' . $img->image_path) }}" 
                    style="width: 120px; height: 80px; object-fit: cover; border-radius: 5px;">

                <form action="{{ route('property-images.destroy', $img->id) }}" method="POST" 
                    style="position: absolute; top: 0; right: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" 
                            style="border-radius: 50%; padding: 0 6px; font-size: 12px;"
                            onclick="return confirm('Delete this image?')">
                        ×
                    </button>
                </form>
            </div>
        @endforeach
    </div>


</div>
@endsection
