@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>My Profile</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="mb-3 text-center">
        @if ($tenant->profile_image)
            <img src="{{ asset('storage/profile_images/' . $tenant->profile_image) }}" 
                alt="Profile Image" class="rounded-circle mb-2" style="width: 120px; height: 120px; object-fit: cover;">
        @else
            <img src="{{ asset('default-avatar.png') }}" 
                alt="Default Profile" class="rounded-circle mb-2" style="width: 120px; height: 120px; object-fit: cover;">
        @endif
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
                    <input type="email" class="form-control" value="{{ old('email', $tenant->email) }}">
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
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="{{ old('dob', $tenant->dob) }}">
                    @error('dob') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>
            <div class="row-mb-3">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="profile_image" class="form-control mt-2" accept="image/*">
                    @error('profile_image') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="text-end">
                <button class="btn btn-primary">Update Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection
