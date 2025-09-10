@extends('layouts.app')

@push('styles')
<style>
    /* Max-width container for wide layout */
.custom-max-container {
    max-width: 1140px;
    margin: auto;
    padding: 0 15px;
}

/* White card-style boxes with subtle shadow */
.info-card,
.contact-card,
.room-table,
.map-box,
.review-box {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

/* Image hover effect */
.property-images img {
    border-radius: 8px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.property-images img:hover {
    transform: scale(1.03);
}

/* Review and reply formatting */
.review-box {
    background-color: #f9f9f9;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #0d6efd;
}

.review-reply {
    background-color: #f1fdf2;
    border-left: 4px solid #198754;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    margin-top: 0.5rem;
}

/* Room cards */
.room-card {
    border: 1px solid #e3e3e3;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 20px;
    background: #fff;
}

/* Map container */
#map {
    border-radius: 8px;
    overflow: hidden;
}

.map-box {
    padding: 0;
    overflow: hidden;
}

/* Route info box on map */
#route-info {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    padding: 10px;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    margin-top: 10px;
    max-width: 300px;
}

/* Buttons */
.btn {
    border-radius: 6px;
}

.btn-outline-primary,
.btn-outline-secondary {
    min-width: 100px;
}

/* Responsive image layout */
@media (max-width: 768px) {
    .property-images {
        flex-direction: column;
    }

    .property-images img {
        width: 100% !important;
        height: auto !important;
    }

    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

</style>
@endpush



@php
    $hasBooking = \App\Models\Booking::where('tenant_id', auth()->id())
    ->whereIn('status', ['pending', 'accepted'])
    ->where(function($q) {
        $q->whereNull('finalized_at')->orWhereNotNull('finalized_at');
    })
    ->exists();
@endphp

@section('content')
<div class="container-fluid bg-light py-4">
    <div class="container custom-max-container">

        <a href="{{ route('tenant.properties.index') }}" class="btn btn-secondary mb-3">← Back to Listings</a>
        <h3 class="mb-3">{{ $property->title }}</h3>

        <div class="mb-3">
       <div class="mb-3 d-flex gap-2 flex-wrap m-3 property-images">
         @foreach ($property->images as $index => $img)
        <img src="{{ asset('storage/property_images/' . $img->image_path) }}" 
             class="shadow-sm"
             style="width: 170px; height: 120px; object-fit: cover; border-radius: 8px; cursor: pointer;"
             data-bs-toggle="modal" 
             data-bs-target="#imageModal{{ $index }}">
        <!-- Modal code unchanged -->
    @endforeach

</div>


  

    <p>
        <strong>Overall Rating:</strong>
        @if ($property->averageRating)
            {{ $property->averageRating }} ★
        @else
            <span class="text-muted">No reviews yet</span>
        @endif
    </p>

    <p><strong>Description:</strong></p>
    <p>{{ $property->description }}</p>

    <p><strong>Location:</strong> {{ $property->location }}</p>
    <p><strong>Price:</strong> ₱{{ number_format($property->price, 2) }}</p>

    <hr>

    <p class="text-muted">Posted by: {{ $property->user->first_name }} {{ $property->user->last_name }}</p>

    @if ($property->rooms->isNotEmpty())
        <h5>Available Rooms</h5>
        @foreach ($property->rooms as $room)
            <div class="border p-3 mb-2 rounded">
                <h6>Room {{ $loop->iteration }}</h6>
                <p><strong>Price:</strong> ₱{{ number_format($room->price, 2) }}</p>
                <p><strong>Capacity:</strong> {{ $room->capacity }} person(s)</p>

                <div class="mt-3">
                    <h6>Reviews</h6>
                    @if ($room->reviews->isEmpty())
                        <p class="text-muted">No reviews yet for this room.</p>
                    @else
                        @foreach ($room->reviews as $review)
                            <div class="bg-light p-2 mb-2 rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ $review->tenant->first_name }} {{ $review->tenant->last_name }}</strong>
                                    <span class="text-warning">
                                        {!! str_repeat('★', $review->rating) !!}
                                        {!! str_repeat('☆', 5 - $review->rating) !!}
                                    </span>
                                </div>
                                <p class="mb-1">{{ $review->comment }}</p>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>

                                @if ($review->reply)
                                    <div class="mt-2 p-2 bg-white border-start border-success border-3">
                                        <strong>Landlord reply:</strong>
                                        <p class="mb-1">{{ $review->reply }}</p>
                                    </div>
                                @endif

                                @if (auth()->id() === $review->tenant_id)
                                    <div class="mt-2 d-flex gap-2">
                                        <a href="{{ route('reviews.edit', $review->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>

                                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <p class="text-muted mt-3">No rooms listed for this property.</p>
    @endif

    <hr>
        <a href="{{ route('messages.show', $property->user_id) }}" class="btn btn-outline-primary mt-2">
            Contact Landlord
        </a>
    <hr>
      
    <form action="{{ route('bookings.store', $property->id) }}" method="POST">
        @csrf
        <h5>Request Booking</h5>
        <div class="border p-3 mb-2 rounded">
            <div class="mb-2">
                <textarea name="terms" class="form-control" rows="3" placeholder="Optional: Add terms or notes to landlord"></textarea>
            </div>

            @if ($property->rooms && $property->rooms->count())
                <div class="mb-3">
                    <label for="room_id" class="form-label"><strong>Select Room</strong></label>
                    <select name="room_id" class="form-select" required>
                        <option value="" disabled selected>Select a room</option>
                        @foreach ($property->rooms as $room)
                            <option value="{{ $room->id }}">
                                Room {{ $loop->iteration }} - ₱{{ number_format($room->price, 2) }} ({{ $room->capacity }} person(s))
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit" class="btn btn-primary {{ $hasBooking ? 'disabled' : '' }}">Request Booking</button>
            @if ($hasBooking)
                <p class="text-danger mt-2">You already have an ongoing or finalized booking.</p>
            @endif
        </div>
    </form>
<hr>

<a href="{{ route('tenant.properties.map', $property->id) }}" class="btn btn-outline-primary">
    📍 View on Map
</a>

    <div id="map" style="height: 400px;" class="my-4 rounded shadow-sm"></div>

    <!-- Info box for start & destination -->
<div id="route-info" style="padding:10px; background:#fff; border:1px solid #ccc; border-radius:8px; width:250px; font-size:14px; margin-top:10px;">
    <strong>Route Information</strong><br>
    Start: <span id="start-location">Not selected</span><br>
    Destination: <span id="destination-location">Property Location</span>
    <button id="use-my-location" class="btn btn-primary">Use My Location</button>
</div> 

<script>
    const propertyLat = {{ $property->latitude }};
    const propertyLng = {{ $property->longitude }};

    const map = L.map('map').setView([propertyLat, propertyLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Destination marker (Property)
    const propertyMarker = L.marker([propertyLat, propertyLng])
        .addTo(map)
        .bindPopup('Property Location')
        .openPopup();

    // Update destination in info box
    document.getElementById("destination-location").innerText = "Property Location";

    let routingControl;
    let startMarker;

    // Geocoder search box
    const geocoder = L.Control.geocoder({
        defaultMarkGeocode: false
    })
    .on('markgeocode', function(e) {
        const startLatLng = e.geocode.center;
        updateRoute(startLatLng, e.geocode.name);
    })
    .addTo(map);

    // Function to update route + markers
    function updateRoute(startLatLng, label = "Start Location") {
        // Remove old route if exists
        if (routingControl) map.removeControl(routingControl);
        // Remove old start marker if exists
        if (startMarker) map.removeLayer(startMarker);

        // Add new start marker
        startMarker = L.marker(startLatLng)
            .addTo(map)
            .bindPopup(label)
            .openPopup();

        // Update info box
        document.getElementById("start-location").innerText = label;

        // Add routing
        routingControl = L.Routing.control({
            waypoints: [
                startLatLng,
                L.latLng(propertyLat, propertyLng)
            ],
            routeWhileDragging: false,
            show: false,
            createMarker: () => null // hide auto markers
        }).addTo(map);

        // Fit map to both points
        map.fitBounds(L.latLngBounds([startLatLng, [propertyLat, propertyLng]]));
    }

    // 📍 "Use My Location" button
    document.getElementById("use-my-location").addEventListener("click", () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const latlng = L.latLng(position.coords.latitude, position.coords.longitude);
                    updateRoute(latlng, "My Location");
                },
                () => alert("Unable to retrieve your location")
            );
        } else {
            alert("Geolocation not supported by your browser");
        }
    });
</script>
</div>
</div>
    </div>
    
@endsection
