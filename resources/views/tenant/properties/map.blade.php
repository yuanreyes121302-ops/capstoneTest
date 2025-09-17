@extends('layouts.app')

@section('content')
<div class="tenant-container">
    <!-- Back Button Section -->
    <a href="{{ route('tenant.properties.show', $property->id) }}"
       class="back-btn mb-4"
       onclick="logBackNavigation({{ $property->id }})">
        <i class="fas fa-arrow-left me-2"></i> Back to Property Details
    </a>

    <!-- Map Section -->
    <div class="map-section">
        <h3 class="section-title">
            <i class="fas fa-map-marked-alt"></i> Interactive Map & Directions
        </h3>

        <div id="map" class="map-container">
            <div id="map-controls" class="map-controls">
                <button id="collapse-map" class="btn btn-sm btn-outline-light" title="Collapse Map">
                    <i class="fas fa-compress"></i>
                </button>
            </div>
        </div>

        <!-- Route Information Box -->
        <div id="route-info" class="route-info-box">
            <button id="expand-map" class="btn btn-sm btn-outline-secondary position-absolute expand-btn" title="Expand Map">
                <i class="fas fa-expand"></i>
            </button>
            <h5 class="route-title">
                <i class="fas fa-route me-2"></i>Route Information
            </h5>
            <div class="route-details">
                <div class="route-item">
                    <i class="fas fa-play-circle text-success me-2"></i>
                    <strong>Start:</strong> <span id="start-location" class="location-text">Not selected</span>
                </div>
                <div class="route-item">
                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                    <strong>Destination:</strong> <span id="destination-location" class="location-text">{{ $property->title }}</span>
                </div>
            </div>
            <button id="use-my-location" class="btn btn-tenant btn-tenant-primary mt-3 w-100">
                <i class="fas fa-crosshairs me-2"></i>Use My Location
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Tenant-specific modern design */
    .tenant-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
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

    /* Map section styling */
    .map-section {
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

    .map-container {
        border-radius: 10px;
        overflow: hidden;
        height: 500px;
        position: relative;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: height 0.3s ease;
    }

    .map-expanded {
        height: 80vh !important;
    }

    .expanded-hide {
        display: none !important;
    }

    .map-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        display: none;
    }

    .map-controls button {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .map-controls button:hover {
        background: rgba(255, 255, 255, 1);
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    /* Route info box styling */
    .route-info-box {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        padding: 20px;
        background: #fff;
        border: 1px solid #e1e8ed;
        border-radius: 12px;
        font-size: 14px;
        margin-top: 20px;
        position: relative;
        max-width: 350px;
    }

    .expand-btn {
        top: 10px;
        right: 10px;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .route-title {
        color: #2c3e50;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .route-details {
        margin-bottom: 1rem;
    }

    .route-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        padding: 0.5rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .location-text {
        color: #34495e;
        font-weight: 500;
    }

    /* Button styling */
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
        color: white;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .tenant-container {
            padding: 0 15px;
        }

        .map-section {
            padding: 1.5rem;
        }

        .map-container {
            height: 400px;
        }

        .route-info-box {
            max-width: 100%;
            margin-top: 15px;
        }

        .btn-tenant {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css"/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
    // Initialize map with property location
    const propertyLat = {{ $property->latitude }};
    const propertyLng = {{ $property->longitude }};
    const map = L.map('map').setView([propertyLat, propertyLng], 14);

    // Load tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let routingControl, startMarker;

    // Add property marker
    const propertyMarker = L.marker([propertyLat, propertyLng])
        .addTo(map)
        .bindPopup('{{ $property->title }}')
        .openPopup();

    // Geocoder search box
    L.Control.geocoder({
        defaultMarkGeocode: false
    })
    .on('markgeocode', function(e) {
        const startLatLng = e.geocode.center;
        updateRoute(startLatLng, e.geocode.name);
    })
    .addTo(map);

    // Update route function
    function updateRoute(startLatLng, label = "Start Location") {
        const destLatLng = L.latLng(propertyLat, propertyLng);

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
            createMarker: () => null,
            lineOptions: {
                styles: [{color: 'red', weight: 5}]
            }
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

    // Log back navigation
    function logBackNavigation(propertyId) {
        // Send AJAX request to log the navigation
        fetch('/api/log-back-navigation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                property_id: propertyId,
                tenant_id: {{ auth()->id() }}
            })
        }).catch(error => {
            console.log('Logging failed:', error);
        });
    }

    // Expansion toggle
    let isExpanded = false;

    document.getElementById('expand-map').addEventListener('click', () => {
        toggleExpansion();
    });

    document.getElementById('collapse-map').addEventListener('click', () => {
        toggleExpansion();
    });

    function toggleExpansion() {
        const mapElement = document.getElementById('map');
        const routeInfo = document.getElementById('route-info');
        const mapControls = document.getElementById('map-controls');

        if (isExpanded) {
            // Collapse
            mapElement.classList.remove('map-expanded');
            routeInfo.classList.remove('expanded-hide');
            mapControls.style.display = 'none';
        } else {
            // Expand
            mapElement.classList.add('map-expanded');
            routeInfo.classList.add('expanded-hide');
            mapControls.style.display = 'block';
        }

        isExpanded = !isExpanded;

        // Resize map
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    }
</script>

@endpush
