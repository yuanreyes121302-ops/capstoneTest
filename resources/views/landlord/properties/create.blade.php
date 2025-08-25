@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('landlord.properties.index') }}" class="btn btn-secondary mb-3">← Back</a>

    <h3 class="mb-4">Add Property</h3>

    <form action="{{ route('landlord.properties.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" class="form-control" value="{{ old('location') }}" required>
        </div>

        <div class="mb-3 row">
            <div class="col-md-6">
                <label>Price</label>
                <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
            </div>
            <div class="col-md-6">
                <label>Rooms</label>
                <input type="number" name="room_count" class="form-control" value="{{ old('room_count', 1) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Save Property</button>
    </form>
</div>
@endsection
