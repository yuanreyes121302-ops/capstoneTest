<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LandlordBookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     *
     * @param Booking $booking
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Booking Request - ' . $this->booking->property->title)
                    ->view('emails.landlord_booking_notification')
                    ->with([
                        'booking' => $this->booking,
                        'tenant' => $this->booking->tenant,
                        'property' => $this->booking->property,
                        'landlord' => $this->booking->landlord,
                    ]);
    }
}
