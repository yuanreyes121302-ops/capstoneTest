@extends('layouts.app')

@php
    $hasBooking = \App\Models\Booking::where('tenant_id', auth()->id())
    ->whereIn('status', ['pending', 'accepted'])
    ->where(function($q) {
        $q->whereNull('finalized_at')->orWhereNotNull('finalized_at');
    })
    ->exists();
@endphp

@section('content')
<div class="container">
    <a href="{{ route('tenant.properties.index') }}" class="btn btn-secondary mb-3">← Back to Listings</a>
    <h3 class="mb-3">{{ $property->title }}</h3>

    <div class="mb-3">
        <div class="mb-3 d-flex gap-2 flex-wrap m-3">
            @foreach ($property->images as $index => $img)
                <img src="{{ asset('storage/property_images/' . $img->image_path) }}"
                    style="width: 170px; height: 120px; object-fit: cover; border-radius: 8px; cursor: pointer;"
                    data-bs-toggle="modal" data-bs-target="#imageModal{{ $index }}">
                
                <!-- Modal -->
                <div class="modal fade" id="imageModal{{ $index }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $index }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-body p-0">
                                <img src="{{ asset('storage/property_images/' . $img->image_path) }}" class="img-fluid w-100">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
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
    <div id="map" style="height: 400px;" class="my-4 rounded shadow-sm"></div>

<script>
    const propertyLat = {{ $property->latitude }};
    const propertyLng = {{ $property->longitude }};
    const startLat = 15.00089; // Example location
    const startLng = 120.65254;

    const map = L.map('map').setView([startLat, startLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Optional: Markers
    L.marker([startLat, startLng]).addTo(map).bindPopup('Your Location');
    L.marker([propertyLat, propertyLng]).addTo(map).bindPopup('Property Location');

    // Leaflet Routing Machine Route (this draws the curve!)
    L.Routing.control({
        waypoints: [
            L.latLng(startLat, startLng),
            L.latLng(propertyLat, propertyLng)
        ],
        routeWhileDragging: false,
        show: false,
        createMarker: function () { return null; }, // hides auto markers
    }).addTo(map);

</script>
</div>
@endsection
