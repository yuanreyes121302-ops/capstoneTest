@extends('layouts.app')

@section('content')

@push('styles')
<style>
    /* Tenant-specific modern profile design */
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

    .tenant-header .btn:hover {
        background: rgba(255,255,255,0.3) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .tenant-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .profile-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        border: none;
        margin-bottom: 2rem;
    }

    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        margin: 0 auto 1rem;
        object-fit: cover;
        position: relative;
    }

    .profile-avatar-container {
        position: relative;
        display: inline-block;
    }

    .edit-profile-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .edit-profile-btn:hover {
        background: #5a6fd8;
        transform: scale(1.1);
    }

    .profile-header h3 {
        font-weight: 300;
        margin-bottom: 0.5rem;
        font-size: 1.8rem;
    }

    .profile-header p {
        opacity: 0.9;
        margin-bottom: 0;
    }

    .profile-content {
        padding: 2.5rem;
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

    .form-control:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .form-select {
        border: 2px solid #e1e8ed;
        border-radius: 10px;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }

    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background-color: white;
        outline: none;
    }

    .file-input-wrapper {
        position: relative;
        border: 2px dashed #667eea;
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        background-color: #f8f9ff;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-top: 1rem;
    }

    .file-input-wrapper:hover {
        background-color: #f0f2ff;
        border-color: #5a6fd8;
    }

    .file-input-wrapper.selected {
        border-color: #27ae60;
        background-color: #f0f9f0;
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

    .file-input-label.selected {
        color: #27ae60;
    }

    .file-input-hint {
        color: #7f8c8d;
        font-size: 0.9rem;
    }

    .image-preview {
        margin-top: 1rem;
        display: none;
    }

    .image-preview img {
        max-width: 100px;
        max-height: 100px;
        border-radius: 8px;
        border: 2px solid #667eea;
        object-fit: cover;
    }

    .upload-status {
        margin-top: 0.5rem;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .upload-status.success {
        color: #27ae60;
    }

    .upload-status.error {
        color: #e74c3c;
    }

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

    .row-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .alert-tenant {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 2rem;
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

    @media (max-width: 768px) {
        .row-inputs {
            grid-template-columns: 1fr;
        }

        .profile-content {
            padding: 1.5rem;
        }

        .profile-header {
            padding: 1.5rem;
        }

        .tenant-header {
            padding: 1.5rem 0;
        }

        .tenant-header h2 {
            font-size: 2rem;
        }
    }
</style>
@endpush

<div class="tenant-header">
    <div class="tenant-container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <a href="{{ route('tenant.profile') }}" class="btn btn-light me-3" style="border-radius: 8px; color: #2c3e50; background: white; border: 1px solid #e1e8ed; transition: all 0.3s ease; font-weight: 500;">
                    ← Back
                </a>
                <h2>My Profile</h2>
            </div>
        </div>
    </div>
</div>

<div class="tenant-container">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar-container">
                @if ($tenant->profile_image)
                    <img src="{{ asset('storage/profile_images/' . $tenant->profile_image) }}?v={{ time() }}"
                        alt="Profile Image" class="profile-avatar">
                @else
                    <img src="{{ asset('images/default-avatar.png') }}"
                        alt="Default Profile" class="profile-avatar">
                @endif
                <button type="button" class="edit-profile-btn" data-bs-toggle="modal" data-bs-target="#editProfileImageModal">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <h3>{{ $tenant->first_name }} {{ $tenant->last_name }}</h3>
        </div>

        <div class="profile-content">
            @if (session('success'))
                <div class="alert-tenant">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.profile.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="row-inputs">
                    <div class="form-group">
                        <label class="form-label">User ID</label>
                        <input type="text" class="form-control" value="{{ $tenant->user_id }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $tenant->email) }}" placeholder="Enter email">
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row-inputs">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name', $tenant->first_name) }}" placeholder="Enter first name">
                        @error('first_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name', $tenant->last_name) }}" placeholder="Enter last name">
                        @error('last_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row-inputs">
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="male" {{ $tenant->gender == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $tenant->gender == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ $tenant->gender == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                               value="{{ old('location', $tenant->location ?? '') }}" id="profileLocation" placeholder="Enter your location">
                        @error('location')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Enter your location to auto-calculate latitude and longitude</small>
                    </div>
                </div>

                <div class="row-inputs">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror"
                               value="{{ old('latitude', $tenant->latitude) }}" id="profileLatitude" readonly placeholder="Latitude will be calculated">
                        @error('latitude')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror"
                               value="{{ old('longitude', $tenant->longitude) }}" id="profileLongitude" readonly placeholder="Longitude will be calculated">
                        @error('longitude')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-tenant-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Profile Image Modal -->
<div class="modal fade" id="editProfileImageModal" tabindex="-1" aria-labelledby="editProfileImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="editProfileImageModalLabel">
                    <i class="fas fa-camera me-2"></i>Change Profile Picture
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('tenant.profile.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Select New Profile Image</label>
                        <div class="file-input-wrapper" id="fileInputWrapper">
                            <input type="file" name="profile_image" class="file-input" accept="image/*" id="profileImageInput" required>
                            <div class="file-input-label" id="uploadLabel">
                                <i class="fas fa-camera fa-2x"></i>
                                <br>Click to Select Image
                            </div>
                            <div class="file-input-hint">JPG, PNG, GIF - High quality recommended</div>
                            <div id="selectedFileName" class="file-input-hint" style="display: none; color: #27ae60; font-weight: 600;"></div>
                        </div>

                        <!-- Image Preview -->
                        <div class="image-preview" id="imagePreview">
                            <img id="previewImg" src="" alt="Image Preview">
                        </div>

                        <!-- Upload Status -->
                        <div class="upload-status" id="uploadStatus"></div>

                        @error('profile_image')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tenant-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-tenant-primary" id="imageModalSubmitBtn">
                        <i class="fas fa-upload"></i> Update Picture
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
document.addEventListener("DOMContentLoaded", function () {
    // Handle main profile form
    const profileForm = document.querySelector('.profile-content form');
    const profileSubmitBtn = document.getElementById('submitBtn');

    if (profileForm && profileSubmitBtn) {
        const originalProfileBtnText = profileSubmitBtn.innerHTML;

        profileForm.addEventListener('submit', function(e) {
            // Show loading state
            profileSubmitBtn.disabled = true;
            profileSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Profile...';
            profileSubmitBtn.style.opacity = '0.7';
        });

        // Re-enable button if form validation fails
        profileForm.addEventListener('invalid', function(e) {
            profileSubmitBtn.disabled = false;
            profileSubmitBtn.innerHTML = originalProfileBtnText;
            profileSubmitBtn.style.opacity = '1';
        }, true);
    }

    // Handle profile image modal
    const imageForm = document.querySelector('#editProfileImageModal form');
    const imageSubmitBtn = document.getElementById('imageModalSubmitBtn');
    const profileImageInput = document.getElementById('profileImageInput');
    const selectedFileName = document.getElementById('selectedFileName');

    if (imageForm && imageSubmitBtn) {
        const originalImageBtnText = imageSubmitBtn.innerHTML;

        // Show selected file name when file is chosen
        profileImageInput.addEventListener('change', function(e) {
            if (this.files && this.files.length > 0) {
                const fileName = this.files[0].name;
                selectedFileName.textContent = 'Selected: ' + fileName;
                selectedFileName.style.display = 'block';
            } else {
                selectedFileName.style.display = 'none';
            }
        });

        imageForm.addEventListener('submit', function(e) {
            // Check if file is selected
            if (!profileImageInput.files || profileImageInput.files.length === 0) {
                e.preventDefault();
                alert('Please select an image file before updating.');
                return false;
            }

            // Show loading state
            imageSubmitBtn.disabled = true;
            imageSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            imageSubmitBtn.style.opacity = '0.7';
        });

        // Re-enable button if form validation fails
        imageForm.addEventListener('invalid', function(e) {
            imageSubmitBtn.disabled = false;
            imageSubmitBtn.innerHTML = originalImageBtnText;
            imageSubmitBtn.style.opacity = '1';
        }, true);
    }

    // Geocode location to lat/lng when user types
    const locationInput = document.getElementById('profileLocation');
    const latInput = document.getElementById('profileLatitude');
    const lngInput = document.getElementById('profileLongitude');

    locationInput.addEventListener('input', function() {
        const location = this.value.trim();
        if (location.length > 2) { // Start geocoding after 3 characters
            geocodeLocation(location);
        }
    });

    function geocodeLocation(address) {
        // Use Nominatim (OpenStreetMap) for geocoding - free and no API key required
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    latInput.value = lat.toFixed(6);
                    lngInput.value = lng.toFixed(6);
                } else {
                    console.log('Geocoding failed: No results found');
                    latInput.value = '';
                    lngInput.value = '';
                }
            })
            .catch(error => {
                console.log('Geocoding error:', error);
                latInput.value = '';
                lngInput.value = '';
            });
    }

});
</script>
@endsection
