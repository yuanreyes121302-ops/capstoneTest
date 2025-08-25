@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Create Room</h4>
    <form action="{{ route('landlord.rooms.store', $property->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-2">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-2">
            <label>Price</label>
            <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price') }}" required>
        </div>

        <div class="mb-2">
            <label>Capacity</label>
            <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}" required>
        </div>

        <div class="mb-2">
            <label>Available Slots</label>
            <input type="number" name="available_slots" class="form-control" value="{{ old('available_slots') }}" required>
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
</div>
@endsection
