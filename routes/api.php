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