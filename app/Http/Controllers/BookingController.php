<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{

    // Store a new booking request
    public function store(Request $request, Property $property)
    {
        $user = auth()->user();

        // Validate input
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'terms' => 'nullable|string',
        ]);

        // Check if the selected room belongs to this property
        $room = \App\Models\Room::where('id', $request->room_id)
            ->where('property_id', $property->id)
            ->first();

        if (!$room) {
            return redirect()->back()->with('error', 'Selected room does not belong to this property.');
        }

        // Check for existing booking
        $existingBooking = \App\Models\Booking::where('tenant_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->where(function($q) {
                $q->whereNull('finalized_at')->orWhereNotNull('finalized_at');
            })
            ->first();

        if ($existingBooking) {
            return redirect()->back()->with('error', 'You already have an ongoing or finalized booking.');
        }

        // Save booking
        \App\Models\Booking::create([
            'tenant_id' => $user->id,
            'landlord_id' => $property->user_id,
            'property_id' => $property->id,
            'room_id' => $room->id,
            'terms' => $request->terms,
            'status' => 'pending',
        ]);

        return redirect()->route('bookings.tenant.index')->with('success', 'Booking request sent.');
    }

    public function landlordIndex()
    {
        $bookings = Booking::with(['tenant', 'property', 'room'])
                    ->where('landlord_id', Auth::id())
                    ->where('status', 'pending') // ✅ Only pending
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('bookings.landlord_index', compact('bookings'));
    }

    public function accept(Request $request, Booking $booking)
    {
        $this->authorizeAction($booking);

        $request->validate([
            'landlord_terms' => 'required|string',
        ]);

        $booking->update([
            'status' => 'accepted',
            'landlord_terms' => $request->landlord_terms,
            'signed_by_landlord' => true,
        ]);

        return back()->with('success', 'Booking accepted with your terms.');
    }

    public function decline(Booking $booking)
    {
        $this->authorizeAction($booking);

        $booking->update(['status' => 'declined']);

        return back()->with('success', 'Booking declined.');
    }

    private function authorizeAction($booking)
    {
        if ($booking->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function tenantIndex()
    {
        $bookings = Booking::with(['property', 'landlord'])
                    ->where('tenant_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('bookings.tenant_index', compact('bookings'));
    }

    public function showFinalizePage(Booking $booking)
    {
        $this->authorize('view', $booking);

        if (!$booking->signed_by_landlord || $booking->signed_by_tenant) {
            return redirect()->route('bookings.tenant.index')->with('error', 'This contract cannot be finalized.');
        }

        return view('bookings.finalize', compact('booking'));
    }

    public function finalize(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        if (!$booking->signed_by_landlord || $booking->signed_by_tenant) {
            return redirect()->route('bookings.tenant.index')->with('error', 'Invalid finalization attempt.');
        }

        $request->validate([
            'agree' => 'required|accepted',
        ]);

        $booking->update([
            'signed_by_tenant' => true,
            'finalized_at' => now(),
        ]);

        return redirect()->route('bookings.tenant.index')->with('success', 'Booking finalized. Contract simulated.');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->tenant_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be cancelled.');
        }

        $booking->delete();

        return redirect()->back()->with('success', 'Booking request cancelled.');
    }

    public function landlordContracts()
    {
        $landlordId = auth()->id();

        $bookings = \App\Models\Booking::with(['tenant', 'property','room'])
            ->where('landlord_id', $landlordId)
            ->whereNotNull('finalized_at')
            ->orderByDesc('finalized_at')
            ->get();

        return view('landlord.contracts.index', compact('bookings'));
    }


}
