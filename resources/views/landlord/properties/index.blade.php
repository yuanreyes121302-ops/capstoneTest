@extends('layouts.app')

@section('content')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/index_property.css') }}">
    @endpush
@push('styles')

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>My Properties</h2>
        <a href="{{ route('landlord.profile') }}" class="btn btn-outline-secondary">
                👤 View Profile
        </a>
    </div>

    <a href="{{ route('landlord.properties.create') }}" class="btn btn-primary mb-3">+ Add Property</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($properties as $property)
            <div class="col-md-12 mb-10">
                <div class="card h-100 shadow-sm">
                    @if($property->images->count())
                        <div class="d-flex overflow-auto gap-2 m-3">
                            @foreach($property->images as $img)
                                <img src="{{ asset('storage/property_images/' . $img->image_path) }}" 
                                    style="width: 120px; height: 100px; object-fit: cover; border-radius: 5px;">
                            @endforeach
                        </div>
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $property->title }}</h5>
                        <p class="card-text mb-1">{{ $property->location }}</p>
                        <p class="card-text mb-1"><strong>₱{{ number_format($property->price, 0) }}</strong></p>
                        <p class="card-text">{{ Str::limit($property->description, 60) }}</p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('landlord.properties.edit', $property->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="{{ route('landlord.rooms.index', $property->id) }}" class="btn btn-outline-info btn-sm mt-2">Manage Rooms</a>

                        <form action="{{ route('landlord.properties.destroy', $property->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">No properties yet. Click "Add Property" to create one.</p>
        @endforelse
    </div>
</div>
@endsection
