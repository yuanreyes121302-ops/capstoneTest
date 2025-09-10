<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LandlordUpdatedProfileController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/profile/update-picture', [LandlordUpdatedProfileController::class, 'updateProfilePicture'])
     ->name('profile.updatePicture');

Route::get('/', function () {
    return view('index');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'is_approved'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect('/admin/users/all');
        } elseif (auth()->user()->role === 'landlord') {
            return redirect('/landlord/profile');
        } elseif (auth()->user()->role === 'tenant') {
            return redirect('/tenant/profile');
        }
        return redirect('/');
    })->name('dashboard');
});

Route::get('/not-approved', function () {
    return view('auth.not-approved');
})->name('not.approved');

Route::middleware(['auth', 'is_approved', 'admin'])->group(function () {
    Route::get('/admin/users', [UserApprovalController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/{user}/approve', [UserApprovalController::class, 'approve'])->name('admin.users.approve');
    Route::post('/admin/users/{user}/deny', [UserApprovalController::class, 'deny'])->name('admin.users.deny');

    Route::get('/admin/users/all', [UserApprovalController::class, 'allUsers'])->name('admin.users.all');
    Route::delete('/admin/users/{user}', [UserApprovalController::class, 'destroy'])->name('admin.users.delete');

    Route::middleware(['auth', 'is_approved', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/add', [App\Http\Controllers\AdminController::class, 'showAddAdminForm'])->name('add.form');
    Route::post('/add', [App\Http\Controllers\AdminController::class, 'storeNewAdmin'])->name('add.store');
});
});

Route::middleware(['auth', 'is_approved', 'role:landlord'])->group(function () {
    Route::get('/landlord/profile', [App\Http\Controllers\LandlordController::class, 'showProfile'])->name('landlord.profile');
    Route::post('/landlord/profile/update', [App\Http\Controllers\LandlordController::class, 'updateProfile'])->name('landlord.profile.update');
    Route::get('/landlord/properties/{property}/rooms', [RoomController::class, 'index'])->name('landlord.rooms.index');
Route::get('/landlord/properties/{property}/rooms/create', [RoomController::class, 'create'])->name('landlord.rooms.create');
Route::post('/landlord/properties/{property}/rooms', [RoomController::class, 'store'])->name('landlord.rooms.store');
Route::get('/landlord/rooms/{room}/edit', [RoomController::class, 'edit'])->name('landlord.rooms.edit');
Route::put('/landlord/rooms/{room}', [RoomController::class, 'update'])->name('landlord.rooms.update');
Route::delete('/landlord/rooms/images/{image}', [RoomController::class, 'deleteImage'])->name('landlord.rooms.images.delete');
Route::delete('/landlord/rooms/{room}', [RoomController::class, 'destroy'])->name('landlord.rooms.destroy');


});

Route::middleware(['auth', 'is_approved', 'role:landlord'])->prefix('landlord')->name('landlord.')->group(function () {
    Route::resource('properties', App\Http\Controllers\PropertyController::class);
});

Route::resource('properties', PropertyController::class);
Route::delete('/property-images/{image}', [PropertyController::class, 'deleteImage'])->name('property-images.destroy');


Route::middleware(['auth', 'is_approved', 'role:tenant'])->group(function () {
    Route::get('/tenant/profile', [TenantController::class, 'showProfile'])->name('tenant.profile');
    Route::post('/tenant/profile', [TenantController::class, 'updateProfile'])->name('tenant.profile.update');
    Route::get('/properties', [PropertyController::class, 'indexForTenant'])->name('tenant.properties.index');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('tenant.properties.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/fetch-conversations', [MessageController::class, 'fetchConversations'])->name('messages.fetchConversations');
    Route::get('/messages/fetch-messages/{userId}', [MessageController::class, 'fetchMessages'])->name('messages.fetchMessages');
    Route::get('/messages/{userId}', [MessageController::class, 'showConversation'])->name('messages.show');
    Route::post('/messages/send', [MessageController::class, 'send'])->name('messages.send');
    Route::post('/messages/mark-read/{userId}', [MessageController::class, 'markAsRead'])->name('messages.markAsRead');
    Route::get('/inbox', [MessageController::class, 'index'])->name('messages.inbox');

    // New API endpoints
    Route::get('/conversations', [MessageController::class, 'conversations']);
    Route::get('/conversations/{counterpartId}/messages', [MessageController::class, 'thread']);
    Route::post('/conversations/{counterpartId}/read', [MessageController::class, 'markAsRead']);
    Route::get('/api/user/{userId}', [MessageController::class, 'getUserData']);

    // CSRF token endpoint for AJAX requests
    Route::get('/csrf-token', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    });
});

Route::middleware(['auth'])->group(function () {
    Route::post('/bookings/{property}', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/landlord/bookings', [BookingController::class, 'landlordIndex'])->name('bookings.landlord.index');
    Route::post('/landlord/bookings/{booking}/accept', [BookingController::class, 'accept'])->name('bookings.accept');
    Route::post('/landlord/bookings/{booking}/decline', [BookingController::class, 'decline'])->name('bookings.decline');
    Route::get('/tenant/bookings', [BookingController::class, 'tenantIndex'])->name('bookings.tenant.index');
    Route::get('/bookings/{booking}/finalize', [BookingController::class, 'showFinalizePage'])->name('bookings.finalize');
    Route::post('/bookings/{booking}/finalize', [BookingController::class, 'finalize'])->name('bookings.finalize.submit');
    Route::delete('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/landlord/contracts', [BookingController::class, 'landlordContracts'])->name('landlord.contracts');
});

Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::patch('/landlord/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('landlord.reviews.reply');
Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

Route::get('/tenant/properties/{id}/map', [App\Http\Controllers\PropertyController::class, 'viewMap'])
    ->name('tenant.properties.map');

