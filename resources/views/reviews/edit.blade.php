@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Your Review</h4>

    <form action="{{ route('reviews.update', $review->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="rating" class="form-label">Rating</label>
            <select name="rating" class="form-select" required>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ $i == $review->rating ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">Comment</label>
            <textarea name="comment" class="form-control" rows="4" required>{{ $review->comment }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Review</button>
        <a href="{{ route('bookings.tenant.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
