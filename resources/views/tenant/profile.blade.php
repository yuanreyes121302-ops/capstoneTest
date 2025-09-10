@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tenant.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
@endpush
    @stack('styles')

@section('content')
<div class="container">
    <div class="navbarcontainer">
        <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>My Profile</h2>
        </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="mb-3 d-flex justify-content-center">
        <div class="d-flex align-items-center">
            @if ($tenant->profile_image)
                <img src="{{ asset('storage/profile_images/' . $tenant->profile_image) }}?v={{ time() }}"
                    alt="Profile Image" class="rounded-circle me-3" style="width: 120px; height: 120px; object-fit: cover;">
            @else
                <img src="{{ asset('default-avatar.png') }}"
                    alt="Default Profile" class="rounded-circle me-3" style="width: 120px; height: 120px; object-fit: cover;">
            @endif
            <div>
                <label class="form-label mb-1">Choose File</label>
                <input type="file" name="profile_image" class="form-control" accept="image/*">
                @error('profile_image') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
    </div>

    <div class="card shadow-sm p-3">
        <form method="POST" action="{{ route('tenant.profile.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">User ID</label>
                    <input type="text" class="form-control" value="{{ $tenant->user_id }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" value="{{ old('email', $tenant->email) }}">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input name="first_name" class="form-control" value="{{ old('first_name', $tenant->first_name) }}">
                    @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input name="last_name" class="form-control" value="{{ old('last_name', $tenant->last_name) }}">
                    @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control">
                        <option value="male" {{ $tenant->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $tenant->gender == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ $tenant->gender == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location', $tenant->location ?? 'Detecting location...') }}" id="profileLocation" readonly>
                    @error('location') <small class="text-danger">{{ $message }}</small> @enderror
                    <small class="text-muted">Your location is automatically detected</small>
                </div>
            </div>


            <div class="text-end">
                <button class="btn btn-primary">Update Profile</button>
            </div>
        </form>
    </div>
    </div>
    
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-detect user location for profile
    const locationInput = document.getElementById('profileLocation');

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    // Use reverse geocoding to get location name
                    fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=en`)
                        .then(response => response.json())
                        .then(data => {
                            const location = `${data.city}, ${data.countryName}`;
                            locationInput.value = location;
                        })
                        .catch(error => {
                            console.log('Geocoding failed, using coordinates');
                            locationInput.value = `${latitude.toFixed(4)}, ${longitude.toFixed(4)}`;
                        });
                },
                function(error) {
                    console.log('Geolocation failed:', error);
                    locationInput.value = 'Location access denied - please enable location services';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000
                }
            );
        } else {
            locationInput.value = 'Geolocation not supported by this browser';
        }
    }

    // Get location on page load if field is empty or shows default text
    if (locationInput.value === 'Detecting location...' || !locationInput.value.trim()) {
        getLocation();
    }

    // Add refresh location button functionality (optional enhancement)
    const refreshBtn = document.createElement('button');
    refreshBtn.type = 'button';
    refreshBtn.className = 'btn btn-sm btn-outline-secondary mt-1';
    refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh Location';
    refreshBtn.onclick = function() {
        locationInput.value = 'Detecting location...';
        getLocation();
    };

    // Insert refresh button after the location input
    locationInput.parentNode.appendChild(refreshBtn);
});
</script>
@endsection
