@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <!-- Total Users -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <i class="fas fa-users fa-3x text-primary"></i>
                                    </div>
                                    <h4 class="card-title text-primary">{{ $totalUsers }}</h4>
                                    <p class="card-text text-muted">Total Users</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Approvals -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <i class="fas fa-clock fa-3x text-warning"></i>
                                    </div>
                                    <h4 class="card-title text-warning">{{ $pendingApprovals }}</h4>
                                    <p class="card-text text-muted">Pending Approvals</p>
                                </div>
                            </div>
                        </div>

                        <!-- Active Admins -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <i class="fas fa-user-shield fa-3x text-success"></i>
                                    </div>
                                    <h4 class="card-title text-success">{{ $activeAdmins }}</h4>
                                    <p class="card-text text-muted">Active Admins</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-center mb-3">Quick Actions</h5>
                            <div class="d-flex justify-content-center flex-wrap">
                                <a href="{{ route('admin.users') }}" class="btn btn-outline-primary me-2 mb-2">
                                    <i class="fas fa-list-check"></i> View Pending Approvals
                                </a>
                                <a href="{{ route('admin.users.all') }}" class="btn btn-outline-secondary me-2 mb-2">
                                    <i class="fas fa-users"></i> All Users
                                </a>
                                <a href="{{ route('admin.add.form') }}" class="btn btn-outline-success me-2 mb-2">
                                    <i class="fas fa-user-plus"></i> Add Admin
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}
</style>
@endsection
