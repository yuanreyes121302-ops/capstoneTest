@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0"><i class="fas fa-tachometer-alt"></i> Landlord Dashboard</h3>
                </div>
                <div class="card-body">
                    <!-- Property Overview Section -->
                    <div class="row text-center mb-4">
                        <div class="col-md-3 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <i class="fas fa-building fa-3x text-primary"></i>
                                    </div>
                                    <h4 class="card-title text-primary">{{ $totalProperties }}</h4>
                                    <p class="card-text text-muted">Total Properties</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <i class="fas fa-check-circle fa-3x text-success"></i>
                                    </div>
                                    <h4 class="card-title text-success">{{ $activeProperties }}</h4>
                                    <p class="card-text text-muted">Active Properties</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-4">
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

                        <div class="col-md-3 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <i class="fas fa-times-circle fa-3x text-danger"></i>
                                    </div>
                                    <h4 class="card-title text-danger">{{ $rejectedProperties }}</h4>
                                    <p class="card-text text-muted">Rejected / Archived</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <a href="{{ route('landlord.properties.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i> View All Properties
                            </a>
                        </div>
                    </div>

                    <!-- Requests Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-envelope"></i> Latest Requests</h5>
                                </div>
                                <div class="card-body">
                                    @if($latestRequests->count() > 0)
                                        @foreach($latestRequests as $request)
                                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                                <div>
                                                    <strong>{{ $request->tenant->first_name }} {{ $request->tenant->last_name }}</strong><br>
                                                    <small class="text-muted">{{ $request->property->title }}</small><br>
                                                    <small class="text-muted">{{ $request->created_at->format('M d, Y') }}</small>
                                                </div>
                                                <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'confirmed' ? 'success' : ($request->status === 'accepted' ? 'success' : 'danger')) }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted text-center">No recent requests</p>
                                    @endif
                                </div>
                                <div class="card-footer text-center">
                                    <a href="{{ route('bookings.landlord.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-eye"></i> View All Requests
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions Panel -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('landlord.properties.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Add New Property
                                        </a>
                                        <a href="{{ route('landlord.properties.index') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-cog"></i> Manage Properties
                                        </a>
                                        <a href="{{ route('bookings.landlord.index') }}" class="btn btn-outline-success">
                                            <i class="fas fa-tasks"></i> Handle Requests
                                        </a>
                                        <a href="{{ route('landlord.contracts') }}" class="btn btn-outline-warning">
                                            <i class="fas fa-file-contract"></i> View Contracts
                                        </a>
                                        <a href="{{ route('landlord.profile') }}" class="btn btn-outline-info">
                                            <i class="fas fa-user"></i> View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Activity</h5>
                                </div>
                                <div class="card-body">
                                    @if($recentActivity->count() > 0)
                                        @foreach($recentActivity as $activity)
                                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                                <div>
                                                    <i class="fas fa-circle text-{{ $activity->status === 'pending' ? 'warning' : ($activity->status === 'confirmed' ? 'success' : ($activity->status === 'accepted' ? 'success' : 'danger')) }} me-2"></i>
                                                    <strong>{{ $activity->tenant->first_name }} {{ $activity->tenant->last_name }}</strong>
                                                    {{ $activity->status === 'pending' ? 'sent a booking request for' : ($activity->status === 'confirmed' ? 'booking was confirmed for' : ($activity->status === 'accepted' ? 'booking was accepted for' : 'booking was declined for')) }}
                                                    <strong>{{ $activity->property->title }}</strong>
                                                </div>
                                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted text-center">No recent activity</p>
                                    @endif
                                </div>
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
