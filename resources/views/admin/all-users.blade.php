@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>All Users</h2>
        <div>
            <a href="{{ route('admin.users') }}" class="btn btn-primary">View Pending Approvals</a>
            <a href="{{ route('admin.add.form') }}" class="btn btn-primary">➕ Add Admin</a>
        </div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    <div class="mb-4">
    <h5>User Summary</h5>
        <ul class="list-group list-group-horizontal">
            <li class="list-group-item">Total Users: <strong>{{ $total }}</strong></li>
            <li class="list-group-item">Admins: <strong>{{ $admins }}</strong></li>
            <li class="list-group-item">Landlords: <strong>{{ $landlords }}</strong></li>
            <li class="list-group-item">Tenants: <strong>{{ $tenants }}</strong></li>
        </ul>
    </div>
    <form method="GET" action="{{ route('admin.users.all') }}" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <select name="role" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Filter by Role --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="landlord" {{ request('role') == 'landlord' ? 'selected' : '' }}>Landlord</option>
                    <option value="tenant" {{ request('role') == 'tenant' ? 'selected' : '' }}>Tenant</option>
                </select>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search name or email" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary mt-1" type="submit">Search</button>
            </div>
        </div>
    </form>


    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->user_id }}</td>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ ucfirst($user->gender) }}</td>
                        <td>{{ $user->dob }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>
                            <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete Account</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7">No existing users.</td></tr>
              
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
