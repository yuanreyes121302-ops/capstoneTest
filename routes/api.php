<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/bfs-route', function (Request $request) {
    $start = [$request->start_lat, $request->start_lng];
    $end = [$request->end_lat, $request->end_lng];

    // Simulate Best-First Search using direct heuristic (straight-line)
    $path = simulateBestFirstSearch($start, $end);

    return response()->json(['path' => $path]);
});

// Log back navigation from map view
Route::post('/log-back-navigation', function (Request $request) {
    \Illuminate\Support\Facades\Log::info('Back navigation from map view', [
        'tenant_id' => $request->tenant_id,
        'property_id' => $request->property_id,
        'timestamp' => now()
    ]);

    return response()->json(['status' => 'logged']);
});


