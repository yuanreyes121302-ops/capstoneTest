@extends('layouts.app')

@push('styles')
<style>
    /* Tenant-specific modern profile design */
    .tenant-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .tenant-header h2 {
        font-weight: 300;
        margin-bottom: 0;
        font-size: 2.5rem;
    }

    .tenant-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .profile-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        border: none;
        margin-bottom: 2rem;
    }

    .profile-content {
        padding: 2.5rem;
    }

    .booking-details {
        margin-bottom: 2rem;
    }

    .booking-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .meta-item i {
        color: #667eea;
        margin-right: 0.5rem;
        font-size: 1.2rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .btn-tenant-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-tenant-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .action-buttons .btn {
        border-radius: 8px;
        font-weight: 500;
    }

    .review-form {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        margin-top: 1rem;
    }

    .review-form .form-label {
        color: #2c3e50;
        font-weight: 600;
    }

    .review-form .form-control {
        border-radius: 8px;
        border: 1px solid #e1e8ed;
    }

    .review-form .btn {
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="tenant-header">
    <div class="container">
        <h2><i class="fas fa-file"></i> Your Booking Requests</h2>
    </div>
</div>

<div class="tenant-container">
    @if ($bookings->count() > 0)
        @foreach ($bookings as $booking)
            <div class="profile-card">
                <div class="profile-content">
                    <div class="booking-details">
                        <div class="booking-meta">
                            <div class="meta-item">
                                <i class="fas fa-building"></i>
                                <div>
                                    <strong>Property:</strong><br>
                                    {{ $booking->property->title }}
                                </div>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                <div>
                                    <strong>Landlord:</strong><br>
                                    {{ $booking->landlord->first_name }} {{ $booking->landlord->last_name }}
                                </div>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <div>
                                    <strong>Date:</strong><br>
                                    {{ $booking->booking_date ? $booking->booking_date->format('F j, Y') : 'Not specified' }}
                                </div>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Time:</strong><br>
                                    {{ $booking->booking_time ? date('g:i A', strtotime($booking->booking_time)) : 'Not specified' }}
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Status:</strong>
                            @if ($booking->status === 'confirmed')
                                <span class="badge bg-success status-badge">Confirmed</span>
                            @elseif ($booking->status === 'accepted')
                                <span class="badge bg-success status-badge">Accepted</span>
                            @elseif ($booking->status === 'declined')
                                <span class="badge bg-danger status-badge">Declined</span>
                            @elseif ($booking->status === 'cancelled')
                                <span class="badge bg-danger status-badge">Cancelled</span>
                            @elseif ($booking->status === 'rescheduled')
                                <span class="badge bg-info status-badge">Rescheduled</span>
                            @else
                                <span class="badge bg-warning status-badge">Pending</span>
                            @endif
                        </div>

                        @if ($booking->room)
                            <div class="mb-3">
                                <strong>Room Booked:</strong> Room {{ $loop->iteration }} - ₱{{ number_format($booking->room->price, 2) }} ({{ $booking->room->capacity }} person(s))
                            </div>
                        @else
                            <div class="mb-3 text-danger">
                                <strong>Room info missing</strong>
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Your Terms:</strong>
                            <pre style="white-space: pre-wrap; background: #f8f9fa; padding: 1rem; border-radius: 8px;">{{ $booking->terms ?? 'None provided.' }}</pre>
                        </div>

                        <div class="mb-3">
                            <strong>Landlord's Terms:</strong>
                            <pre style="white-space: pre-wrap; background: #f8f9fa; padding: 1rem; border-radius: 8px;">{{ $booking->landlord_terms ?? 'None provided.' }}</pre>
                        </div>

                        <small class="text-muted">Requested {{ $booking->created_at->diffForHumans() }}</small>
                    </div>

                    <div class="action-buttons">
                        @if ($booking->status === 'confirmed')
                            <button class="btn btn-info" onclick="toggleContract({{ $booking->id }})">
                                <i class="fas fa-file-contract me-1"></i>View Contract
                            </button>
                        @elseif ($booking->status === 'accepted' && !$booking->signed_by_tenant)
                            <a href="{{ route('bookings.finalize', $booking->id) }}" class="btn btn-tenant-primary">
                                Finalize Contract
                            </a>
                        @elseif ($booking->status === 'pending' && !$booking->signed_by_tenant)
                            <a href="{{ route('bookings.reschedule.page', $booking->id) }}" class="btn btn-warning">
                                <i class="fas fa-calendar-alt me-1"></i>Reschedule
                            </a>
                            <a href="{{ route('bookings.cancel.page', $booking->id) }}" class="btn btn-danger">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                        @elseif ($booking->status === 'accepted' && $booking->finalized_at)
                            <p class="mb-0"><strong>Finalized at:</strong> {{ $booking->finalized_at->format('F j, Y g:i A') }}</p>
                        @endif
                    </div>

                    @if ($booking->status === 'confirmed')
                        <div id="contract{{ $booking->id }}" class="contract-details" style="display: none; margin-top: 1rem; padding: 1.5rem; background: #f8f9ff; border-radius: 10px; border: 1px solid #e1e8ed;">
                            <h5><i class="fas fa-file-contract"></i> Contract Details</h5>
                            <div class="contract-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 1rem;">
                                <div class="info-card" style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #667eea;">
                                    <h6 style="margin: 0 0 0.5rem 0; color: #2c3e50; font-weight: 600;"><i class="fas fa-building"></i> Property</h6>
                                    <p style="margin: 0;">{{ $booking->property->title }} - {{ $booking->property->location }}</p>
                                </div>
                                <div class="info-card" style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #667eea;">
                                    <h6 style="margin: 0 0 0.5rem 0; color: #2c3e50; font-weight: 600;"><i class="fas fa-user"></i> Parties</h6>
                                    <p style="margin: 0;"><strong>Tenant:</strong> {{ $booking->tenant_name }}<br><strong>Landlord:</strong> {{ $booking->landlord->first_name }} {{ $booking->landlord->last_name }}</p>
                                </div>
                                <div class="info-card" style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #667eea;">
                                    <h6 style="margin: 0 0 0.5rem 0; color: #2c3e50; font-weight: 600;"><i class="fas fa-calendar-check"></i> Booking Details</h6>
                                    <p style="margin: 0;">{{ $booking->booking_date->format('F d, Y') }} at {{ date('g:i A', strtotime($booking->booking_time)) }}</p>
                                </div>
                                <div class="info-card" style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #667eea;">
                                    <h6 style="margin: 0 0 0 0.5rem 0; color: #2c3e50; font-weight: 600;"><i class="fas fa-clock"></i> Status</h6>
                                    <p style="margin: 0;">Confirmed on {{ $booking->updated_at->format('F d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                            @if($booking->terms)
                                <div class="terms-section" style="background: white; padding: 1rem; border-radius: 8px; border: 1px solid #e1e8ed; margin-bottom: 1rem;">
                                    <h6 style="color: #667eea; margin-bottom: 0.5rem;"><i class="fas fa-file-alt"></i> Your Terms</h6>
                                    <div style="max-height: 150px; overflow-y: auto;">{{ $booking->terms }}</div>
                                </div>
                            @endif
                            @if($booking->landlord_terms)
                                <div class="terms-section" style="background: white; padding: 1rem; border-radius: 8px; border: 1px solid #e1e8ed;">
                                    <h6 style="color: #667eea; margin-bottom: 0.5rem;"><i class="fas fa-file-contract"></i> Landlord's Terms</h6>
                                    <div style="max-height: 150px; overflow-y: auto;">{{ $booking->landlord_terms }}</div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($booking->status === 'accepted' && $booking->finalized_at)
                        @php
                            $alreadyReviewed = \App\Models\Review::where('tenant_id', auth()->id())
                                ->where('room_id', $booking->room_id)
                                ->exists();
                        @endphp

                        @if (!$alreadyReviewed)
                            <div class="review-form">
                                <h5>Leave a Review</h5>
                                <form action="{{ route('reviews.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="room_id" value="{{ $booking->room_id }}">

                                    <div class="mb-3">
                                        <label for="rating{{ $booking->id }}" class="form-label">Rating (1 to 5)</label>
                                        <select name="rating" class="form-select" id="rating{{ $booking->id }}" required>
                                            <option value="" disabled selected>-- Select Rating --</option>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comment{{ $booking->id }}" class="form-label">Your Feedback</label>
                                        <textarea name="comment" class="form-control" id="comment{{ $booking->id }}" rows="3" required></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-tenant-primary">Submit Review</button>
                                </form>
                            </div>
                        @else
                            <p class="text-muted mt-3">You already submitted a review for this room.</p>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="profile-card">
            <div class="profile-content text-center">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4>No Booking Requests</h4>
                <p>You have no booking requests at the moment.</p>
                <a href="{{ route('tenant.properties.index') }}" class="btn btn-tenant-primary">Browse Properties</a>
            </div>
        </div>
    @endif
</div>

<script>
function toggleContract(bookingId) {
    const contractDiv = document.getElementById('contract' + bookingId);
    if (contractDiv.style.display === 'none' || contractDiv.style.display === '') {
        contractDiv.style.display = 'block';
    } else {
        contractDiv.style.display = 'none';
    }
}
</script>
@endsection
