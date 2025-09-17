<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;
use App\Mail\LandlordBookingNotification;
use App\Mail\BookingApproved;
use App\Mail\BookingDeclined;
use App\Models\Notification;

class BookingController extends Controller
{

    // Store a new booking request
    public function store(Request $request, Property $property)
    {
        // DEBUG: Controller entry
        Log::debug('BookingController@store entered', [
            'route' => $request->route() ? $request->route()->getName() : 'unknown',
            'user_id' => auth()->id(),
            'user_role' => auth()->user() ? auth()->user()->role : 'unknown',
            'request_method' => $request->method(),
            'request_keys' => array_keys($request->all()),
            'property_id' => $property->id,
            'timestamp' => now()
        ]);

        $user = auth()->user();

        if (!$property->user_id) {
            return redirect()->back()->with('error', 'This property has no landlord assigned.');
        }

        // Validate input
        $rules = [
            'terms' => 'nullable|string',
            'booking_date' => 'required|date|after:today',
            'booking_time' => 'required|date_format:H:i',
            'tenant_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ];

        if ($property->rooms && $property->rooms->count() > 0) {
            $rules['room_id'] = 'required|exists:rooms,id';
        } else {
            $rules['room_id'] = 'nullable|exists:rooms,id';
        }

        $request->validate($rules);

        // DEBUG: Validation passed
        Log::debug('BookingController@store validation passed', [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
            'timestamp' => now()
        ]);

        // Check if the selected room belongs to this property (if room selected)
        $room = null;
        if ($request->room_id) {
            $room = \App\Models\Room::where('id', $request->room_id)
                ->where('property_id', $property->id)
                ->first();

            if (!$room) {
                // DEBUG: Room not found
                Log::debug('BookingController@store room not found', [
                    'user_id' => auth()->id(),
                    'room_id' => $request->room_id,
                    'property_id' => $property->id,
                    'timestamp' => now()
                ]);
                return redirect()->back()->with('error', 'Selected room does not belong to this property.');
            }
        }

        // Check for existing booking for the same date and time (and room if specified)
        $existingQuery = \App\Models\Booking::where('tenant_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time);

        if ($request->room_id) {
            $existingQuery->where('room_id', $request->room_id);
        }

        $existingBooking = $existingQuery->first();

        if ($existingBooking) {
            // DEBUG: Existing booking found
            Log::debug('BookingController@store existing booking found', [
                'user_id' => auth()->id(),
                'existing_booking_id' => $existingBooking->id,
                'existing_status' => $existingBooking->status,
                'timestamp' => now()
            ]);
            return redirect()->back()->with('error', 'You already have a booking for this date and time' . ($request->room_id ? ' for this room' : '') . '.');
        }

        // Use DB transaction to ensure atomicity
        $booking = DB::transaction(function () use ($user, $property, $room, $request) {
            // DEBUG: Transaction started
            Log::debug('BookingController@store transaction started', [
                'user_id' => auth()->id(),
                'tenant_id' => $user->id,
                'landlord_id' => $property->user_id,
                'property_id' => $property->id,
                'room_id' => $room ? $room->id : null,
                'timestamp' => now()
            ]);

            $booking = \App\Models\Booking::create([
                'tenant_id' => $user->id,
                'landlord_id' => $property->user_id,
                'property_id' => $property->id,
                'room_id' => $room ? $room->id : null,
                'tenant_name' => $request->tenant_name,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'terms' => $request->terms,
                'status' => 'pending',
            ]);

            if (!$booking || !$booking->exists) {
                throw new \Exception('Failed to create booking record');
            }

            // DEBUG: Booking created
            Log::debug('BookingController@store booking created', [
                'booking_id' => $booking->id,
                'booking_attributes' => $booking->toArray(),
                'timestamp' => now()
            ]);

            // Log successful booking creation
            Log::info('Booking created successfully', [
                'booking_id' => $booking->id,
                'tenant_id' => $user->id,
                'landlord_id' => $property->user_id,
                'property_id' => $property->id,
                'room_id' => $room ? $room->id : null,
                'status' => 'pending'
            ]);

            // Create notification for landlord
            Notification::create([
                'user_id' => $property->user_id,
                'booking_id' => $booking->id,
                'message' => "Tenant {$user->first_name} {$user->last_name} has requested a booking for {$property->title} on {$request->booking_date} at {$request->booking_time}.",
                'is_read' => false,
            ]);

            // DEBUG: Transaction about to commit
            Log::debug('BookingController@store transaction about to commit', [
                'booking_id' => $booking->id,
                'timestamp' => now()
            ]);

            return $booking;
        });

        // DEBUG: Transaction committed successfully
        Log::debug('BookingController@store transaction committed successfully', [
            'booking_id' => $booking->id,
            'timestamp' => now()
        ]);

        // Log booking submission
        Log::info('Booking request submitted', [
            'booking_id' => $booking->id,
            'tenant_id' => $user->id,
            'landlord_id' => $property->user_id,
            'property_id' => $property->id,
            'room_id' => $room ? $room->id : null,
            'status' => 'pending',
            'timestamp' => now()
        ]);

        // Send confirmation email to tenant
        try {
            Mail::to($user->email)->send(new BookingConfirmation($booking));
            Log::info('Booking confirmation email sent', [
                'booking_id' => $booking->id,
                'email' => $user->email,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation email', [
                'booking_id' => $booking->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
        }

        // Send notification email to landlord
        try {
            Mail::to($property->user->email)->send(new LandlordBookingNotification($booking));
            Log::info('Landlord booking notification email sent', [
                'booking_id' => $booking->id,
                'landlord_email' => $property->user->email,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send landlord booking notification email', [
                'booking_id' => $booking->id,
                'landlord_email' => $property->user->email,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
        }

        return redirect()->back()->with('success', 'Booking request submitted successfully!');
    }

    public function landlordIndex()
    {
        $bookings = Booking::with(['tenant', 'property', 'room'])
                    ->where('landlord_id', Auth::id())
                    ->where('status', 'pending') // ✅ Only pending
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('bookings.landlord_index', compact('bookings'));
    }

    public function accept(Request $request, Booking $booking)
    {
        $this->authorizeAction($booking);

        $request->validate([
            'landlord_terms' => 'required|string|min:10|max:2000',
        ], [
            'landlord_terms.required' => 'Please provide your terms and conditions.',
            'landlord_terms.min' => 'Terms must be at least 10 characters long.',
            'landlord_terms.max' => 'Terms cannot exceed 2000 characters.',
        ]);

        // Check for overlapping bookings
        $overlap = Booking::where('property_id', $booking->property_id)
            ->where('booking_date', $booking->booking_date)
            ->where('booking_time', $booking->booking_time)
            ->whereIn('status', ['confirmed', 'accepted'])
            ->when($booking->room_id, fn($q) => $q->where('room_id', $booking->room_id))
            ->exists();

        if ($overlap) {
            return back()->with('error', 'This booking slot is no longer available.');
        }

        DB::transaction(function () use ($request, $booking) {
            $booking->update([
                'status' => 'confirmed',
                'landlord_terms' => $request->landlord_terms,
                'signed_by_landlord' => true,
                'contract_status' => Booking::CONTRACT_ACTIVE,
            ]);
        });

        // Send approval email to tenant
        try {
            Mail::to($booking->tenant->email)->send(new BookingApproved($booking));
            Log::info('Booking approval email sent', [
                'booking_id' => $booking->id,
                'email' => $booking->tenant->email,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking approval email', [
                'booking_id' => $booking->id,
                'email' => $booking->tenant->email,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
        }

        return back()->with('success', 'Booking confirmed successfully! The tenant will be notified of your terms.');
    }

    public function decline(Booking $booking)
    {
        $this->authorizeAction($booking);

        $booking->update(['status' => 'declined']);

        // Send decline email to tenant
        try {
            Mail::to($booking->tenant->email)->send(new BookingDeclined($booking));
            Log::info('Booking decline email sent', [
                'booking_id' => $booking->id,
                'email' => $booking->tenant->email,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking decline email', [
                'booking_id' => $booking->id,
                'email' => $booking->tenant->email,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
        }

        return back()->with('success', 'Booking declined successfully.');
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

    public function showCancelPage(Booking $booking)
    {
        if ($booking->tenant_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be cancelled.');
        }

        return view('bookings.cancel', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->tenant_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be cancelled.');
        }

        $booking->update(['status' => 'cancelled']);

        Log::info('Booking cancelled', [
            'booking_id' => $booking->id,
            'tenant_id' => auth()->id(),
            'timestamp' => now()
        ]);

        return redirect()->route('bookings.tenant.index')->with('success', 'Booking request cancelled.');
    }

    public function showReschedulePage(Booking $booking)
    {
        if ($booking->tenant_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($booking->status, ['pending', 'accepted'])) {
            return redirect()->route('bookings.tenant.index')->with('error', 'Only pending or accepted bookings can be rescheduled.');
        }

        return view('bookings.reschedule', compact('booking'));
    }

    public function reschedule(Request $request, Booking $booking)
    {
        if ($booking->tenant_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($booking->status, ['pending', 'accepted'])) {
            return redirect()->back()->with('error', 'Only pending or accepted bookings can be rescheduled.');
        }

        $rules = [
            'booking_date' => 'required|date|after:today',
            'booking_time' => 'required|date_format:H:i',
        ];

        if ($booking->property->rooms && $booking->property->rooms->count() > 0) {
            $rules['room_id'] = 'required|exists:rooms,id';
        } else {
            $rules['room_id'] = 'nullable|exists:rooms,id';
        }

        $request->validate($rules);

        // Check if the selected room belongs to this property
        $room = \App\Models\Room::where('id', $request->room_id)
            ->where('property_id', $booking->property_id)
            ->first();

        if (!$room) {
            return redirect()->back()->with('error', 'Selected room does not belong to this property.');
        }

        // Check for overlapping bookings
        $overlap = Booking::where('property_id', $booking->property_id)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereIn('status', ['confirmed', 'accepted'])
            ->when($request->room_id, fn($q) => $q->where('room_id', $request->room_id))
            ->where('id', '!=', $booking->id)
            ->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'The new booking slot is not available.');
        }

        DB::transaction(function () use ($request, $booking) {
            $booking->update([
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'room_id' => $request->room_id,
            ]);

            // Create notification for landlord
            Notification::create([
                'user_id' => $booking->landlord_id,
                'booking_id' => $booking->id,
                'message' => "Tenant {$booking->tenant->first_name} {$booking->tenant->last_name} has rescheduled their booking for {$booking->property->title} to {$request->booking_date} at {$request->booking_time}.",
                'is_read' => false,
            ]);

            Log::info('Booking rescheduled', [
                'booking_id' => $booking->id,
                'tenant_id' => auth()->id(),
                'new_date' => $request->booking_date,
                'new_time' => $request->booking_time,
                'new_room_id' => $request->room_id,
                'timestamp' => now()
            ]);
        });

        // Send notification email to landlord
        try {
            Mail::to($booking->landlord->email)->send(new LandlordBookingNotification($booking));
            Log::info('Landlord reschedule notification email sent', [
                'booking_id' => $booking->id,
                'landlord_email' => $booking->landlord->email,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send landlord reschedule notification email', [
                'booking_id' => $booking->id,
                'landlord_email' => $booking->landlord->email,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
        }

        return redirect()->route('bookings.tenant.index')->with('success', 'Reschedule request submitted successfully!');
    }

    public function landlordContracts()
    {
        $landlordId = auth()->id();

        $bookings = \App\Models\Booking::with(['tenant', 'property','room'])
            ->where('landlord_id', $landlordId)
            ->whereNotNull('finalized_at')
            ->orderByDesc('finalized_at')
            ->paginate(10);

        return view('landlord.contracts.index', compact('bookings'));
    }

    public function completeContract(Request $request, Booking $booking)
    {
        $this->authorizeContractAction($booking);

        if ($booking->contract_status !== Booking::CONTRACT_ACTIVE) {
            return back()->with('error', 'Only active contracts can be marked as completed.');
        }

        $booking->update([
            'contract_status' => Booking::CONTRACT_COMPLETED,
        ]);

        return back()->with('success', 'Contract marked as completed successfully.');
    }

    public function terminateContract(Request $request, Booking $booking)
    {
        $this->authorizeContractAction($booking);

        $request->validate([
            'termination_reason' => 'required|string|max:1000',
        ]);

        if ($booking->contract_status !== Booking::CONTRACT_ACTIVE) {
            return back()->with('error', 'Only active contracts can be terminated.');
        }

        $booking->update([
            'contract_status' => Booking::CONTRACT_TERMINATED,
            'termination_reason' => $request->termination_reason,
            'terminated_at' => now(),
        ]);

        return back()->with('success', 'Contract terminated successfully.');
    }

    private function authorizeContractAction($booking)
    {
        if ($booking->landlord_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$booking->finalized_at) {
            abort(403, 'Contract must be finalized first.');
        }
    }


}
