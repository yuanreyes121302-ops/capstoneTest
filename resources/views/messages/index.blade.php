@extends('layouts.app')

@section('content')
@push('styles')
    <link href="{{ asset('css/messages.css') }}" rel="stylesheet">
@endpush
<div class="container-fluid messaging-container">
    <div class="row h-100">
        <!-- Mobile Toggle Button -->
        <div class="d-md-none p-2">
            <button class="btn btn-primary" id="sidebar-toggle">
                <i class="fas fa-bars"></i> Conversations
            </button>
        </div>

        <!-- Inbox Sidebar -->
        <div class="col-md-4 inbox-sidebar d-md-block" id="inbox-sidebar">
            <div class="p-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <a href="{{ auth()->user()->role === 'landlord' ? route('landlord.dashboard') : route('tenant.profile') }}" class="btn btn-light" style="border-radius: 8px; color: #2c3e50; background: white; border: 1px solid #e1e8ed; transition: all 0.3s ease; font-weight: 500;">
                        ← Back
                    </a>
                    <h4 class="mb-0">Messages</h4>
                </div>
                <div id="conversations-list">
                    <!-- Conversations will be loaded here via AJAX -->
                    <div class="text-center text-white" id="loading-conversations">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="sr-only">Loading...</span>
                        </div> Loading conversations...
                    </div>
                </div>
                <div id="conversations-error" class="alert alert-danger d-none mt-3">
                    Failed to load conversations.
                    <button class="btn btn-sm btn-outline-danger ms-2" id="retry-conversations">Retry</button>
                </div>
            </div>
        </div>

        <!-- Message Panel -->
        <div class="col-md-8 col-12 message-panel" id="message-panel">
            <div class="p-3 h-100 d-flex flex-column">
                <div id="conversation-header" class="d-none">
                    <div class="d-flex align-items-center justify-content-between">
                        <!-- Left side: Avatar and Name -->
                        <div class="d-flex align-items-center">
                            <div class="position-relative me-2">
                                <img id="conversation-avatar" src="" alt="Avatar" class="rounded-circle">
                                <span id="conversation-status" class="conversation-status position-absolute bottom-0 end-0 bg-success border border-white rounded-circle"
                                      style="width: 15px; height: 15px;" title="Online">
                                </span>
                            </div>
                            <div class="ms-2">
                                <h6 id="conversation-name" class="mb-0" style="font-size: 0.9rem;"></h6>
                                <small id="conversation-role" class="text-muted" style="font-size: 0.75rem;"></small>
                            </div>
                        </div>

                        <!-- Right side: Settings Menu -->
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" type="button" id="conversation-settings" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v" style="font-size: 1rem;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="conversation-settings">
                                <li><a class="dropdown-item" href="#" id="view-profile">View Profile</a></li>
                                <li><a class="dropdown-item" href="#" id="block-user">Block User</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" id="report-user">Report User</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="messages-container" class="messages-container border rounded p-3">
                    <div class="no-conversation" id="no-conversation-selected">
                        <h5>Select a conversation</h5>
                        <p>Choose a conversation from the sidebar to start messaging</p>
                    </div>
                    <div class="text-center text-muted d-none" id="loading-messages">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading messages...</span>
                        </div>
                    </div>
                </div>

                <div id="message-form" class="mt-0">
                    <form id="send-message-form">
                        @csrf
                        <input type="hidden" name="receiver_id" id="receiver-id">
                        <div class="send-form">
                            <div class="input-group">
                                <textarea name="message" id="message-input" class="form-control" placeholder="Type your message..." rows="1" required></textarea>
                                <button type="submit" class="btn btn-primary" id="send-button">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentConversationId = null;
    const selectedUserId = {{ $selectedUserId ?? 'null' }};
    let isSidebarVisible = true;

    // Mobile sidebar toggle
    $('#sidebar-toggle').on('click', function() {
        isSidebarVisible = !isSidebarVisible;
        $('#inbox-sidebar').toggleClass('d-none d-md-block');
        $(this).find('i').toggleClass('fa-bars fa-times');
    });

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

    // Load conversations
    loadConversations();

    // Auto-select conversation if userId is provided
    if (selectedUserId) {
        selectConversation(selectedUserId);
    }

    // Handle conversation click
    $(document).on('click', '.conversation-item', function() {
        const counterpartId = $(this).data('counterpart-id');
        selectConversation(counterpartId);

        // Hide sidebar on mobile after selection
        if ($(window).width() < 768) {
            $('#inbox-sidebar').addClass('d-none');
            $('#sidebar-toggle').find('i').removeClass('fa-times').addClass('fa-bars');
            isSidebarVisible = false;
        }
    });

    // Handle send message
    $('#send-message-form').on('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    // Retry loading conversations
    $(document).on('click', '#retry-conversations', function() {
        loadConversations();
    });

    // Handle settings menu items
    $(document).on('click', '#view-profile', function(e) {
        e.preventDefault();
        if (currentConversationId) {
            window.location.href = `/user/${currentConversationId}`;
        }
    });

    $(document).on('click', '#block-user', function(e) {
        e.preventDefault();
        if (currentConversationId && confirm('Are you sure you want to block this user?')) {
            // Implement block user functionality
            alert('Block user functionality would be implemented here');
        }
    });

    $(document).on('click', '#report-user', function(e) {
        e.preventDefault();
        if (currentConversationId && confirm('Are you sure you want to report this user?')) {
            // Implement report user functionality
            alert('Report user functionality would be implemented here');
        }
    });



    // Listen for real-time messages
    if (window.Echo) {
        window.Echo.private('chat.' + {{ auth()->id() }})
            .listen('.message.sent', (e) => {
                // Update unread counts
                loadConversations();

                // If message is for current conversation, append it
                if (currentConversationId && (e.sender_id == currentConversationId || e.receiver_id == currentConversationId)) {
                    appendMessage({
                        id: e.message.id,
                        senderId: e.sender_id,
                        text: e.message.message,
                        createdAt: e.message.created_at,
                        isSender: e.sender_id == {{ auth()->id() }}
                    });
                }
            });
    }

    function loadConversations(retryCount = 0) {
        const maxRetries = 3;
        const retryDelay = 2000; // 2 seconds

        $('#loading-conversations').show();
        $('#conversations-error').addClass('d-none');

        // Check if user is authenticated
        if (!{{ auth()->check() ? 'true' : 'false' }}) {
            console.error('User not authenticated');
            $('#loading-conversations').hide();
            $('#conversations-error').html('You are not logged in. Please <a href="/login">login</a> to continue.');
            $('#conversations-error').removeClass('d-none');
            return;
        }

        console.log(`Loading conversations... (attempt ${retryCount + 1}/${maxRetries + 1})`);
        console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
        console.log('Auth check result:', {{ auth()->check() ? 'true' : 'false' }});

        $.ajax({
            url: '/conversations',
            type: 'GET',
            timeout: 10000, // 10 second timeout
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function(xhr) {
                console.log('Sending request with headers:', xhr.getAllResponseHeaders ? 'Headers available' : 'Headers not accessible');
            }
        })
            .done(function(data) {
                console.log('Conversations loaded successfully:', data);
                $('#loading-conversations').hide();
                renderConversations(data);
            })
            .fail(function(xhr, status, error) {
                console.error('Failed to load conversations:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error,
                    retryCount: retryCount
                });

                let errorMessage = 'Failed to load conversations.';
                let shouldRetry = false;

                // Handle different error types
                if (xhr.status === 0) {
                    // Network error
                    errorMessage = 'Network connection failed. Please check your internet connection.';
                    shouldRetry = true;
                } else if (xhr.status === 401) {
                    // Authentication failed
                    console.error('Authentication failed - user session may have expired');
                    errorMessage = 'Your session has expired. Please <a href="/login">login again</a>.';
                } else if (xhr.status === 403) {
                    // Access forbidden
                    console.error('Access forbidden - user may not have permission');
                    errorMessage = 'Access denied. Please check your account permissions.';
                } else if (xhr.status === 419) {
                    // CSRF token mismatch
                    console.error('CSRF token mismatch');
                    errorMessage = 'Security token expired. Please refresh the page.';
                } else if (xhr.status === 500) {
                    // Server error
                    console.error('Server error occurred');
                    errorMessage = 'Server error occurred. Please try again later.';
                    shouldRetry = true;
                } else if (status === 'timeout') {
                    // Request timeout
                    console.error('Request timed out');
                    errorMessage = 'Request timed out. Please try again.';
                    shouldRetry = true;
                }

                // Auto-retry for certain errors
                if (shouldRetry && retryCount < maxRetries) {
                    console.log(`Retrying in ${retryDelay}ms...`);
                    $('#loading-conversations').hide();
                    $('#conversations-error').html(`${errorMessage} Retrying in ${retryDelay/1000} seconds... (${retryCount + 1}/${maxRetries})`);
                    $('#conversations-error').removeClass('d-none');

                    setTimeout(function() {
                        loadConversations(retryCount + 1);
                    }, retryDelay);
                    return;
                }

                // Show final error message
                $('#loading-conversations').hide();
                $('#conversations-error').html(errorMessage);
                $('#conversations-error').removeClass('d-none');
            });
    }

    function renderConversations(conversations) {
        const $list = $('#conversations-list');
        $list.empty();

        if (conversations.length === 0) {
            $list.html('<div class="text-center text-white mt-4"><i class="fas fa-comments fa-2x mb-2"></i><p>No conversations yet</p></div>');
            return;
        }

        conversations.forEach(function(conv) {
            const isActive = currentConversationId == conv.counterpart.id;
            const onlineStatus = conv.counterpart.is_online ? 'online' : 'offline';
            const statusClass = conv.counterpart.is_online ? 'bg-success' : 'bg-secondary';
            const $item = $(`
                <div class="conversation-item ${isActive ? 'active' : ''}" data-counterpart-id="${conv.counterpart.id}">
                    <div class="d-flex align-items-center">
                        <div class="position-relative me-3">
                            <img src="${conv.counterpart.avatar}" alt="Avatar" class="rounded-circle">
                            <span class="conversation-status position-absolute bottom-0 end-0 ${statusClass} border border-white rounded-circle"
                                  style="width: 15px; height: 15px;" title="${onlineStatus}">
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <strong class="text-truncate">${conv.counterpart.name}</strong>
                                <small class="text-muted ms-2">${formatTime(conv.lastMessage.createdAt)}</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-end">
                                <small class="text-muted text-truncate flex-grow-1 me-2">${truncateText(conv.lastMessage.text, 30)}</small>
                                ${conv.unreadCount > 0 ? `<span class="badge bg-danger">${conv.unreadCount}</span>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `);
            $list.append($item);
        });
    }

    function selectConversation(counterpartId) {
        currentConversationId = counterpartId;
        $('.conversation-item').removeClass('active');
        $(`.conversation-item[data-counterpart-id="${counterpartId}"]`).addClass('active');

        $('#loading-messages').removeClass('d-none');
        $('#messages-container .no-conversation').addClass('d-none');

        loadConversationMessages(counterpartId, 0); // Start with 0 retries
    }

    function loadConversationMessages(counterpartId, retryCount) {
        const maxRetries = 3;

        console.log(`Loading messages for conversation ${counterpartId}, attempt ${retryCount + 1}`);

        $.ajax({
            url: `/conversations/${counterpartId}/messages?all=true`,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
            .done(function(data) {
                console.log('Messages loaded successfully:', data);
                $('#loading-messages').addClass('d-none');
                renderConversation(data);
            })
            .fail(function(xhr, status, error) {
                console.error('Failed to load conversation messages:', {
                    counterpartId: counterpartId,
                    retryCount: retryCount,
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });

                if (retryCount < maxRetries) {
                    $('#loading-messages').addClass('d-none');
                    $('#messages-container').html(`
                        <div class="alert alert-warning">
                            Failed to load messages. Retrying... (${retryCount + 1}/${maxRetries})
                            <button class="btn btn-sm btn-outline-warning ms-2" onclick="loadConversationMessages(${counterpartId}, ${maxRetries})">Skip Retry</button>
                        </div>
                    `);

                    // Auto-retry after 2 seconds
                    setTimeout(function() {
                        loadConversationMessages(counterpartId, retryCount + 1);
                    }, 2000);
                } else {
                    $('#loading-messages').addClass('d-none');
                    $('#messages-container').html(`
                        <div class="alert alert-danger">
                            Failed to load messages after ${maxRetries} attempts.
                            <button class="btn btn-sm btn-outline-danger ms-2" onclick="loadConversationMessages(${counterpartId}, 0)">Retry</button>
                            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="refreshConversations()">Refresh All</button>
                        </div>
                    `);
                }
            });
    }

    function refreshConversations() {
        loadConversations();
        $('#messages-container').html(`
            <div class="no-conversation" id="no-conversation-selected">
                <h5>Select a conversation</h5>
                <p>Choose a conversation from the sidebar to start messaging</p>
            </div>
        `);
        currentConversationId = null;
        $('.conversation-item').removeClass('active');
    }

    function renderConversation(data) {
        console.log('Rendering conversation:', data);

        // Get counterpart user data from the first message
        let counterpart = {
            id: currentConversationId,
            name: 'Loading...',
            avatar: '/images/default-avatar.png',
            role: '',
            contact_number: '',
            is_online: false,
            last_seen: null
        };

        if (data.messages && data.messages.length > 0) {
            const firstMsg = data.messages[0];
            const counterpartId = firstMsg.sender_id == {{ auth()->id() }} ? firstMsg.receiver_id : firstMsg.sender_id;

            console.log('Fetching user data for counterpart:', counterpartId);

            // Fetch user data for the counterpart
            $.get(`/api/user/${counterpartId}`)
                .done(function(userData) {
                    console.log('User data received:', userData);

                    // Build full name with fallback
                    let fullName = 'User';
                    if (userData.full_name && userData.full_name.trim()) {
                        fullName = userData.full_name.trim();
                    } else if (userData.first_name || userData.last_name) {
                        fullName = (userData.first_name || '') + ' ' + (userData.last_name || '');
                        fullName = fullName.trim();
                    }
                    if (!fullName || fullName === 'User') {
                        fullName = 'User ' + userData.id;
                    }

                    counterpart = {
                        id: userData.id,
                        name: fullName,
                        avatar: userData.profile_image || '/images/default-avatar.png',
                        role: userData.role || '',
                        contact_number: userData.contact_number || '',
                        is_online: userData.is_online || false,
                        last_seen: userData.last_seen || null
                    };
                    updateConversationHeader(counterpart);
                    setupMessageComposer(counterpart);
                })
                .fail(function(xhr, status, error) {
                    console.error('Failed to fetch user data:', {
                        counterpartId: counterpartId,
                        status: xhr.status,
                        responseText: xhr.responseText,
                        error: error
                    });
                    // Fallback to basic info - try to get name from conversation data
                    counterpart.name = 'Unknown User'; // Better fallback than "User 3"
                    counterpart.avatar = '{{ asset("images/default-avatar.png") }}'; // Use Laravel asset helper
                    counterpart.role = 'user';
                    counterpart.contact_number = '';
                    counterpart.is_online = false;

                    // Try to get name from conversation list if available
                    const $activeConversation = $(`.conversation-item[data-counterpart-id="${counterpartId}"]`);
                    if ($activeConversation.length > 0) {
                        const conversationName = $activeConversation.find('strong').text();
                        if (conversationName && conversationName !== 'Loading...' && !conversationName.includes('User ')) {
                            counterpart.name = conversationName;
                        }
                    }
                    updateConversationHeader(counterpart);
                    setupMessageComposer(counterpart);
                });
        } else {
            counterpart.name = 'Unknown User';
            counterpart.avatar = '{{ asset("images/default-avatar.png") }}';
            counterpart.role = 'user';
            counterpart.contact_number = '';
            counterpart.is_online = false;

            // Try to get name from conversation list if available
            const $activeConversation = $(`.conversation-item[data-counterpart-id="${currentConversationId}"]`);
            if ($activeConversation.length > 0) {
                const conversationName = $activeConversation.find('strong').text();
                if (conversationName && conversationName !== 'Loading...' && !conversationName.includes('User ')) {
                    counterpart.name = conversationName;
                }
            }
            updateConversationHeader(counterpart);
            setupMessageComposer(counterpart);
        }

        $('#conversation-header').removeClass('d-none');
        $('#receiver-id').val(currentConversationId);
        $('#no-conversation-selected').addClass('d-none');

        const $container = $('#messages-container');
        $container.empty();

        if (data.messages && data.messages.length > 0) {
            data.messages.forEach(function(msg) {
                appendMessage(msg);
            });
        } else {
            $container.html('<div class="text-center text-muted mt-4"><i class="fas fa-comments fa-2x mb-2"></i><p>No messages yet. Start the conversation!</p></div>');
        }

        $container.scrollTop($container[0].scrollHeight);
    }

    function setupMessageComposer(counterpart) {
        const currentUserRole = '{{ auth()->user()->role }}';

        // Show composer for all users by default
        // Both tenants and landlords can send messages
        $('#message-form').removeClass('d-none');

        // Add role-based styling or messaging if needed
        if (currentUserRole === 'tenant' && counterpart.role === 'landlord') {
            $('#message-input').attr('placeholder', 'Message the landlord...');
        } else if (currentUserRole === 'landlord' && counterpart.role === 'tenant') {
            $('#message-input').attr('placeholder', 'Reply to tenant...');
        } else {
            $('#message-input').attr('placeholder', 'Type your message...');
        }
    }

    function updateConversationHeader(counterpart) {
        $('#conversation-avatar').attr('src', counterpart.avatar);
        $('#conversation-name').text(counterpart.name);

        // Update online status indicator
        const statusClass = counterpart.is_online ? 'bg-success' : 'bg-secondary';
        const statusTitle = counterpart.is_online ? 'Online' : 'Offline';
        $('#conversation-status').removeClass('bg-success bg-secondary').addClass(statusClass).attr('title', statusTitle);

        // Hide contact info and role for cleaner display
        $('#conversation-contact').hide();
        $('#conversation-role').hide();
    }

    function ucfirst(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    }

    function sendMessage() {
        const messageText = $('#message-input').val().trim();
        if (!messageText) return;

        const $sendButton = $('#send-button');
        const originalHtml = $sendButton.html();
        $sendButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        const formData = new FormData(document.getElementById('send-message-form'));

        $.ajax({
            url: '/messages/send',
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

                loadConversations(); // Refresh inbox to update unread counts
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

    function appendMessage(msg) {
        const isSender = msg.sender_id == {{ auth()->id() }} || msg.isSender;
        const messageClass = isSender ? 'sent' : 'received';
        const $message = $(`
            <div class="message-bubble ${messageClass} mb-3">
                <div>${escapeHtml(msg.message || msg.text)}</div>
                <small class="message-time">${formatTime(msg.created_at || msg.createdAt)}</small>
            </div>
        `);
        $('#messages-container').append($message);
        $('#messages-container').scrollTop($('#messages-container')[0].scrollHeight);
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

    function truncateText(text, maxLength) {
        if (!text) return '';
        return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Handle window resize for responsive behavior
    $(window).on('resize', function() {
        if ($(window).width() >= 768) {
            $('#inbox-sidebar').removeClass('d-none').addClass('d-md-block');
            isSidebarVisible = true;
        } else {
            if (!isSidebarVisible) {
                $('#inbox-sidebar').addClass('d-none');
            }
        }
    });
});
</script>
@endpush
