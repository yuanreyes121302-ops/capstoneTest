@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light me-3" style="border-radius: 8px; color: #2c3e50; background: white; border: 1px solid #e1e8ed; transition: all 0.3s ease; font-weight: 500;">
                    ← Back
                </a>
                <h1 style="color: #2c3e50; font-weight: 600; margin: 0;">Pending Approvals</h1>
            </div>
            <a href="{{ route('admin.users.all') }}" class="btn btn-outline-primary" style="border-radius: 8px; padding: 10px 20px;">
                <i class="fas fa-users"></i> View All Users
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm" style="border-radius: 8px;">
                {{ session('success') }}
            </div>
        @endif

        @if ($pendingUsers->isEmpty())
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle fa-3x mb-3" style="color: #27ae60;"></i>
                    <h4 class="text-muted">No Pending Approvals</h4>
                    <p class="text-muted">All users have been approved.</p>
                </div>
            </div>
        @else
            <div class="row">
                @foreach ($pendingUsers as $user)
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-user-circle fa-2x me-3" style="color: #3498db;"></i>
                                    <div>
                                        <h5 class="card-title mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                                        <p class="text-muted mb-0">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>User ID:</strong> {{ $user->user_id }}
                                    </div>
                                    <div class="col-6">
                                        <strong>Role:</strong> <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="flex-fill">
                                        @csrf
                                        <button class="btn btn-success w-100" style="border-radius: 8px;">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.deny', $user->id) }}" method="POST" class="flex-fill">
                                        @csrf
                                        <button class="btn btn-outline-danger w-100" style="border-radius: 8px;" onclick="return confirm('Are you sure you want to deny this user?')">
                                            <i class="fas fa-times"></i> Deny
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection
