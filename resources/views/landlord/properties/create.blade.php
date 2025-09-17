@extends('layouts.app')

@section('content')

@push('styles')
<style>
    /* Landlord-specific modern design for create form */
    .landlord-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .landlord-header h2 {
        font-weight: 300;
        margin-bottom: 0;
        font-size: 2.5rem;
    }

    .landlord-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        border: none;
    }

    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .form-header h3 {
        font-weight: 300;
        margin-bottom: 0.5rem;
        font-size: 2rem;
    }

    .form-header p {
        opacity: 0.9;
        margin-bottom: 0;
    }

    .form-content {
        padding: 2.5rem;
    }

    /* Step indicator */
    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .step {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        font-weight: 600;
        margin: 0 0.5rem;
        transition: all 0.3s ease;
    }

    .step.active {
        background: #667eea;
        color: white;
    }

    .step.completed {
        background: #28a745;
        color: white;
    }

    .step-line {
        width: 60px;
        height: 2px;
        background: #e9ecef;
        margin: 0 0.5rem;
    }

    .step-line.active {
        background: #667eea;
    }

    /* Step content */
    .step-content {
        display: none;
    }

    .step-content.active {
        display: block;
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Room management */
    .room-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid #e9ecef;
    }

    .room-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .room-title {
        font-weight: 600;
        color: #2c3e50;
    }

    .room-images {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .room-image {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .room-image:hover {
        transform: scale(1.05);
    }

    .form-group {
        margin-bottom: 2rem;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
    }

    .form-control {
        border: 2px solid #e1e8ed;
        border-radius: 10px;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background-color: white;
        outline: none;
    }

    .form-control[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    .map-container {
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid #e1e8ed;
        margin-bottom: 2rem;
    }

    #map {
        height: 350px;
        width: 100%;
    }

    .gps-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.8rem 2rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .gps-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    }

    .file-input-wrapper {
        position: relative;
        border: 2px dashed #667eea;
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        background-color: #f8f9ff;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-input-wrapper:hover {
        background-color: #f0f2ff;
        border-color: #5a6fd8;
    }

    .file-input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .file-input-label {
        color: #667eea;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .file-input-hint {
        color: #7f8c8d;
        font-size: 0.9rem;
    }

    .btn-landlord {
        border-radius: 25px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        border: none;
        min-width: 140px;
    }

    .btn-landlord-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-landlord-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    }

    .btn-landlord-secondary {
        background: #ecf0f1;
        color: #2c3e50;
        border: 2px solid #bdc3c7;
    }

    .btn-landlord-secondary:hover {
        background: #d5dbdb;
        transform: translateY(-2px);
    }

    .back-btn {
        position: absolute;
        top: 2rem;
        left: 2rem;
        z-index: 10;
    }

    .row-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .row-inputs {
            grid-template-columns: 1fr;
        }

        .form-content {
            padding: 1.5rem;
        }

        .form-header {
            padding: 1.5rem;
        }

        .landlord-header {
            padding: 1.5rem 0;
        }

        .landlord-header h2 {
            font-size: 2rem;
        }
    }

    /* Error styling */
    .error-message {
        color: #e74c3c;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-control.is-invalid {
        border-color: #e74c3c;
    }
</style>
@endpush

<div class="landlord-header">
    <div class="landlord-container">
        <h2>Add New Property</h2>
    </div>
</div>

