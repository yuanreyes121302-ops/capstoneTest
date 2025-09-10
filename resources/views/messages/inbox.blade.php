@extends('layouts.app')

@section('content')
    <div class="container">
        <h4>Your Conversations</h4>

        @forelse ($conversations as $userId => $messages)
            @php
                $lastMessage = $messages->first();
                $otherUser = $lastMessage->sender_id == auth()->id()
                    ? $lastMessage->receiver
                    : $lastMessage->sender;
            @endphp

            <div class="card mb-3">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img
                            src="{{ $otherUser->profile_image ? asset('storage/profile_images/' . $otherUser->profile_image) : asset('default-avatar.png') }}"
                            alt="Profile"
                            class="rounded-circle me-3"
                            width="50"
                            height="50"
                        >
                        <div>
                            <h6 class="mb-0">{{ $otherUser->first_name }} {{ $otherUser->last_name }}</h6>
                            <small class="text-muted">
                                {{ \Illuminate\Support\Str::limit($lastMessage->message, 40) }}
                            </small>
                        </div>
                    </div>

                    <a href="{{ route('messages.index', ['userId' => $otherUser->id]) }}" class="btn btn-sm btn-outline-primary">
                        View Chat →
                    </a>
                </div>
            </div>
        @empty
            <p>No messages yet.</p>
        @endforelse
    </div>
@endsection
