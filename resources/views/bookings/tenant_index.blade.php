@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Your Booking Request</h4>
    @if ($bookings->count() > 0)
        @foreach ($bookings as $booking)
            <div class="card mb-3">
                <div class="card-body">
                    <strong>Property:</strong> {{ $booking->property->title }}<br>
                    <strong>Landlord:</strong> {{ $booking->landlord->first_name }} {{ $booking->landlord->last_name }}<br>
                    <strong>Status:</strong>
                    @if ($booking->status === 'accepted')
                        <span class="badge bg-success">Accepted</span>
                    @elseif ($booking->status === 'declined')
                        <span class="badge bg-danger">Declined</span>
                    @else
                        <span class="badge bg-secondary">Pending</span>
                    @endif
                    <br>
                    @if ($booking->room)
                        <p><strong>Room Booked:</strong> Room {{ $loop->iteration }} - ₱{{ number_format($booking->room->price, 2) }} ({{ $booking->room->capacity }} person(s))</p>
                    @else
                        <p class="text-danger">Room info missing</p>
                    @endif
                    <strong>Your Terms:</strong> <pre>{{ $booking->terms ?? 'None provided.' }}</pre>
                    <strong>Landlords Terms:</strong> <pre>{{ $booking->landlord_terms ?? 'None provided.' }}</pre>
                    <small class="text-muted">Requested {{ $booking->created_at->diffForHumans() }}</small>
                </div>
                @if ($booking->status === 'accepted' && !$booking->signed_by_tenant)
                    <a href="{{ route('bookings.finalize', $booking->id) }}" class="btn btn-primary btn-sm">
                        Finalize Contract
                    </a>
                @elseif ($booking->status === 'pending' && !$booking->signed_by_tenant)
                    <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm m-3">Cancel Request</button>
                    </form>
                @elseif ($booking->status === 'accepted' && $booking->finalized_at)
                     <p class="m-2"><strong>Finalized at:</strong> {{ $booking->finalized_at->format('F j, Y g:i A') }}</p>
                @endif
            </div>
            @if ($booking->status === 'accepted' && $booking->finalized_at)
    @php
        $alreadyReviewed = \App\Models\Review::where('tenant_id', auth()->id())
            ->where('room_id', $booking->room_id)
            ->exists();
    @endphp

    @if (!$alreadyReviewed)
        <form action="{{ route('reviews.store') }}" method="POST" class="mt-3">
            @csrf
            <input type="hidden" name="room_id" value="{{ $booking->room_id }}">

            <div class="mb-2">
                <label for="rating" class="form-label">Rating (1 to 5)</label>
                <select name="rating" class="form-select" required>
                    <option value="" disabled selected>-- Select Rating --</option>
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="mb-2">
                <label for="comment" class="form-label">Your Feedback</label>
                <textarea name="comment" class="form-control" rows="3" required></textarea>
            </div>

            <button type="submit" class="btn btn-sm btn-success">Submit Review</button>
        </form>
    @else
        <p class="text-muted">You already submitted a review for this room.</p>
    @endif
@endif

        @endforeach
    @else
        <div class="alert alert-info">
            You requested no booking requests at the moment.
        </div>
    @endif
</div>
@endsection
