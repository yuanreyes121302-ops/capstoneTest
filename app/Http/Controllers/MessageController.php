<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{

    public function index(Request $request)
    {
        $selectedUserId = $request->get('userId');
        return view('messages.index', compact('selectedUserId'));
    }

    public function inbox()
    {
        $userId = auth()->id();

        // Get latest messages grouped by conversation
        $latestMessages = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->latest()
            ->get()
            ->groupBy(function ($message) use ($userId) {
                return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
            });

        return view('messages.inbox', ['conversations' => $latestMessages]);

    }


    public function showConversation($userId)
    {
        $user = auth()->user();

        $otherUser = User::findOrFail($userId); // make sure this is NOT null

        $messages = Message::where(function ($query) use ($user, $userId) {
            $query->where('sender_id', $user->id)->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($user, $userId) {
            $query->where('sender_id', $userId)->where('receiver_id', $user->id);
        })->orderBy('created_at')->get();

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.conversation', compact('messages', 'otherUser'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        // Broadcast the message to the receiver only
        broadcast(new \App\Events\MessageSent($message));

        // Always return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->header('Accept') === 'application/json') {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', 'Message sent!');
    }

    // API endpoint to fetch conversations
    public function fetchConversations()
    {
        try {
            $userId = auth()->id();

            \Log::info('Fetching conversations for user:', ['user_id' => $userId]);

            if (!$userId) {
                \Log::error('No authenticated user found');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $conversations = Message::with(['sender', 'receiver'])
                ->where(function ($query) use ($userId) {
                    $query->where('sender_id', $userId)
                        ->orWhere('receiver_id', $userId);
                })
                ->latest()
                ->get()
                ->groupBy(function ($message) use ($userId) {
                    return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
                })
                ->map(function ($messages, $otherUserId) use ($userId) {
                    try {
                        $lastMessage = $messages->first();
                        $otherUser = $lastMessage->sender_id == $userId
                            ? $lastMessage->receiver
                            : $lastMessage->sender;

                        if (!$otherUser) {
                            \Log::warning('Other user not found for message', ['message_id' => $lastMessage->id, 'other_user_id' => $otherUserId]);
                            return null;
                        }

                        return [
                            'user_id' => $otherUser->id,
                            'name' => trim(($otherUser->first_name ?? '') . ' ' . ($otherUser->last_name ?? '')) ?: 'User',
                            'role' => $otherUser->role ?? '',
                            'profile_image' => $otherUser->profile_image ? asset('storage/profile_images/' . $otherUser->profile_image) : asset('default-avatar.png'),
                            'last_message' => \Illuminate\Support\Str::limit($lastMessage->message ?? '', 40),
                            'timestamp' => $lastMessage->created_at->diffForHumans(),
                            'unread' => $messages->where('sender_id', '!=', $userId)->where('read_at', null)->count(),
                        ];
                    } catch (\Exception $e) {
                        \Log::error('Error processing conversation for user ' . $otherUserId, [
                            'error' => $e->getMessage(),
                            'user_id' => $userId
                        ]);
                        return null;
                    }
                })
                ->filter() // Remove null entries
                ->values(); // Re-index array

            \Log::info('Conversations fetched successfully', [
                'user_id' => $userId,
                'count' => count($conversations)
            ]);

            return response()->json($conversations);

        } catch (\Exception $e) {
            \Log::error('Error fetching conversations', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Failed to load conversations'], 500);
        }
    }

    // API endpoint to fetch messages for a conversation
    public function fetchMessages($userId)
    {
        $user = auth()->user();

        // Log for debugging
        \Log::info('Fetching messages', [
            'current_user' => $user->id,
            'target_user' => $userId,
            'route_param' => request()->route('userId')
        ]);

        $messages = Message::where(function ($query) use ($user, $userId) {
            $query->where('sender_id', $user->id)->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($user, $userId) {
            $query->where('sender_id', $userId)->where('receiver_id', $user->id);
        })->orderBy('created_at')->get();

        // Mark messages as read
        $updated = Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $response = [
            'messages' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'created_at' => $msg->created_at->diffForHumans(),
                    'is_sender' => $msg->sender_id == auth()->id(),
                    'read_at' => $msg->read_at,
                ];
            }),
            'marked_as_read' => $updated,
            'debug' => [
                'current_user' => $user->id,
                'target_user' => $userId,
                'message_count' => $messages->count()
            ]
        ];

        \Log::info('Messages response', $response['debug']);

        return response()->json($response);
    }

    // Mark messages as read
    public function markAsRead($userId)
    {
        $user = auth()->user();

        // Mark messages from the specified user as read
        $updatedCount = Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'marked_as_read' => $updatedCount
        ]);
    }

    // Load conversations (for left list) - matches API contract
    public function conversations()
    {
        try {
            $userId = auth()->id();

            \Log::info('Loading conversations for user', [
                'user_id' => $userId,
                'user_agent' => request()->userAgent(),
                'ip' => request()->ip(),
                'session_id' => session()->getId()
            ]);

            if (!$userId) {
                \Log::warning('No authenticated user found for conversations', [
                    'session_id' => session()->getId(),
                    'headers' => request()->headers->all()
                ]);
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Verify user exists and is approved
            $user = auth()->user();
            if (!$user) {
                \Log::warning('Authenticated user not found in database', ['user_id' => $userId]);
                return response()->json(['error' => 'User not found'], 401);
            }

            if ($user->role !== 'admin' && !$user->is_approved) {
                \Log::warning('User not approved', ['user_id' => $userId, 'role' => $user->role]);
                return response()->json(['error' => 'Account not approved'], 403);
            }

            // Get all unique counterparts with their latest message
            try {
                $counterparts = Message::selectRaw('
                        CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as counterpart_id,
                        MAX(created_at) as last_message_at
                    ', [$userId])
                    ->where(function($q) use ($userId){
                        $q->where('sender_id', $userId)
                          ->orWhere('receiver_id', $userId);
                    })
                    ->groupBy('counterpart_id')
                    ->orderBy('last_message_at', 'desc')
                    ->get();

                \Log::info('Counterparts query successful', [
                    'user_id' => $userId,
                    'counterparts_count' => $counterparts->count()
                ]);
            } catch (\Exception $e) {
                \Log::error('Error in counterparts query', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => 'Failed to load conversations'], 500);
            }

            // Get unread counts for each counterpart
            try {
                $unreadCounts = Message::select('sender_id as counterpart_id', DB::raw('COUNT(*) as unread_count'))
                    ->where('receiver_id', $userId)
                    ->whereNull('read_at')
                    ->groupBy('sender_id')
                    ->pluck('unread_count', 'counterpart_id');

                \Log::info('Unread counts query successful', [
                    'user_id' => $userId,
                    'unread_counts' => $unreadCounts->toArray()
                ]);
            } catch (\Exception $e) {
                \Log::error('Error in unread counts query', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $unreadCounts = collect(); // Fallback to empty collection
            }

            $conversations = [];

            foreach ($counterparts as $counterpart) {
                try {
                    $counterpartId = $counterpart->counterpart_id;

                    // Get the latest message for this counterpart
                    $latestMessage = Message::where(function($q) use ($userId, $counterpartId){
                        $q->where(function($subQ) use ($userId, $counterpartId) {
                            $subQ->where('sender_id', $userId)->where('receiver_id', $counterpartId);
                        })->orWhere(function($subQ) use ($userId, $counterpartId) {
                            $subQ->where('sender_id', $counterpartId)->where('receiver_id', $userId);
                        });
                    })->orderBy('created_at', 'desc')->first();

                    if ($latestMessage) {
                        $user = User::find($counterpartId);
                        if ($user) {
                            // Build full name
                            $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                            if (empty($fullName)) {
                                $fullName = 'User ' . $user->id;
                            }

                            // Handle profile image
                            $profileImage = asset('images/default-avatar.png');
                            if ($user->profile_image) {
                                $imagePath = public_path('storage/profile_images/' . $user->profile_image);
                                if (file_exists($imagePath)) {
                                    $profileImage = asset('storage/profile_images/' . $user->profile_image);
                                }
                            }

                            // Safely get online status
                            $isOnline = false;
                            try {
                                $isOnline = $user->isCurrentlyOnline();
                            } catch (\Exception $e) {
                                \Log::error('Error getting online status', [
                                    'user_id' => $counterpartId,
                                    'error' => $e->getMessage()
                                ]);
                            }

                            $conversations[] = [
                                'counterpart' => [
                                    'id' => $counterpartId,
                                    'name' => $fullName,
                                    'role' => ucfirst($user->role ?? 'user'),
                                    'avatar' => $profileImage,
                                    'is_online' => $isOnline,
                                    'last_seen' => $user->last_seen ? \Carbon\Carbon::parse($user->last_seen)->toISOString() : null,
                                ],
                                'lastMessage' => [
                                    'text' => $latestMessage->message ?? '',
                                    'createdAt' => $latestMessage->created_at->toISOString(),
                                ],
                                'unreadCount' => (int)($unreadCounts[$counterpartId] ?? 0),
                            ];
                        } else {
                            \Log::warning('User not found for counterpart', ['counterpart_id' => $counterpartId]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing counterpart conversation', [
                        'counterpart_id' => $counterpart->counterpart_id ?? 'unknown',
                        'user_id' => $userId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Continue processing other counterparts
                    continue;
                }
            }

            \Log::info('Conversations loaded successfully', [
                'user_id' => $userId,
                'count' => count($conversations),
                'query_time' => now()->toISOString()
            ]);

            return response()->json($conversations);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error loading conversations', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);

            return response()->json(['error' => 'Database error occurred'], 500);
        } catch (\Exception $e) {
            \Log::error('Unexpected error loading conversations', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Failed to load conversations'], 500);
        }
    }

    // Load messages for a conversation (right panel) - matches API contract
    public function thread($counterpartId)
    {
        try {
            $userId = auth()->id();

            \Log::info('Thread method called', [
                'userId' => $userId,
                'counterpartId' => $counterpartId
            ]);

            if (!$userId) {
                \Log::error('No authenticated user found for thread');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!$counterpartId || !is_numeric($counterpartId)) {
                \Log::warning('Invalid counterpart ID provided', ['counterpartId' => $counterpartId]);
                return response()->json(['error' => 'Invalid conversation ID'], 400);
            }

            $messages = Message::where(function($q) use ($userId, $counterpartId){
                    $q->where('sender_id', $userId)->where('receiver_id', $counterpartId);
                })->orWhere(function($q) use ($userId, $counterpartId){
                    $q->where('sender_id', $counterpartId)->where('receiver_id', $userId);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            \Log::info('Messages found', [
                'count' => $messages->count(),
                'userId' => $userId,
                'counterpartId' => $counterpartId
            ]);

            // Mark incoming messages as read
            $updated = Message::where('sender_id', $counterpartId)
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            \Log::info('Messages marked as read', ['updated' => $updated]);

            $formattedMessages = $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'message' => $msg->message ?? '',
                    'created_at' => $msg->created_at->toISOString(),
                    'read_at' => $msg->read_at ? $msg->read_at->toISOString() : null,
                ];
            });

            return response()->json([
                'conversationId' => (string) $counterpartId,
                'messages' => $formattedMessages,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in thread method', [
                'error' => $e->getMessage(),
                'userId' => auth()->id(),
                'counterpartId' => $counterpartId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Failed to load messages'], 500);
        }
    }

    // Mark messages as read for a specific conversation
    public function markConversationRead($counterpartId)
    {
        $userId = auth()->id();

        $updatedCount = Message::where('sender_id', $counterpartId)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([], 204);
    }

    // API endpoint to get user data for conversation header
    public function getUserData($userId)
    {
        try {
            \Log::info('Getting user data', ['userId' => $userId, 'requesting_user' => auth()->id()]);

            if (!$userId || !is_numeric($userId)) {
                \Log::warning('Invalid user ID provided', ['userId' => $userId]);
                return response()->json(['error' => 'Invalid user ID'], 400);
            }

            $user = User::find($userId);

            if (!$user) {
                \Log::warning('User not found', ['userId' => $userId]);
                return response()->json(['error' => 'User not found'], 404);
            }

            // Build full name
            $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            if (empty($fullName)) {
                $fullName = 'User ' . $user->id;
            }

            // Handle profile image
            $profileImage = null;
            if ($user->profile_image) {
                $imagePath = public_path('storage/profile_images/' . $user->profile_image);
                if (file_exists($imagePath)) {
                    $profileImage = asset('storage/profile_images/' . $user->profile_image);
                }
            }

            // Fallback to default avatar if no profile image
            if (!$profileImage) {
                $profileImage = asset('images/default-avatar.png');
            }

            \Log::info('User data retrieved successfully', [
                'user_id' => $user->id,
                'full_name' => $fullName,
                'has_profile_image' => !empty($user->profile_image)
            ]);

            return response()->json([
                'id' => $user->id,
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'full_name' => $fullName,
                'role' => $user->role ?? '',
                'profile_image' => $profileImage,
                'contact_number' => $user->contact_number ?? '',
                'is_online' => $user->isCurrentlyOnline(),
                'last_seen' => $user->last_seen ? \Carbon\Carbon::parse($user->last_seen)->toISOString() : null
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting user data', [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'requesting_user' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Failed to retrieve user data'], 500);
        }
    }


}
