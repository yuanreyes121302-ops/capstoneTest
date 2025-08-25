@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Finalized Contracts</h3>

    @if ($bookings->isEmpty())
        <p>No finalized contracts yet.</p>
    @else
        <div class="list-group">
            @foreach ($bookings as $booking)
                <div class="list-group-item mb-3 border rounded p-3">
                    <h5>{{ $booking->property->title }}</h5>
                    <p><strong>Tenant:</strong> {{ $booking->tenant->first_name }} {{ $booking->tenant->last_name }}</p>
                    @if ($booking->room)
                        <p><strong>Room Booked:</strong> {{ $booking->room->name }} - ₱{{ number_format($booking->room->price, 2) }} ({{ $booking->room->capacity }} person(s))</p>
                    @else
                        <p class="text-danger">Room info missing</p>
                    @endif

                    <p><strong>Tenant Terms:</strong> {{ $booking->terms }}</p>
                    <p><strong>Your Terms:</strong> {{ $booking->landlord_terms }}</p>
                    <p><strong>Finalized At:</strong> {{ $booking->finalized_at->format('F d, Y') }}   ({{ $booking->finalized_at->diffForHumans() }})</p> 
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
