@extends('layouts.app')

@push('styles')
<style>
    /* Tenant-specific modern property details design */
    .tenant-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .tenant-header h2 {
        font-weight: 300;
        margin-bottom: 0;
        font-size: 2.5rem;
    }

    .tenant-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .info-card,
    .contact-card,
    .room-table,
    .map-box,
    .review-box {
        background-color: #fff;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border: none;
    }

    /* Image gallery section */
    .image-gallery {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        border: none;
    }

    .property-images {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .property-images img {
        width: 100%;
        height: 120px;
        border-radius: 10px;
        object-fit: cover;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .property-images img:hover {
        transform: scale(1.05);
    }

    /* Carousel styles for slidable gallery */
    .carousel-control-prev,
    .carousel-control-next {
        width: 5%;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        width: 30px;
        height: 30px;
    }

    /* Room images section */
    .room-images {
        margin-top: 2rem;
    }

    .room-images h5 {
        color: #2c3e50;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .room-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 0.5rem;
    }

    .room-gallery img {
        width: 100%;
        height: 100px;
        border-radius: 8px;
        object-fit: cover;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .room-gallery img:hover {
        transform: scale(1.05);
    }

    /* Property details section */
    .property-details {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        border: none;
    }

    .property-title {
        font-size: 2rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .property-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .meta-item i {
        color: #667eea;
        margin-right: 0.5rem;
        font-size: 1.2rem;
    }

    .meta-item strong {
        color: #2c3e50;
    }

    .property-description {
        line-height: 1.6;
        color: #34495e;
        margin-bottom: 1.5rem;
    }

    .property-landlord {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9ff;
        border-radius: 10px;
        border-left: 4px solid #667eea;
    }

    .property-landlord i {
        color: #667eea;
        margin-right: 0.5rem;
    }

    /* Contact and booking section */
    .contact-booking {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        border: none;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
    }

    .section-title i {
        color: #667eea;
        margin-right: 0.5rem;
    }

    /* Rooms section */
    .rooms-section {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        border: none;
    }

    .room-card {
        border: 1px solid #e3e3e3;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background: #fff;
        transition: box-shadow 0.3s ease;
    }

    .room-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .room-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .room-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
    }

    /* Removed room-price styling */

    /* Map section */
    .map-section {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        border: none;
    }

    #map {
        border-radius: 10px;
        overflow: hidden;
        height: 400px;
    }

    #route-info {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        padding: 15px;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 14px;
        margin-top: 15px;
        max-width: 300px;
    }

    /* Buttons */
    .btn-tenant {
        border-radius: 25px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        border: none;
        min-width: 140px;
    }

    .btn-tenant-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-tenant-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    }

    .btn-tenant-secondary {
        background: #ecf0f1;
        color: #2c3e50;
        border: 2px solid #bdc3c7;
    }

    .btn-tenant-secondary:hover {
        background: #d5dbdb;
        transform: translateY(-2px);
    }

    .btn-tenant-success {
        background: #27ae60;
        color: white;
    }

    .btn-tenant-success:hover {
        background: #229954;
        transform: translateY(-2px);
    }

    .back-btn {
        border-radius: 8px;
        color: #2c3e50;
        background: white;
        border: 1px solid #e1e8ed;
        transition: all 0.3s ease;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
    }

    .back-btn:hover {
        background: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        text-decoration: none;
        color: #2c3e50;
    }

    /* Review styling */
    .review-box {
        background-color: #f9f9f9;
        padding: 1.5rem;
        border-radius: 10px;
        border-left: 4px solid #667eea;
        margin-bottom: 1rem;
    }

    .review-reply {
        background-color: #f0f9f0;
        border-left: 4px solid #27ae60;
        padding: 1rem;
        border-radius: 8px;
        margin-top: 1rem;
    }

    /* Form styling */
    .form-control {
        border: 2px solid #e1e8ed;
        border-radius: 10px;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-select {
        border: 2px solid #e1e8ed;
        border-radius: 10px;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .tenant-header {
            padding: 1.5rem 0;
        }

        .tenant-header h2 {
            font-size: 2rem;
        }

        .property-images {
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        }

        .property-meta {
            grid-template-columns: 1fr;
        }

        .info-card,
        .contact-card,
        .room-table,
        .map-box,
        .review-box,
        .image-gallery,
        .property-details,
        .contact-booking,
        .rooms-section,
        .map-section {
            padding: 1.5rem;
        }

        .btn-tenant {
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
<div class="tenant-container">
    <!-- Back Button Section -->
    <a href="{{ route('tenant.properties.index') }}" class="back-btn mb-4">
        <i class="fas fa-arrow-left me-2"></i> Back to Listings
    </a>

    <!-- Image Gallery Section -->
    <div class="image-gallery">
        <div class="property-images">
            @php
                $allImages = collect();
                // Add property images
                foreach ($property->images as $img) {
                    $allImages->push([
                        'path' => 'storage/property_images/' . $img->image_path,
                        'alt' => 'Property Image',
                        'type' => 'property'
                    ]);
                }
                // Add room images
                foreach ($property->rooms as $room) {
                    foreach ($room->images as $img) {
                        $allImages->push([
                            'path' => 'storage/room_images/' . $img->image_path,
                            'alt' => 'Room ' . $room->name . ' Image',
                            'type' => 'room'
                        ]);
                    }
                }
            @endphp
            @foreach ($allImages as $index => $img)
                <img src="{{ asset($img['path']) }}"
                     alt="{{ $img['alt'] }} {{ $index + 1 }}"
                     data-bs-toggle="modal"
                     data-bs-target="#imageGalleryModal"
                     data-slide-to="{{ $index }}">
            @endforeach
        </div>
    </div>

    <!-- Property Details Section -->
    <div class="property-details">
        <h1 class="property-title">{{ $property->title }}</h1>

        <div class="property-meta">
            <div class="meta-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <strong>Location</strong><br>
                    {{ $property->location }}
                </div>
            </div>
            <div class="meta-item">
                <i class="fas fa-dollar-sign"></i>
                <div>
                    <strong>Price</strong><br>
                    ₱{{ number_format($property->price, 0) }}
                </div>
            </div>
            <div class="meta-item">
                <i class="fas fa-star"></i>
                <div>
                    <strong>Rating</strong><br>
                    @if ($property->averageRating)
                        {{ $property->averageRating }} ★
                    @else
                        No reviews yet
                    @endif
                </div>
            </div>
        </div>

        <div class="property-description">
            <h5>Description</h5>
            <p>{{ $property->description }}</p>
        </div>

        <div class="property-landlord">
            <i class="fas fa-user"></i>
            <div>
                <strong>Posted by:</strong> {{ $property->user->first_name }} {{ $property->user->last_name }}
            </div>
        </div>
    </div>

    <!-- Contact and Booking Section -->
    <div class="contact-booking">
        <h3 class="section-title">
            <i class="fas fa-envelope"></i> Contact & Book
        </h3>

        <div class="d-flex gap-3 mb-4 flex-wrap">
            <a href="{{ route('messages.show', $property->user_id) }}" class="btn btn-tenant btn-tenant-secondary">
                <i class="fas fa-comments me-2"></i> Contact Landlord
            </a>
            <a href="{{ route('tenant.properties.map', $property->id) }}" class="btn btn-tenant btn-tenant-secondary">
                <i class="fas fa-map-marked-alt me-2"></i> View on Map
            </a>
        </div>

        @if($hasBooking)
            <button type="button" class="btn btn-tenant btn-tenant-primary disabled" disabled>
                <i class="fas fa-calendar-check me-2"></i> You Already Have a Booking Request
            </button>
            <p class="text-danger mt-2">You already have an ongoing or finalized booking.</p>
        @elseif($property->rooms->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>No rooms available for booking.
            </div>
        @else
            <button type="button" class="btn btn-tenant btn-tenant-primary" data-bs-toggle="modal" data-bs-target="#bookingModal">
                <i class="fas fa-calendar-check me-2"></i> Request Booking
            </button>
        @endif
    </div>

    <!-- Rooms Section -->
    @if ($property->rooms->isNotEmpty())
        <div class="rooms-section">
            <h3 class="section-title">
                <i class="fas fa-bed"></i> Available Rooms
            </h3>

            @foreach ($property->rooms as $room)
                <div class="room-card">
                    <div class="room-header">
                        <div class="room-title">{{ $room->name }}</div>
                    </div>
                    <p><strong>Capacity:</strong> {{ $room->capacity }} person(s)</p>
                    <p><strong>Available Slots:</strong>
                        @if ($room->available_slots > 0)
                            {{ $room->available_slots }}
                        @else
                            <span class="text-danger">Fully booked</span>
                        @endif
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Reviews Section -->
    <div class="rooms-section">
        <h3 class="section-title">
            <i class="fas fa-star"></i> Reviews & Ratings
        </h3>

        @if(auth()->check() && auth()->user()->role === 'tenant')
            <div class="mb-4">
                <h5>Write a Review</h5>
                <form action="{{ route('reviews.store', $property) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <select name="rating" id="rating" class="form-select" required>
                            <option value="">Select rating</option>
                            <option value="5">5 ★ - Excellent</option>
                            <option value="4">4 ★ - Very Good</option>
                            <option value="3">3 ★ - Good</option>
                            <option value="2">2 ★ - Fair</option>
                            <option value="1">1 ★ - Poor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment (Optional)</label>
                        <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-tenant-primary">Submit Review</button>
                </form>
            </div>
        @endif

        @if($property->reviews->isNotEmpty())
            @foreach($property->reviews as $review)
                <div class="review-box">
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
                        <div class="review-reply">
                            <strong>Landlord reply:</strong>
                            <p class="mb-1">{{ $review->reply }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-muted">No reviews yet. Be the first to review this property!</p>
        @endif
    </div>

    <!-- Map Section -->
    <div class="map-section">
        <h3 class="section-title">
            <i class="fas fa-map-marked-alt"></i> Location & Directions
        </h3>

        <div id="map"></div>

        <!-- Info box for start & destination -->
        <div id="route-info">
            <strong>Route Information</strong><br>
            Start: <span id="start-location">Not selected</span><br>
            Destination: <span id="destination-location">Property Location</span>
            <button id="use-my-location" class="btn btn-tenant btn-tenant-secondary mt-2">Use My Location</button>
        </div>
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
            createMarker: () => null, // hide auto markers
            lineOptions: {
                styles: [{color: 'red', weight: 5}]
            }
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const propertyId = {{ $property->id }};
    const dateSelect = document.getElementById('booking_date');
    const timeSelect = document.getElementById('booking_time');
    const roomSelect = document.getElementById('room_id');

    let availabilityData = null;

    // Load available dates and times on page load
    loadAvailability();

    function loadAvailability() {
        fetch(`/properties/${propertyId}/availability`, { credentials: 'same-origin' })
            .then(response => response.json())
            .then(data => {
                availabilityData = data;
                dateSelect.innerHTML = '<option value="">Select a date</option>';
                if (data.available_dates.length === 0) {
                    dateSelect.innerHTML = '<option value="">No available dates at this time</option>';
                    document.getElementById('date-error').textContent = 'Please select a date and time before continuing.';
                    document.getElementById('date-error').style.display = 'block';
                } else {
                    data.available_dates.forEach(date => {
                        const option = document.createElement('option');
                        option.value = date;
                        option.textContent = new Date(date).toLocaleDateString();
                        dateSelect.appendChild(option);
                    });
                    document.getElementById('date-error').style.display = 'none';
                }
                dateSelect.disabled = false;
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="">First select a date</option>';
                roomSelect.disabled = true;
                roomSelect.innerHTML = '<option value="">First select a time</option>';
            })
            .catch(error => {
                console.error('Error loading availability:', error);
                document.getElementById('date-error').textContent = 'Please select a date and time before continuing.';
                document.getElementById('date-error').style.display = 'block';
            });
    }

    // When date is selected, load available times from stored data
    dateSelect.addEventListener('change', function() {
        const selectedDate = this.value;
        if (selectedDate && availabilityData) {
            // Clear any residual date error when a valid date is selected
            document.getElementById('date-error').style.display = 'none';
            const times = availabilityData.available_times[selectedDate] || [];
            timeSelect.innerHTML = '<option value="">Select a time</option>';
            if (times.length === 0) {
                timeSelect.innerHTML = '<option value="">No available times for this date</option>';
                document.getElementById('time-error').textContent = 'No available times for this date.';
                document.getElementById('time-error').style.display = 'block';
            } else {
                times.forEach(time => {
                    const option = document.createElement('option');
                    option.value = time;
                    // Convert to 12-hour format
                    const [hours, minutes] = time.split(':');
                    const hour12 = hours % 12 || 12;
                    const ampm = hours < 12 ? 'AM' : 'PM';
                    option.textContent = `${hour12}:${minutes} ${ampm}`;
                    timeSelect.appendChild(option);
                });
                document.getElementById('time-error').style.display = 'none';
            }
            timeSelect.disabled = false;
            roomSelect.disabled = true;
            roomSelect.innerHTML = '<option value="">First select a time</option>';
        } else {
            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="">First select a date</option>';
            roomSelect.disabled = true;
            roomSelect.innerHTML = '<option value="">First select a time</option>';
        }
    });

    // When time is selected, load available rooms
    timeSelect.addEventListener('change', function() {
        const selectedDate = dateSelect.value;
        const selectedTime = this.value;
        if (selectedDate && selectedTime) {
            // Need to fetch rooms separately since not in availability data
            fetch(`/properties/${propertyId}/availability/${selectedDate}/${selectedTime}`, { credentials: 'same-origin' })
                .then(response => response.json())
                .then(data => {
                    roomSelect.innerHTML = '<option value="">Select a room</option>';
                    if (data.available_rooms.length === 0) {
                        roomSelect.innerHTML = '<option value="">No available rooms for this time</option>';
                        document.getElementById('room-error').textContent = 'No available rooms for this time.';
                        document.getElementById('room-error').style.display = 'block';
                    } else {
                        data.available_rooms.forEach(room => {
                            const option = document.createElement('option');
                            option.value = room.id;
                            option.textContent = `${room.name} - ₱${room.price} (${room.capacity} person(s))`;
                            roomSelect.appendChild(option);
                        });
                        document.getElementById('room-error').style.display = 'none';
                    }
                    roomSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading rooms:', error);
                    document.getElementById('room-error').textContent = 'Unable to load available rooms. Please try again later.';
                    document.getElementById('room-error').style.display = 'block';
                });
        } else {
            roomSelect.disabled = true;
            roomSelect.innerHTML = '<option value="">First select a time</option>';
        }
    });

    // Form validation
    const bookingForm = document.getElementById('bookingForm');
    const requireRoom = {{ $property->rooms->count() > 1 ? 'true' : 'false' }};
    bookingForm.addEventListener('submit', function(e) {
        const requiredFields = ['tenant_name', 'contact_number', 'email', 'booking_date', 'booking_time'];
        if (requireRoom) {
            requiredFields.push('room_id');
        }
        let isValid = true;
        let missingDateOrTime = false;

        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (!element || !element.value.trim()) {
                if (element) element.classList.add('is-invalid');
                isValid = false;
                if (field === 'booking_date' || field === 'booking_time') {
                    missingDateOrTime = true;
                }
            } else {
                if (element) element.classList.remove('is-invalid');
            }
        });

        // Email validation
        const email = document.getElementById('email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email.value && !emailRegex.test(email.value)) {
            email.classList.add('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            if (missingDateOrTime) {
                document.getElementById('form-error').textContent = 'Please select a date and time before continuing.';
            } else {
                document.getElementById('form-error').textContent = 'Please fill in all required fields correctly.';
            }
            document.getElementById('form-error').style.display = 'block';
        } else {
            document.getElementById('form-error').style.display = 'none';
        }
    });

    // Remove invalid class on input
    document.querySelectorAll('#bookingForm input, #bookingForm select, #bookingForm textarea').forEach(element => {
        element.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
});
</script>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Request Booking for {{ $property->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bookings.store', $property->id) }}" method="POST" id="bookingForm">
                @csrf
                <div class="modal-body">
                    <!-- Tenant Information -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Your Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tenant_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="tenant_name" name="tenant_name"
                                       value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact_number" class="form-label">Contact Number *</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number"
                                       value="{{ auth()->user()->contact_number }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="{{ auth()->user()->email }}" required>
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Select Date</h6>
                        <select class="form-select" id="booking_date" name="booking_date" required>
                            <option value="">Select a date</option>
                        </select>
                        <div id="date-error" class="text-danger mt-1" style="display: none;"></div>
                    </div>

                    <!-- Time Selection -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Select Time</h6>
                        <select class="form-select" id="booking_time" name="booking_time" required disabled>
                            <option value="">First select a date</option>
                        </select>
                        <div id="time-error" class="text-danger mt-1" style="display: none;"></div>
                    </div>

                    <!-- Room Selection -->
                    @if ($property->rooms && $property->rooms->count() > 1)
                        <div class="mb-4">
                            <h6 class="fw-bold">Select Room *</h6>
                            <select name="room_id" id="room_id" class="form-select" required disabled>
                                <option value="">First select a time</option>
                            </select>
                            <div id="room-error" class="text-danger mt-1" style="display: none;"></div>
                        </div>
                    @elseif ($property->rooms && $property->rooms->count() == 1)
                        <div class="mb-4">
                            <h6 class="fw-bold">Room</h6>
                            <input type="hidden" name="room_id" value="{{ $property->rooms->first()->id }}">
                            <p class="form-control-plaintext">{{ $property->rooms->first()->name }}</p>
                        </div>
                    @else
                        <div class="mb-4">
                            <p class="text-warning">No rooms available, please contact landlord. You can still submit booking with date and time.</p>
                            <input type="hidden" name="room_id" value="">
                        </div>
                    @endif

                    <!-- Terms -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Additional Notes</h6>
                        <textarea name="terms" class="form-control" rows="3" placeholder="Optional: Add terms or notes to landlord"></textarea>
                    </div>
                </div>
                <div id="form-error" class="text-danger mt-2" style="display: none;"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-tenant-primary">Confirm Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
@if(session('success'))
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Booking Successful
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                <p class="mb-3">{{ session('success') }}</p>
                <p class="text-muted">The landlord will review your request and get back to you soon.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('bookings.tenant.index') }}" class="btn btn-tenant-primary">View My Bookings</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Show success modal if session success exists
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        });
    @endif
</script>
@endif

<!-- Image Gallery Modal -->
<div class="modal fade" id="imageGalleryModal" tabindex="-1" aria-labelledby="imageGalleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageGalleryModalLabel">Property Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach ($allImages as $index => $img)
                            <button type="button" data-bs-target="#imageCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach ($allImages as $index => $img)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ asset($img['path']) }}" class="d-block w-100 rounded" alt="{{ $img['alt'] }} {{ $index + 1 }}">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>{{ $img['alt'] }} {{ $index + 1 }}</h5>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
