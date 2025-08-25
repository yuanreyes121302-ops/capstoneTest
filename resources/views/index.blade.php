@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <h1 class="text-center">Welcome to DormHub</h1>
        <p class="text-center">Find and manage student dorms easily.</p>
    </div>
    {{-- temp--}}
    <h2>Users</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Approved</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role }}</td>
            <td>{{ $user->is_approved ? 'Yes' : 'No' }}</td>
            <td>
                @if($user->role !== 'Admin')
                <form action="{{ route('users.makeAdmin', $user->id) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning">Make Admin</button>
                </form>
                @else
                    <span class="text-success">Already Admin</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
