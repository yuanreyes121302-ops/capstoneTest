@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="color: #2c3e50; font-weight: 600;">All Users</h1>
            <div>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-danger position-relative me-2" style="border-radius: 8px; padding: 10px 20px;">
                    <i class="fas fa-clock"></i> Pending Approvals
                    <span id="pending-count" class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $pendingUsers->count() }}</span>
                </a>
                <a href="{{ route('admin.add.form') }}" class="btn btn-outline-success" style="border-radius: 8px; padding: 10px 20px;">
                    <i class="fas fa-user-plus"></i> Add Admin
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm" style="border-radius: 8px;">
                @foreach ($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- User Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2" style="color: #3498db;"></i>
                        <h6 class="card-title">Total Users</h6>
                        <h4 class="text-primary mb-0">{{ $total }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body text-center">
                        <i class="fas fa-user-shield fa-2x mb-2" style="color: #27ae60;"></i>
                        <h6 class="card-title">Admins</h6>
                        <h4 class="text-success mb-0">{{ $admins }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body text-center">
                        <i class="fas fa-home fa-2x mb-2" style="color: #e67e22;"></i>
                        <h6 class="card-title">Landlords</h6>
                        <h4 class="text-warning mb-0">{{ $landlords }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body text-center">
                        <i class="fas fa-graduation-cap fa-2x mb-2" style="color: #9b59b6;"></i>
                        <h6 class="card-title">Tenants</h6>
                        <h4 class="text-info mb-0">{{ $tenants }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.all') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Filter by Role</label>
                            <select name="role" class="form-select" onchange="this.form.submit()" style="border-radius: 8px;">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="landlord" {{ request('role') == 'landlord' ? 'selected' : '' }}>Landlord</option>
                                <option value="tenant" {{ request('role') == 'tenant' ? 'selected' : '' }}>Tenant</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Search</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="{{ request('search') }}" style="border-radius: 8px 0 0 8px;">
                                <button class="btn btn-primary" type="submit" style="border-radius: 0 8px 8px 0;"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-semibold py-3 px-4">User ID</th>
                                <th class="border-0 fw-semibold py-3 px-4">Name</th>
                                <th class="border-0 fw-semibold py-3 px-4">Gender</th>
                                <th class="border-0 fw-semibold py-3 px-4">Date of Birth</th>
                                <th class="border-0 fw-semibold py-3 px-4">Email</th>
                                <th class="border-0 fw-semibold py-3 px-4">Role</th>
                                <th class="border-0 fw-semibold py-3 px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="py-3 px-4">{{ $user->user_id }}</td>
                                    <td class="py-3 px-4">{{ $user->first_name }} {{ $user->last_name }}</td>
                                    <td class="py-3 px-4">{{ ucfirst($user->gender) }}</td>
                                    <td class="py-3 px-4">{{ $user->dob }}</td>
                                    <td class="py-3 px-4">{{ $user->email }}</td>
                                    <td class="py-3 px-4">
                                        <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm" style="border-radius: 6px;"><i class="fas fa-trash"></i> Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<script>
$(document).ready(function() {
    function updatePendingCount() {
        $.get('{{ route("admin.pending.count") }}', function(data) {
            $('#pending-count').text(data.count);
        });
    }

    // Update every 30 seconds
    setInterval(updatePendingCount, 30000);
});
</script>

<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
.table th, .table td {
    vertical-align: middle;
}
</style>
@endsection
