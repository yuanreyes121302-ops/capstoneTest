@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Room</h4>

<form action="{{ isset($room) ? route('landlord.rooms.update', $room->id) : route('landlord.rooms.store', $property->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($room)) @method('PUT') @endif

    <div class="mb-2">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $room->name ?? '') }}" required>
    </div>

    <div class="mb-2">
        <label>Price</label>
        <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price', $room->price ?? '') }}" required>
    </div>

    <div class="mb-2">
        <label>Capacity</label>
        <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $room->capacity ?? '') }}" required>
    </div>

    <div class="mb-2">
        <label>Available Slots</label>
        <input type="number" name="available_slots" class="form-control" value="{{ old('available_slots', $room->available_slots ?? '') }}" required>
    </div>
    
    @php
    $maxImages = 5;
    $currentCount = isset($room) ? $room->images->count() : 0;
    $remaining = $maxImages - $currentCount;
@endphp

@if ($remaining > 0)
    <div class="mb-3">
        <label for="images" class="form-label">Upload Room Images ({{ $remaining }} remaining)</label>
        <input type="file" name="images[]" class="form-control" multiple {{ $remaining === 0 ? 'disabled' : '' }} accept="image/*">
    </div>
@else
    <div class="alert alert-info">
        Maximum of {{ $maxImages }} images reached. Delete existing ones to upload more.
    </div>
@endif
    <button type="submit" class="btn btn-success">Save</button>
</form>

@if ($room->images->isNotEmpty())
    <h6 class="mt-3">Room Images</h6>
    <div class="d-flex flex-wrap gap-3">
        @foreach ($room->images as $image)
            <div class="position-relative">
                <img src="{{ asset('storage/' . $image->image_path) }}" style="width: 120px; height: 90px; object-fit: cover; border-radius: 5px;">
                <form action="{{ route('landlord.rooms.images.delete', $image->id) }}" method="POST" class="position-absolute top-0 end-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger m-1" onclick="return confirm('Delete this image?')">&times;</button>
                </form>
            </div>
        @endforeach
    </div>
@endif
</div>
@endsection