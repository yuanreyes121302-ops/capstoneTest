<?php

namespace App\Http\Controllers;
use App\Models\Review;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
   
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // Prevent duplicate reviews
        $exists = Review::where('tenant_id', auth()->id())
            ->where('room_id', $request->room_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already reviewed this room.');
        }

        Review::create([
            'tenant_id' => auth()->id(),
            'room_id' => $request->room_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Review submitted successfully.');
    }

    public function reply(Request $request, Review $review)
    {
        // Check that the landlord owns the room
        if ($review->room->property->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

        $review->reply = $request->reply;
        $review->save();

        return back()->with('success', 'Reply submitted successfully.');
    }

    public function edit(Review $review)
    {
        if ($review->tenant_id !== auth()->id()) {
            abort(403);
        }

        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        if ($review->tenant_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('bookings.tenant.index')->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review)
    {
        if ($review->tenant_id !== auth()->id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }


}
