@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Cancel Booking Request</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5>Are you sure you want to cancel this booking?</h5>
                        <p>This action cannot be undone.</p>
                    </div>

                    <div class="booking-details mb-4">
                        <h5>Booking Details:</h5>
                        <p><strong>Property:</strong> {{ $booking->property->title }}</p>
                        <p><strong>Room:</strong> {{ $booking->room ? 'Room ' . $booking->room->id : 'N/A' }}</p>
                        <p><strong>Date:</strong> {{ $booking->booking_date ? $booking->booking_date->format('F j, Y') : 'Not specified' }}</p>
                        <p><strong>Time:</strong> {{ $booking->booking_time ? date('g:i A', strtotime($booking->booking_time)) : 'Not specified' }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($booking->status) }}</p>
                    </div>

                    <div class="d-flex gap-3">
                        <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                Yes, Cancel Booking
                            </button>
                        </form>
                        <a href="{{ route('bookings.tenant.index') }}" class="btn btn-secondary">No, Keep Booking</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