<div class="landlord-container">
    <div class="form-card">
        <div class="form-header">
            <h3>Property Details</h3>
            <p>Fill in the information below to add your property</p>
        </div>

        <div class="form-content">
            <a href="{{ route('landlord.properties.index') }}" class="btn btn-light back-btn" style="border-radius: 8px; color: #2c3e50; background: white; border: 1px solid #e1e8ed; transition: all 0.3s ease; font-weight: 500;">
                ← Back
            </a>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step1">1</div>
                <div class="step-line" id="line1"></div>
                <div class="step" id="step2">2</div>
            </div>

            <form action="{{ route('landlord.properties.store') }}" method="POST" enctype="multipart/form-data" id="propertyForm">
                @csrf

                <!-- Step 1: Property Details -->
                <div class="step-content active" id="step1Content">
                    <h4 class="mb-4">Step 1: Property Details</h4>

                    <div class="form-group">
                        <label class="form-label">Property Title</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" placeholder="Enter property title" required>
                        @error('title')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="4" placeholder="Describe your property" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Location (Address)</label>
                        <input type="text" id="location" name="location"
                               class="form-control @error('location') is-invalid @enderror"
                               value="{{ old('location') }}" placeholder="Enter full address" required>
                        @error('location')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row-inputs">
                        <div class="form-group">
                            <label class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="latitude" name="latitude"
                                   placeholder="14.5995" readonly required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="longitude" name="longitude"
                                   placeholder="120.9842" readonly required>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" id="useLocation" class="gps-btn">
                            <i class="fas fa-crosshairs"></i> Use Current Location
                        </button>
                    </div>

                    <div class="map-container">
                        <div id="map"></div>
                    </div>

                    <div class="row-inputs">
                        <div class="form-group">
                            <label class="form-label">Monthly Price (₱)</label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price') }}" placeholder="50000" min="0" required>
                            @error('price')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Number of Rooms</label>
                            <input type="number" name="room_count" class="form-control @error('room_count') is-invalid @enderror"
                                   value="{{ old('room_count', 1) }}" min="1" required>
                            @error('room_count')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Property Images <span class="text-danger">*</span></label>
                        <div class="file-input-wrapper" id="fileInputWrapper">
                            <input type="file" name="images[]" class="file-input" id="imageInput" multiple accept="image/*" required>
                            <div class="file-input-label" id="uploadLabel">
                                <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                <br>Click to Upload Images
                            </div>
                            <div class="file-input-hint">Select 1-10 images (JPG, PNG, GIF, WebP - High quality recommended)</div>
                        </div>

                        <!-- Image Preview Area -->
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <h6 class="text-muted mb-3">Selected Images:</h6>
                            <div id="previewContainer" class="d-flex flex-wrap gap-2"></div>
                        </div>

                        @error('images')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-center">
                        <button type="button" class="btn btn-landlord-primary" id="nextBtn">
                            <i class="fas fa-arrow-right"></i> Next: Add Rooms
                        </button>
                    </div>
                </div>

                <!-- Step 2: Room Management -->
                <div class="step-content" id="step2Content">
                    <h4 class="mb-4">Step 2: Room Management</h4>
                    <p class="text-muted mb-4">Add details and images for each room in your property.</p>

                    <div id="roomsContainer">
                        <!-- Rooms will be added here dynamically -->
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-landlord-secondary me-2" id="prevBtn">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-landlord-primary" id="addRoomBtn">
                            <i class="fas fa-plus"></i> Add Room
                        </button>
                        <button type="submit" class="btn btn-landlord-primary ms-2" id="submitBtn">
                            <i class="fas fa-save"></i> Done
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    const originalBtnText = submitBtn.innerHTML;
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');
    const fileInputWrapper = document.getElementById('fileInputWrapper');
    const uploadLabel = document.getElementById('uploadLabel');

    // Enhanced image preview and validation functionality
    imageInput.addEventListener('change', function(e) {
        const files = e.target.files;
        previewContainer.innerHTML = '';
        let validFiles = 0;
        let invalidFiles = 0;
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

        if (files.length > 0) {
            // Check file count
            if (files.length > 10) {
                fileInputWrapper.style.borderColor = '#e74c3c';
                uploadLabel.innerHTML = `<i class="fas fa-exclamation-triangle fa-2x text-danger"></i><br>Maximum 10 images allowed`;
                imagePreview.style.display = 'none';
                return;
            }

            imagePreview.style.display = 'block';

            Array.from(files).forEach((file, index) => {
                // Validate file type
                if (!allowedTypes.includes(file.type)) {
                    invalidFiles++;
                    return;
                }

                validFiles++;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'position-relative d-inline-block';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;" title="${file.name}">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeImage(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            });

            // Update UI based on validation results
            if (invalidFiles > 0) {
                fileInputWrapper.style.borderColor = '#e74c3c';
                uploadLabel.innerHTML = `<i class="fas fa-exclamation-triangle fa-2x text-danger"></i><br>${validFiles} valid, ${invalidFiles} invalid files`;
            } else {
                fileInputWrapper.style.borderColor = '#667eea';
                uploadLabel.innerHTML = `<i class="fas fa-check-circle fa-2x text-success"></i><br>${validFiles} image(s) selected`;
            }
        } else {
            imagePreview.style.display = 'none';
            fileInputWrapper.style.borderColor = '#667eea';
            uploadLabel.innerHTML = `<i class="fas fa-cloud-upload-alt fa-2x"></i><br>Click to Upload Images`;
        }
    });

    // Remove image from preview
    window.removeImage = function(index) {
        const dt = new DataTransfer();
        const files = Array.from(imageInput.files);

        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));

        imageInput.files = dt.files;
        imageInput.dispatchEvent(new Event('change'));
    };

    // Form submission with validation
    form.addEventListener('submit', function(e) {
        const requiredFields = ['title', 'description', 'location', 'latitude', 'longitude', 'price', 'room_count'];
        let isValid = true;

        // Check required text fields
        requiredFields.forEach(field => {
            const element = document.querySelector(`[name="${field}"]`);
            if (!element.value.trim()) {
                element.classList.add('is-invalid');
                isValid = false;
            } else {
                element.classList.remove('is-invalid');
            }
        });

        // Check if images are selected
        if (imageInput.files.length === 0) {
            fileInputWrapper.style.borderColor = '#e74c3c';
            uploadLabel.innerHTML = `<i class="fas fa-exclamation-triangle fa-2x text-danger"></i><br>Please select at least one image`;
            isValid = false;
        } else {
            fileInputWrapper.style.borderColor = '#667eea';
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Property...';
        submitBtn.style.opacity = '0.7';
    });

    // Re-enable button if form validation fails
    form.addEventListener('invalid', function(e) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        submitBtn.style.opacity = '1';
    }, true);

    // Real-time validation feedback
    const inputs = document.querySelectorAll('input[required], textarea[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            } else {
                this.classList.add('is-invalid');
            }
        });
    });
});
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
        lngInput.value = Number(lng).value = Number(lng).toFixed(12);
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

    // Auto-geocode address to lat/lng when location input loses focus
    const locationInput = document.getElementById("location");
    locationInput.addEventListener("blur", function() {
        const address = this.value.trim();
        if (address) {
            // Use Nominatim for geocoding (free, no API key required)
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);
                        updateMarker(lat, lng);
                    } else {
                        console.warn('No geocoding results found for address:', address);
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                });
        }
    });

    // In case the map is inside a flex/tab and renders before it’s visible
    setTimeout(() => map.invalidateSize(), 200);
});

// Step navigation
document.addEventListener("DOMContentLoaded", function () {
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const step1 = document.getElementById('step1Content');
    const step2 = document.getElementById('step2Content');
    const step1Indicator = document.getElementById('step1');
    const step2Indicator = document.getElementById('step2');
    const line1 = document.getElementById('line1');

    nextBtn.addEventListener('click', function() {
        // Validate Step 1
        const requiredFields = ['title', 'description', 'location', 'latitude', 'longitude', 'price', 'room_count'];
        let isValid = true;

        requiredFields.forEach(field => {
            const element = document.querySelector(`[name="${field}"]`);
            if (!element.value.trim()) {
                element.classList.add('is-invalid');
                isValid = false;
            } else {
                element.classList.remove('is-invalid');
            }
        });

        if (document.getElementById('imageInput').files.length === 0) {
            document.getElementById('fileInputWrapper').style.borderColor = '#e74c3c';
            isValid = false;
        }

        if (isValid) {
            step1.classList.remove('active');
            step2.classList.add('active');
            step1Indicator.classList.remove('active');
            step1Indicator.classList.add('completed');
            step2Indicator.classList.add('active');
            line1.classList.add('active');
        }
    });

    prevBtn.addEventListener('click', function() {
        step2.classList.remove('active');
        step1.classList.add('active');
        step2Indicator.classList.remove('active');
        step1Indicator.classList.remove('completed');
        step1Indicator.classList.add('active');
        line1.classList.remove('active');
    });
});

