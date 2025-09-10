@extends('layouts.app')

@section('content')
    @push('styles')
        <link href="{{ asset('css/messages.css') }}" rel="stylesheet">
        <style>
            /* Custom Profile Card */
            .card {
                border-radius: 15px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 15px;
                background-color: #fff;
            }

            .card img {
                border-radius: 50%;
                width: 150px;
                height: 150px;
                object-fit: cover;
                border: 3px solid #eee;
            }

            /* Message Container */
            .border {
                height: 400px;
                overflow-y: auto;
                border-radius: 10px;
                background-color: #f9f9f9;
                padding: 15px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            /* Individual Message */
            .mb-2 {
                background-color: #fff;
                border-radius: 10px;
                padding: 12px;
                margin-bottom: 15px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
                transition: background-color 0.3s ease;
            }

            .mb-2 strong {
                font-weight: bold;
            }

            .mb-2 p {
                margin-top: 8px;
                font-size: 14px;
                line-height: 1.6;
            }

            .mb-2 small {
                font-size: 12px;
                color: #999;
            }

            /* Message Styles for Sender and Receiver */
            .message-bubble.sent {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                margin-left: auto;
                text-align: right;
                border-radius: 18px 18px 4px 18px;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            }

            .message-bubble.received {
                background: #fff3cd;
                color: #333;
                border-radius: 18px 18px 18px 4px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            /* Hover Effect for Messages */
            .mb-2:hover {
                background-color: #f1f1f1;
            }

            /* Input Group */
            .input-group {
                display: flex;
                gap: 10px;
            }

            .input-group .form-control {
                border-radius: 25px;
                padding: 12px;
                font-size: 14px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                border: 1px solid #ddd;
            }

            .input-group button {
                border-radius: 25px;
                padding: 12px 20px;
                font-size: 14px;
                transition: background-color 0.3s ease;
            }

            .input-group button:hover {
                background-color: #007bff;
            }
        </style>
    @endpush

    <div class="container">
        <a href="{{ route('messages.inbox') }}" class="btn btn-outline-primary mb-3">
            Go to Inbox
        </a>

        @if ($otherUser)
            <div class="d-flex flex-column align-items-center text-center mb-2">
                <div class="position-relative d-inline-block">
                    <img
                        src="{{ $otherUser->profile_image ? asset('storage/profile_images/' . $otherUser->profile_image) : asset('default-avatar.png') }}"
                        alt="Profile Image"
                        class="img-fluid rounded-circle"
                        style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #ddd;"
                    >
                    <!-- Online/Offline Status Indicator -->
                    <span id="user-status" class="position-absolute bottom-0 end-0 {{ $otherUser->isCurrentlyOnline() ? 'bg-success' : 'bg-secondary' }} border border-white rounded-circle"
                          style="width: 15px; height: 15px;" title="{{ $otherUser->isCurrentlyOnline() ? 'Online' : 'Offline' }}">
                    </span>
                </div>
                <div class="mt-2">
                    <h6 class="mb-1" style="font-size: 1rem; color: #333;">{{ $otherUser->first_name }} {{ $otherUser->last_name }}</h6>
                    <small class="text-muted" style="font-size: 0.8rem;">{{ $otherUser->isCurrentlyOnline() ? 'Online' : 'Offline' }}</small>
                </div>
            </div>

            <div id="messages-container" class="messages-container border rounded p-3">
                @foreach ($messages as $msg)
                    @php
                        $isSender = $msg->sender_id == auth()->id();
                        $messageClass = $isSender ? 'sent' : 'received';
                    @endphp
                    <div class="message-bubble {{ $messageClass }} mb-3">
                        <div>{{ $msg->message }}</div>
                        <small class="message-time">{{ $msg->created_at->diffForHumans() }}</small>
                    </div>
                @endforeach
            </div>

            <div id="message-form" class="mt-0">
                <form method="POST" action="{{ route('messages.send') }}" id="send-message-form">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                    <div class="send-form">
                        <div class="input-group">
                            <textarea name="message" id="message-input" class="form-control" placeholder="Type your message..." rows="1" required></textarea>
                            <button type="submit" class="btn btn-primary" id="send-button">Send</button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="alert alert-danger">
                Error: Landlord not found.
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
    $(document).ready(function() {
        // Auto-resize textarea
        $('#message-input').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Auto-submit on Enter, new line on Shift+Enter
        $('#message-input').on('keydown', function(e) {
            if (e.key === 'Enter') {
                if (e.shiftKey) {
                    // Shift+Enter: allow new line
                    return true;
                } else {
                    // Enter: submit message
                    e.preventDefault();
                    sendMessage();
                    return false;
                }
            }
        });

        // Handle send message
        $('#send-message-form').on('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });

        function sendMessage() {
            const messageText = $('#message-input').val().trim();
            if (!messageText) return;

            const $sendButton = $('#send-button');
            const originalHtml = $sendButton.html();
            $sendButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            const formData = new FormData(document.getElementById('send-message-form'));

            $.ajax({
                url: '{{ route("messages.send") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#message-input').val('').height('auto');

                    // Add new message to container without reloading
                    const $message = $(`
                        <div class="message-bubble sent mb-3">
                            <div>${escapeHtml(messageText)}</div>
                            <small class="message-time">now</small>
                        </div>
                    `);
                    $('#messages-container').append($message);

                    // Scroll to bottom to show new message
                    const $container = $('#messages-container');
                    $container.scrollTop($container[0].scrollHeight);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to send message:', error);
                    alert('Failed to send message. Please try again.');
                },
                complete: function() {
                    $sendButton.prop('disabled', false).html(originalHtml);
                }
            });
        }

        function formatTime(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;

            if (diff < 60000) return 'now';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
            if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
            return date.toLocaleDateString();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    });
    </script>
    @endpush
@endsection
