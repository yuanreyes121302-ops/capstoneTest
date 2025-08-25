@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('messages.inbox') }}" class="btn btn-outline-primary mb-3">
        Go to Inbox
    </a>

    @if ($otherUser)
        <div class="d-flex flex-column align-items-center text-center">
            <div class="card mb-3 mt-2 w-100" style="max-width: 540px;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="{{ $otherUser->profile_image ? asset('storage/profile_images/' . $otherUser->profile_image) : asset('default-avatar.png') }}"
                            alt="Profile Image"
                            class="img-fluid rounded-start w-100 h-100 object-fit-cover m-1"
                            style="max-height: 180px;max-width: 180px;">
                    </div>
                    <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">{{ $otherUser->first_name }} {{ $otherUser->last_name }}</h5>
                        <p class="card-text"><strong>Email:</strong> {{ $otherUser->email }}</p>
                        <p class="card-text"><strong>Gender:</strong> {{ ucfirst($otherUser->gender) }}</p>
                        <p class="card-text"><strong>DOB:</strong> {{ $otherUser->dob }}</p>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border rounded p-3 mb-3" style="height: 300px; overflow-y: auto;">
            @foreach ($messages as $msg)
                <div class="mb-2">
                    <strong>{{ $msg->sender_id == auth()->id() ? 'You' : ($msg->sender->first_name ?? 'Unknown') }}:</strong>
                    <p class="mb-1">{{ $msg->message }}</p>
                    <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('messages.send') }}">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
            <div class="input-group">
                <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                <button type="submit" class="btn btn-primary">Send</button>
            </div>
        </form>
    @else
        <div class="alert alert-danger">
            Error: Landlord not found.
        </div>
    @endif
</div>
@endsection
