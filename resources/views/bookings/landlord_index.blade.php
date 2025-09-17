@extends('layouts.app')

@push('styles')
<style>
    /* Landlord-specific modern design for requests page */
    .landlord-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .landlord-header h2 {
        font-weight: 300;
        margin-bottom: 0;
        font-size: 2.5rem;
    }

    .landlord-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .request-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        border: none;
        margin-bottom: 2rem;
        transition: transform 0.3s ease;
    }

    .request-card:hover {
        transform: translateY(-5px);
    }

    .request-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .tenant-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .tenant-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .tenant-details h5 {
        margin: 0;
        font-weight: 600;
    }

    .tenant-details p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.9rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .request-body {
        padding: 2rem;
    }

    .request-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .detail-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 10px;
        border-left: 4px solid #667eea;
    }

    .detail-item h6 {
        margin: 0 0 0.5rem 0;
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .detail-item p {
        margin: 0;
        color: #34495e;
        font-size: 0.95rem;
    }

    .terms-section {
        background: #f8f9ff;
        padding: 1.5rem;
        border-radius: 10px;
        border: 1px solid #e1e8ed;
        margin-bottom: 2rem;
    }

    .terms-section h6 {
        color: #667eea;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .terms-content {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e1e8ed;
        max-height: 150px;
        overflow-y: auto;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .btn-action {
        border-radius: 25px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        border: none;
        min-width: 120px;
    }

    .btn-accept {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
    }

    .btn-accept:hover {
        background: linear-gradient(135deg, #229954 0%, #27ae60 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(39, 174, 96, 0.5);
    }

    .btn-decline {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
    }

    .btn-decline:hover {
        background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(231, 76, 60, 0.5);
    }

    .terms-form {
        margin-top: 1rem;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border: 2px solid #e1e8ed;
        border-radius: 10px;
        padding: 0.8rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .no-requests {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .no-requests i {
        font-size: 4rem;
        color: #bdc3c7;
        margin-bottom: 1rem;
    }

    .no-requests h4 {
        color: #7f8c8d;
        margin-bottom: 0.5rem;
    }

    .no-requests p {
        color: #95a5a6;
    }

    .back-btn {
        position: absolute;
        top: 2rem;
        left: 2rem;
        z-index: 10;
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .back-btn:hover {
        background: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }

    @media (max-width: 768px) {
        .request-details {
            grid-template-columns: 1fr;
        }

        .tenant-info {
            flex-direction: column;
            text-align: center;
            gap: 0.5rem;
        }

        .request-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
        }
    }

    /* Notification styles */
    .alert {
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .alert-success {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        color: white;
    }

    .alert-danger {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="landlord-header">
    <div class="landlord-container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <a href="{{ route('landlord.dashboard') }}" class="btn btn-light me-3" style="border-radius: 8px; color: #2c3e50; background: white; border: 1px solid #e1e8ed; transition: all 0.3s ease; font-weight: 500;">
                    ← Back
                </a>
                <h2>Manage Requests</h2>
            </div>
            <a href="{{ route('landlord.properties.index') }}" class="btn btn-light">
                <i class="fas fa-home"></i> Manage Properties
            </a>
        </div>
    </div>
</div>

<div class="landlord-container">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($bookings->count() > 0)
        @foreach ($bookings as $booking)
            <div class="request-card">
                <div class="request-header">
                    <div class="tenant-info">
                        @if($booking->tenant->profile_image)
                            <img src="{{ asset('storage/profile_images/' . $booking->tenant->profile_image) }}"
                                alt="Tenant Photo" class="tenant-avatar">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}"
                                alt="No Photo" class="tenant-avatar">
                        @endif
                        <div class="tenant-details">
                            <h5>{{ $booking->tenant->first_name }} {{ $booking->tenant->last_name }}</h5>
                            <p><i class="fas fa-envelope me-1"></i>{{ $booking->tenant->email }}</p>
                            <p><i class="fas fa-calendar me-1"></i>Requested {{ $booking->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <span class="status-badge bg-warning">
                        {{ $booking->updated_at > $booking->created_at ? 'Reschedule Pending' : ucfirst($booking->status) }}
                    </span>
                </div>

                <div class="request-body">
                    <div class="request-details">
                        <div class="detail-item">
                            <h6><i class="fas fa-building me-1"></i>Property</h6>
                            <p>{{ $booking->property->title }}</p>
                        </div>
                        <div class="detail-item">
                            <h6><i class="fas fa-map-marker-alt me-1"></i>Location</h6>
                            <p>{{ $booking->property->location }}</p>
                        </div>
                        <div class="detail-item">
                            <h6><i class="fas fa-door-open me-1"></i>Room</h6>
                            <p>{{ $booking->room ? $booking->room->name : 'Not specified' }}</p>
                        </div>
                        <div class="detail-item">
                            <h6><i class="fas fa-calendar-check me-1"></i>Booking Date & Time</h6>
                            <p>{{ $booking->booking_date->format('F d, Y') }} at {{ $booking->booking_time->format('g:i A') }}</p>
                        </div>
                        <div class="detail-item">
                            <h6><i class="fas fa-clock me-1"></i>Requested On</h6>
                            <p>{{ $booking->created_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>

                    @if($booking->terms)
                        <div class="terms-section">
                            <h6><i class="fas fa-file-alt me-1"></i>Tenant's Terms & Conditions</h6>
                            <div class="terms-content">{{ $booking->terms }}</div>
                        </div>
                    @endif

                    @if ($booking->status === 'pending')
                        <div class="action-buttons">
                            <button class="btn btn-action btn-accept" onclick="showAcceptForm({{ $booking->id }})">
                                <i class="fas fa-check me-1"></i>Accept
                            </button>
                            <form action="{{ route('bookings.decline', $booking->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-action btn-decline"
                                        onclick="return confirm('Are you sure you want to decline this request?')">
                                    <i class="fas fa-times me-1"></i>Decline
                                </button>
                            </form>
                            <a href="{{ route('messages.show', $booking->tenant->id) }}" class="btn btn-action" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                                <i class="fas fa-comments me-1"></i>Message Tenant
                            </a>
                        </div>

                        <div id="acceptForm{{ $booking->id }}" class="terms-form" style="display: none;">
                            <hr>
                            <form action="{{ route('bookings.accept', $booking->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="landlord_terms_{{ $booking->id }}" class="form-label">
                                        <i class="fas fa-edit me-1"></i>Your Terms & Conditions
                                    </label>
                                    <textarea name="landlord_terms" id="landlord_terms_{{ $booking->id }}"
                                              class="form-control" rows="4" required
                                              placeholder="Enter your terms and conditions for this booking...">{{ old('landlord_terms') }}</textarea>
                                    @error('landlord_terms')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary me-2" onclick="hideAcceptForm({{ $booking->id }})">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-action btn-accept">
                                        <i class="fas fa-paper-plane me-1"></i>Send Terms
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="action-buttons">
                            <a href="{{ route('messages.show', $booking->tenant->id) }}" class="btn btn-action" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                                <i class="fas fa-comments me-1"></i>Message Tenant
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $bookings->links() }}
            </div>
        @endif
    @else
        <div class="no-requests">
            <i class="fas fa-inbox"></i>
            <h4>No Pending Requests</h4>
            <p>You don't have any booking requests at the moment.</p>
            <a href="{{ route('landlord.dashboard') }}" class="btn btn-light" style="border-radius: 8px; color: #2c3e50; background: white; border: 1px solid #e1e8ed; transition: all 0.3s ease; font-weight: 500;">
                ← Back
            </a>
        </div>
    @endif
</div>

<script>
function showAcceptForm(bookingId) {
    document.getElementById('acceptForm' + bookingId).style.display = 'block';
    document.querySelector('#acceptForm' + bookingId + ' textarea').focus();
}

function hideAcceptForm(bookingId) {
    document.getElementById('acceptForm' + bookingId).style.display = 'none';
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
@endsection
