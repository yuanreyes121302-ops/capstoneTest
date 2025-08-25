@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Rooms for {{ $property->title }}</h4>
    <a href="{{ route('landlord.rooms.create', $property->id) }}" class="btn btn-primary mb-3">+ Add Room</a>

    @foreach($rooms as $room)
    <div class="card mb-2 p-3">
        <h5>{{ $room->name }}</h5>
        <p>Price: ₱{{ number_format($room->price, 2) }} | Capacity: {{ $room->capacity }} | Available: {{ $room->available_slots }}</p>
        @if ($room->images->isNotEmpty())
            <div class="d-flex flex-wrap gap-2">
                @foreach ($room->images as $image)
                    <img src="{{ asset('storage/' . $image->image_path) }}"
                        style="width: 120px; height: 90px; object-fit: cover; border-radius: 6px;">
                @endforeach
            </div>
        @endif
        
        <h5>Reviews</h5>

        @forelse ($room->reviews as $review)
            <div class="border p-3 mb-3 rounded">
                <div class="d-flex justify-content-between">
                    <strong>{{ $review->tenant->first_name }}</strong>
                    <span class="text-warning">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </span>
                </div>
                <p class="mb-1">{{ $review->comment }}</p>
                <small class="text-muted">{{ $review->created_at->format('F j, Y g:i A') }}</small>

                @if ($review->reply)
                    <div class="mt-2 p-2 bg-light border-start border-success border-3">
                        <strong>Your reply:</strong> {{ $review->reply }}
                    </div>
                @else
                    <form action="{{ route('landlord.reviews.reply', $review->id) }}" method="POST" class="mt-2">
                        @csrf
                        @method('PATCH')
                        <div class="input-group">
                            <input type="text" name="reply" class="form-control" placeholder="Write a reply..." required>
                            <button class="btn btn-sm btn-success" type="submit">Reply</button>
                        </div>
                    </form>
                @endif
            </div>
        @empty
            <p class="text-muted">No reviews yet.</p>
        @endforelse

        <div class="card-footer d-flex justify-content">
            <a href="{{ route('landlord.rooms.edit', $room->id) }}" class="btn btn-warning btn-sm m-1">Edit</a>
            <form action="{{ route('landlord.rooms.destroy', $room->id) }}" method="POST" class="d-inline m-1">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection
