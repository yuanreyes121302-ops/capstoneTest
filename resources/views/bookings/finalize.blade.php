@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Finalize Booking Agreement</h4>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Property: {{ $booking->property->title }}</h5>
            @if ($booking->room)
                <p><strong>Room Booked:</strong> {{ $booking->room->name }} - ₱{{ number_format($booking->room->price, 2) }} ({{ $booking->room->capacity }} person(s))</p>
            @else
                <p class="text-danger">Room info missing</p>
            @endif
            <hr>
            <h6>Your Submitted Terms:</h6>
            <p>{{ $booking->terms ?? 'None' }}</p>

            <h6>Landlord’s Terms:</h6>
            <p>{{ $booking->landlord_terms ?? 'None' }}</p>

            <form method="POST" action="{{ route('bookings.finalize.submit', $booking->id) }}">
                @csrf
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" name="agree" id="agree" required>
                    <label class="form-check-label" for="agree">I agree to all terms and conditions above.</label>
                </div>
                <button type="submit" class="btn btn-success">Finalize Agreement</button>
                <a href="{{ route('bookings.tenant.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
