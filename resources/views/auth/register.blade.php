@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group">
                            <label for="user_id">User ID</label>
                            <input id="user_id" type="text" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ old('user_id') }}" required autofocus>

                            @error('user_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input id="form-control" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender" class="form-control" required>
                                <option value="">-- Select --</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input id="dob" type="date" class="form-control" name="dob" min="1900-01-01"
                            max="<?php echo date('Y-m-d'); ?>" value="{{ old('dob') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Register As</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">-- Select Role --</option>
                                <option value="tenant" {{ old('role') == 'tenant' ? 'selected' : '' }}>Tenant</option>
                                <option value="landlord" {{ old('role') == 'landlord' ? 'selected' : '' }}>Landlord</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password-confirm">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                Register
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
