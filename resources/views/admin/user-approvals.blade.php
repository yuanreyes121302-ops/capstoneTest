@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Pending Approvals</h2>
        <a href="{{ route('admin.users.all') }}" class="btn btn-primary">View All Users</a>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendingUsers as $user)
                    <tr>
                        <td>{{ $user->user_id }}</td>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn btn-success btn-sm m-1">Approve</button>
                            </form>

                            <form action="{{ route('admin.users.deny', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn btn-danger btn-sm">Deny</button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No pending users.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
