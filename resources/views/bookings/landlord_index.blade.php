@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Incoming Booking Requests</h4>
    @if ($bookings->count() > 0)
        @foreach ($bookings as $booking)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex">
                        @if($booking->tenant->profile_image)
                            <img src="{{ $booking->tenant->profile_image 
                                ? asset('storage/profile_images/' . $booking->tenant->profile_image) 
                                : asset('default-avatar.png') }}" 
                                alt="Tenant Photo" 
                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; margin-right: 15px;">
                        @else
                            <img src="{{ asset('images/default-user.png') }}" 
                                alt="No Photo" 
                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; margin-right: 15px;">
                        @endif

                        <div>
                            <strong>Name:</strong> {{ $booking->tenant->first_name }} {{ $booking->tenant->last_name }}<br>
                            <strong>Email:</strong> {{ $booking->tenant->email }}<br>
                            <strong>Gender:</strong> {{ $booking->tenant->gender }}<br>
                            <strong>Date of Birth:</strong> {{ $booking->tenant->dob }}<br>
                        </div>
                    </div>

                    <hr>
                    <strong>Property:</strong> {{ $booking->property->title }}<br>
                    <strong>Status:</strong> <span class="badge bg-secondary">{{ $booking->status }}</span><br>
                    <strong>Terms:</strong> <pre>{{ $booking->terms ?? 'None provided.' }}</pre>
                    @if ($booking->room)
                        <p><strong>Room Booked:</strong> Room {{ $loop->iteration }} - ₱{{ number_format($booking->room->price, 2) }} ({{ $booking->room->capacity }} person(s))</p>
                    @else
                        <p class="text-danger">Room info missing</p>
                    @endif
                    @if ($booking->status === 'pending')
                        <form action="{{ route('bookings.accept', $booking->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label for="landlord_terms_{{ $booking->id }}" class="form-label"><strong>Your Terms:</strong></label>
                                <textarea name="landlord_terms" id="landlord_terms_{{ $booking->id }}" class="form-control" rows="3" required>{{ old('landlord_terms') }}</textarea>
                            </div>
                            <button class="btn btn-success btn-sm">Accept with Terms</button>
                        </form>

                        <form action="{{ route('bookings.decline', $booking->id) }}" method="POST" class="mt-1">
                            @csrf
                            <button class="btn btn-danger btn-sm">Decline</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">
            You have no pending booking requests at the moment.
        </div>
    @endif
</div>
@endsection
