@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/landlord.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
@endpush
@stack('styles')

@section('content')
<div class="container">
    <div class="navbarcontainer">
<div class="d-flex justify-content-between align-items-center mb-3">
        <h2>My Profile</h2>
        <a href="{{ route('landlord.properties.index') }}" class="btn btn-outline-primary">
            🏠 Manage Properties
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3 d-flex justify-content-center">
        <div class="d-flex align-items-center">
            @if (auth()->user()->profile_image)
                <img src="{{ asset('storage/profile_images/' . auth()->user()->profile_image) }}?v={{ time() }}"
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
        <form method="POST" action="{{ route('landlord.profile.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">User ID</label>
                    <input type="text" class="form-control" value="{{ $user->user_id }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" 
                           value="{{ old('email', $user->email) }}">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input name="first_name" class="form-control" 
                           value="{{ old('first_name', $user->first_name) }}">
                    @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input name="last_name" class="form-control" 
                           value="{{ old('last_name', $user->last_name) }}">
                    @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control">
                        <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $user->contact_number) }}">
                    @error('contact_number') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>



            <div class="text-end">
                <button class="btn btn-primary">Update Profile</button>
            </div>
        </form>
    </div>
    </div>


</div>
@endsection

