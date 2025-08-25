@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">
    ← Back
</a>
    <h3 class="mb-4">Add New Admin</h3>

    <form action="{{ route('admin.add.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label>User ID</label>
                <input name="user_id" class="form-control" value="{{ old('user_id') }}">
                @error('user_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6">
                <label>Email</label>
                <input name="email" class="form-control" value="{{ old('email') }}">
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>First Name</label>
                <input name="first_name" class="form-control" value="{{ old('first_name') }}">
                @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6">
                <label>Last Name</label>
                <input name="last_name" class="form-control" value="{{ old('last_name') }}">
                @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">-- Select Gender --</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6">
                <label>Date of Birth</label>
                <input type="date" name="dob" class="form-control"
       value="{{ old('dob') }}"
       min="1900-01-01"
       max="{{ now()->subYears(10)->format('Y-m-d') }}">
                @error('dob') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
        </div>

        <button class="btn btn-success">Add Admin</button>
    </form>
</div>
@endsection