// Room management
document.addEventListener("DOMContentLoaded", function () {
    const addRoomBtn = document.getElementById('addRoomBtn');
    const roomsContainer = document.getElementById('roomsContainer');
    let roomIndex = 0;

    addRoomBtn.addEventListener('click', function() {
        const roomCard = document.createElement('div');
        roomCard.className = 'room-card';
        roomCard.innerHTML = `
            <div class="room-header">
                <div class="room-title">Room ${roomIndex + 1}</div>
                <button type="button" class="btn btn-sm btn-danger remove-room-btn">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            <div class="form-group">
                <label class="form-label">Room Name</label>
                <input type="text" name="rooms[name][${roomIndex}]" class="form-control" placeholder="e.g., Bedroom 1" required>
            </div>
            <div class="row-inputs">
                <div class="form-group">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="rooms[capacity][${roomIndex}]" class="form-control" placeholder="2" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Available Slots</label>
                    <input type="number" name="rooms[available_slots][${roomIndex}]" class="form-control" placeholder="1" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Room Images</label>
                <div class="file-input-wrapper room-file-input-wrapper" id="roomFileInputWrapper${roomIndex}">
                    <input type="file" name="rooms[images][${roomIndex}][]" class="file-input room-image-input" id="roomImageInput${roomIndex}" multiple accept="image/*">
                    <div class="file-input-label room-upload-label" id="roomUploadLabel${roomIndex}">
                        <i class="fas fa-cloud-upload-alt fa-2x"></i>
                        <br>Click to Upload Images
                    </div>
                    <div class="file-input-hint">Select 1-5 images (JPG, PNG, GIF, WebP)</div>
                </div>
                <!-- Room Image Preview Area -->
                <div id="roomImagePreview${roomIndex}" class="mt-3" style="display: none;">
                    <h6 class="text-muted mb-3">Selected Images:</h6>
                    <div id="roomPreviewContainer${roomIndex}" class="d-flex flex-wrap gap-2"></div>
                </div>
            </div>
        `;
        roomsContainer.appendChild(roomCard);

        // Add image upload functionality for this room
        setupRoomImageUpload(roomIndex);

        roomIndex++;

        // Add remove functionality
        roomCard.querySelector('.remove-room-btn').addEventListener('click', function() {
            roomCard.remove();
            roomIndex--;
            // Renumber rooms
            const roomCards = roomsContainer.querySelectorAll('.room-card');
            roomCards.forEach((card, idx) => {
                card.querySelector('.room-title').textContent = `Room ${idx + 1}`;
            });
        });
    });

    function setupRoomImageUpload(index) {
        const roomImageInput = document.getElementById(`roomImageInput${index}`);
        const roomImagePreview = document.getElementById(`roomImagePreview${index}`);
        const roomPreviewContainer = document.getElementById(`roomPreviewContainer${index}`);
        const roomFileInputWrapper = document.getElementById(`roomFileInputWrapper${index}`);
        const roomUploadLabel = document.getElementById(`roomUploadLabel${index}`);

        roomImageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            roomPreviewContainer.innerHTML = '';
            let validFiles = 0;
            let invalidFiles = 0;
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

            if (files.length > 0) {
                // Check file count
                if (files.length > 5) {
                    roomFileInputWrapper.style.borderColor = '#e74c3c';
                    roomUploadLabel.innerHTML = `<i class="fas fa-exclamation-triangle fa-2x text-danger"></i><br>Maximum 5 images allowed`;
                    roomImagePreview.style.display = 'none';
                    return;
                }

                roomImagePreview.style.display = 'block';

                Array.from(files).forEach((file, fileIndex) => {
                    // Validate file type
                    if (!allowedTypes.includes(file.type)) {
                        invalidFiles++;
                        return;
                    }

                    validFiles++;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'position-relative d-inline-block';
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;" title="${file.name}">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeRoomImage(${index}, ${fileIndex})">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        roomPreviewContainer.appendChild(previewItem);
                    };
                    reader.readAsDataURL(file);
                });

                // Update UI based on validation results
                if (invalidFiles > 0) {
                    roomFileInputWrapper.style.borderColor = '#e74c3c';
                    roomUploadLabel.innerHTML = `<i class="fas fa-exclamation-triangle fa-2x text-danger"></i><br>${validFiles} valid, ${invalidFiles} invalid files`;
                } else {
                    roomFileInputWrapper.style.borderColor = '#667eea';
                    roomUploadLabel.innerHTML = `<i class="fas fa-check-circle fa-2x text-success"></i><br>${validFiles} image(s) selected`;
                }
            } else {
                roomImagePreview.style.display = 'none';
                roomFileInputWrapper.style.borderColor = '#667eea';
                roomUploadLabel.innerHTML = `<i class="fas fa-cloud-upload-alt fa-2x"></i><br>Click to Upload Images`;
            }
        });
    }

    // Global function to remove room images
    window.removeRoomImage = function(roomIndex, fileIndex) {
        const roomImageInput = document.getElementById(`roomImageInput${roomIndex}`);
        const dt = new DataTransfer();
        const files = Array.from(roomImageInput.files);

        files.splice(fileIndex, 1);
        files.forEach(file => dt.items.add(file));

        roomImageInput.files = dt.files;
        roomImageInput.dispatchEvent(new Event('change'));
    };
});
</script>

@endsection
