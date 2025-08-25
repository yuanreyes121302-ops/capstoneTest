@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Available Properties</h3>

    <div class="row">
        @foreach ($properties as $property)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    @if($property->images->first())
                        <img src="{{ asset('storage/property_images/' . $property->images->first()->image_path) }}" 
                             class="card-img-top" style="height: 180px; object-fit: cover;">
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $property->title }}</h5>
                        <p class="card-text">{{ Str::limit($property->description, 100) }}</p>
                        <p class="text-muted mb-2">By {{ $property->user->first_name }} {{ $property->user->last_name }}</p>
                        <a href="{{ route('tenant.properties.show', $property->id) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
