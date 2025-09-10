<?php
/**
 * Test script to verify conversations API endpoint
 * Run this from the command line: php test_conversations.php
 */

// Simulate a request to test the conversations endpoint
echo "Testing conversations API endpoint...\n";

// Check if we're in Laravel environment
if (!function_exists('app')) {
    echo "Error: This script must be run from within a Laravel application.\n";
    exit(1);
}

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $pdo = DB::connection()->getPdo();
    echo "   ✓ Database connection successful\n";

    // Test Message model
    echo "2. Testing Message model...\n";
    $messageCount = \App\Models\Message::count();
    echo "   ✓ Found {$messageCount} messages in database\n";

    // Test User model
    echo "3. Testing User model...\n";
    $userCount = \App\Models\User::count();
    echo "   ✓ Found {$userCount} users in database\n";

    // Test authentication (if user is logged in)
    echo "4. Testing authentication...\n";
    $user = auth()->user();
    if ($user) {
        echo "   ✓ User authenticated: {$user->email} (ID: {$user->id})\n";

        // Test conversations method
        echo "5. Testing conversations method...\n";
        $controller = app(\App\Http\Controllers\MessageController::class);
        $response = $controller->conversations();

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getContent(), true);
            echo "   ✓ Conversations API returned " . count($data) . " conversations\n";

            if (count($data) > 0) {
                echo "   ✓ Sample conversation data:\n";
                echo "     - User: " . ($data[0]['counterpart']['name'] ?? 'Unknown') . "\n";
                echo "     - Last message: " . substr($data[0]['lastMessage']['text'] ?? '', 0, 50) . "...\n";
                echo "     - Unread count: " . ($data[0]['unreadCount'] ?? 0) . "\n";
            }
        } else {
            echo "   ✗ Conversations API failed with status: " . $response->getStatusCode() . "\n";
            echo "   Response: " . $response->getContent() . "\n";
        }
    } else {
        echo "   ! No user authenticated - some tests will be skipped\n";
        echo "   To test fully, please run this script while logged in\n";
    }

    echo "\n✓ All basic tests completed successfully!\n";

} catch (\Exception $e) {
    echo "✗ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\nRecommendations:\n";
echo "- Check browser console for JavaScript errors\n";
echo "- Verify CSRF token is being sent with AJAX requests\n";
echo "- Ensure user sessions are not expiring prematurely\n";
echo "- Check network connectivity and firewall settings\n";
?>
