@extends('layouts.app')

@section('content')

@push('styles')
<style>
    /* Container styling */
    .containerkoto{
        max-width: 850px;
        margin: 40px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    /* Headings */
    h3 {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 25px;
        color: #333;
    }

    /* Form labels */
    label {
        font-weight: 600;
        margin-bottom: 6px;
        color: #444;
        display: inline-block;
    }

    /* Input fields */
    .form-control {
        padding: 10px 14px;
        font-size: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        background-color: #fdfdfd;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: none;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* GPS button */
    #useLocation.btn {
        margin-bottom: 20px;
    }

    /* Map container */
    #map {
        height: 400px;
        margin-bottom: 25px;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #ccc;
    }

    /* Two-column layout for price and rooms */
    .mb-3.row > .col-md-6 {
        padding-right: 10px;
    }

    .mb-3.row > .col-md-6:last-child {
        padding-right: 0;
    }

    /* File input */
    input[type="file"] {
        padding: 6px;
        background-color: #fefefe;
    }

    /* Buttons */
    .btn-primary {
        background-color: #007bff;
        border: none;
        padding: 10px 18px;
        font-size: 14px;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-secondary {
        background-color: #f1f1f1;
        color: #333;
        border: 1px solid #ccc;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
    }

    .btn-secondary:hover {
        background-color: #e2e2e2;
    }

    /* Margin helpers override */
    .mb-3 {
        margin-bottom: 20px !important;
    }

    .mt-2 {
        margin-top: 10px !important;
    }

    .mb-4 {
        margin-bottom: 28px !important;
    }
</style>
@endpush


<div class="container">
    <div class="containerkoto">
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
            <label>Location (Address)</label>
            <input type="text" id="location" name="location" class="form-control" value="{{ old('location') }}" required>
        </div>

        <div class="form-group">
            <label for="latitude">Latitude</label>
            <input type="text" class="form-control" id="latitude" name="latitude" placeholder="14.5995" readonly required>
        </div>

        <div class="form-group">
            <label for="longitude">Longitude</label>
            <input type="text" class="form-control" id="longitude" name="longitude" placeholder="120.9842" readonly required>
        </div>
        
        <button type="button" id="useLocation" class="btn btn-primary mt-2">Use GPS</button>

        <div id="map" style="height: 400px;"></div>

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
    </div>
    

<script>
document.addEventListener("DOMContentLoaded", function () {
    const latInput = document.getElementById("latitude");
    const lngInput = document.getElementById("longitude");
    const useGpsBtn = document.getElementById("useLocation");

    // Use existing values if present; otherwise default to Manila
    const initLat = parseFloat(latInput.value) || 14.5995;
    const initLng = parseFloat(lngInput.value) || 120.9842;

    const map = L.map("map").setView([initLat, initLng], 13);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "© OpenStreetMap contributors",
    }).addTo(map);

    // Create marker immediately so it can be dragged right away
    let marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);

    // Helpers
    function setFields(lat, lng) {
        latInput.value = Number(lat).toFixed(12);
        lngInput.value = Number(lng).toFixed(12);
    }

    function updateMarker(lat, lng, zoom = 16) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], zoom);
        setFields(lat, lng);
    }

    // Initialize fields from initial marker
    setFields(initLat, initLng);

    // Dragging the pin updates the fields
    marker.on("dragend", () => {
        const pos = marker.getLatLng();
        setFields(pos.lat, pos.lng);
    });

    // Clicking the map moves the pin and updates the fields
    map.on("click", (e) => {
        updateMarker(e.latlng.lat, e.latlng.lng);
    });

    // Manual typing moves the pin
    latInput.addEventListener("change", () => {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) updateMarker(lat, lng);
    });
    lngInput.addEventListener("change", () => {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) updateMarker(lat, lng);
    });

    // GPS button
    useGpsBtn.addEventListener("click", () => {
        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser.");
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                updateMarker(pos.coords.latitude, pos.coords.longitude);
            },
            (err) => {
                alert("GPS failed: " + err.message + "\nTip: GPS requires HTTPS or localhost.");
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });

    // In case the map is inside a flex/tab and renders before it’s visible
    setTimeout(() => map.invalidateSize(), 200);
});
</script>

@endsection
