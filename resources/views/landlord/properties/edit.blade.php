@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('landlord.properties.index') }}" class="btn btn-light mb-3" style="border-radius: 8px; color: #2c3e50; background: white; border: 1px solid #e1e8ed; transition: all 0.3s ease; font-weight: 500;">← Back</a>

    <h3 class="mb-4">Edit Property</h3>

    <form action="{{ route('landlord.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $property->title) }}" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" required>{{ old('description', $property->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" class="form-control" value="{{ old('location', $property->location) }}" required>
        </div>

         <!-- Latitude & Longitude -->
        <div class="mb-3 row">
            <div class="col-md-6">
                <label>Latitude</label>
                <input type="text" id="latitude" name="latitude" class="form-control" 
                       value="{{ old('latitude', $property->latitude) }}" required readonly>
            </div>
            <div class="col-md-6">
                <label>Longitude</label>
                <input type="text" id="longitude" name="longitude" class="form-control" 
                       value="{{ old('longitude', $property->longitude) }}" required readonly>
            </div>
        </div>

        <!-- GPS + Map -->
        <div class="mb-3">
            <button type="button" class="btn btn-outline-primary mb-2" onclick="getLocation()">📍 Use GPS</button>
            <div id="map" style="height: 300px; border-radius: 5px;"></div>
        </div>

        <div class="mb-3 row">
            <div class="col-md-6">
                <label>Price</label>
                <input type="number" name="price" class="form-control" value="{{ old('price', $property->price) }}" required>
            </div>
            <div class="col-md-6">
                <label>Rooms</label>
                <input type="number" name="room_count" class="form-control" value="{{ old('rooms', $property->room_count) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Additional Property Images (Optional)</label>
            <div class="file-input-wrapper" id="fileInputWrapper" style="border: 2px dashed #007bff; border-radius: 10px; padding: 2rem; text-align: center; background-color: #f8f9ff; transition: all 0.3s ease; cursor: pointer;">
                <input type="file" name="images[]" class="file-input" id="imageInput" multiple accept="image/*" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer;">
                <div class="file-input-label" id="uploadLabel" style="color: #007bff; font-weight: 600; margin-bottom: 0.5rem;">
                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                    <br>Click to Add More Images
                </div>
                <div class="file-input-hint" style="color: #7f8c8d; font-size: 0.9rem;">Select up to 10 images (JPG, PNG, GIF, WebP - High quality recommended)</div>
            </div>

            <!-- Image Preview Area -->
            <div id="imagePreview" class="mt-3" style="display: none;">
                <h6 class="text-muted mb-3">New Images to Add:</h6>
                <div id="previewContainer" class="d-flex flex-wrap gap-2"></div>
            </div>

            @error('images.*')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Property</button>
    </form>

    <h5 class="m-3">Property Images</h5>       
    <div class="d-flex flex-wrap gap-3 mt-1">
        
        @foreach($property->images as $img)
            <div class="position-relative" style="display: inline-block;">
                <img src="{{ asset('storage/property_images/' . $img->image_path) }}" 
                    style="width: 120px; height: 80px; object-fit: cover; border-radius: 5px;">

                <form action="{{ route('landlord.property-images.destroy', $img->id) }}" method="POST"
                    style="position: absolute; top: 0; right: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" 
                            style="border-radius: 50%; padding: 0 6px; font-size: 12px;"
                            onclick="return confirm('Delete this image?')">
                        ×
                    </button>
                </form>
            </div>
        @endforeach
    </div>

<!-- Leaflet + GPS Script -->
<script>
    var map = L.map('map').setView([{{ $property->latitude }}, {{ $property->longitude }}], 15);
    var marker = L.marker([{{ $property->latitude }}, {{ $property->longitude }}], {draggable:true}).addTo(map);

    // Update inputs when marker is dragged
    marker.on('dragend', function(e) {
        var latlng = marker.getLatLng();
        document.getElementById('latitude').value = latlng.lat;
        document.getElementById('longitude').value = latlng.lng;
    });

    // OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    // GPS function
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;

                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            }, function() {
                alert("Failed to retrieve GPS location. Please drag the pin instead.");
            });
        } else {
            alert("Your browser does not support GPS.");
        }
    }
</script>

<!-- Image Upload Validation Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
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
                fileInputWrapper.style.borderColor = '#dc3545';
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
                fileInputWrapper.style.borderColor = '#dc3545';
                uploadLabel.innerHTML = `<i class="fas fa-exclamation-triangle fa-2x text-danger"></i><br>${validFiles} valid, ${invalidFiles} invalid files`;
            } else {
                fileInputWrapper.style.borderColor = '#007bff';
                uploadLabel.innerHTML = `<i class="fas fa-check-circle fa-2x text-success"></i><br>${validFiles} image(s) selected`;
            }
        } else {
            imagePreview.style.display = 'none';
            fileInputWrapper.style.borderColor = '#007bff';
            uploadLabel.innerHTML = `<i class="fas fa-cloud-upload-alt fa-2x"></i><br>Click to Add More Images`;
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
});
</script>

</div>
@endsection
