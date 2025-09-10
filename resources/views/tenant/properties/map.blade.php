@extends('layouts.app')

@section('content')
<div class="container">
    <div id="map" style="height: 400px;" class="my-4 rounded shadow-sm"></div>

    <!-- Info box -->
    <div id="route-info" 
        style="padding:10px; background:#fff; border:1px solid #ccc; border-radius:8px; width:250px; font-size:14px; margin-top:10px;">
        <strong>Route Information</strong><br>
        Start: <span id="start-location">Not selected</span><br>
        Destination: <span id="destination-location">Property Location</span>
        <button id="use-my-location" class="btn btn-primary mt-2">Use My Location</button>
    </div>
    <div class="mb-3">
    <label for="property-select">Choose Destination:</label>
    <select id="property-select" class="form-select">
        <option value="" disabled selected>Select a property</option>
        @foreach($properties as $prop)
            <option value="{{ $prop->latitude }},{{ $prop->longitude }}">
                {{ $prop->title }}
            </option>
        @endforeach
    </select>
</div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css"/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
    const map = L.map('map').setView([12.8797, 121.7740], 6); // Default PH center

    // Load tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let routingControl, startMarker, propertyMarker;

    // Geocoder search box
    L.Control.geocoder({
        defaultMarkGeocode: false
    })
    .on('markgeocode', function(e) {
        const startLatLng = e.geocode.center;
        updateRoute(startLatLng, e.geocode.name);
    })
    .addTo(map);

    // Dropdown change -> set destination
    document.getElementById("property-select").addEventListener("change", function() {
        const coords = this.value.split(",");
        const lat = parseFloat(coords[0]);
        const lng = parseFloat(coords[1]);

        if (isNaN(lat) || isNaN(lng)) return;

        // Remove old property marker
        if (propertyMarker) {
            map.removeLayer(propertyMarker);
        }

        // Add new property marker
        propertyMarker = L.marker([lat, lng])
            .addTo(map)
            .bindPopup("Selected Property")
            .openPopup();

        document.getElementById("destination-location").innerText = this.options[this.selectedIndex].text;

        // If start point already exists, update the route
        if (startMarker) {
            updateRoute(startMarker.getLatLng(), "Start Location", L.latLng(lat, lng));
        } else {
            map.setView([lat, lng], 14);
        }
    });

    // Update route function
    function updateRoute(startLatLng, label = "Start Location", destinationLatLng = null) {
        if (!destinationLatLng && !propertyMarker) return;
        const destLatLng = destinationLatLng || propertyMarker.getLatLng();

        // Remove old route if exists
        if (routingControl) map.removeControl(routingControl);

        // Remove old start marker if exists
        if (startMarker) map.removeLayer(startMarker);

        // Add new start marker
        startMarker = L.marker(startLatLng)
            .addTo(map)
            .bindPopup(label)
            .openPopup();

        document.getElementById("start-location").innerText = label;

        // Add new route
        routingControl = L.Routing.control({
            waypoints: [startLatLng, destLatLng],
            routeWhileDragging: false,
            show: false,
            createMarker: () => null
        }).addTo(map);

        map.fitBounds(L.latLngBounds([startLatLng, destLatLng]));
    }

    // GPS Button
    document.getElementById("use-my-location").addEventListener("click", () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    const latlng = L.latLng(pos.coords.latitude, pos.coords.longitude);
                    updateRoute(latlng, "My Location");
                },
                () => alert("Unable to retrieve your location")
            );
        } else {
            alert("Geolocation not supported by your browser");
        }
    });
</script>

@endpush
