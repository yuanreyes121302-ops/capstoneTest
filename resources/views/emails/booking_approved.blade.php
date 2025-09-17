<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Approved</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #27ae60; }
        .btn { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); color: white; text-decoration: none; border-radius: 25px; margin: 10px 5px; }
        .btn:hover { opacity: 0.9; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Booking Approved!</h1>
            <p>Great news! Your booking request has been approved.</p>
        </div>

        <div class="content">
            <h2>Hello {{ $tenant->first_name }} {{ $tenant->last_name }},</h2>

            <p>Congratulations! Your booking request has been approved by the landlord. Here are the details:</p>

            <div class="booking-details">
                <h3>Booking Details</h3>
                <p><strong>Property:</strong> {{ $property->title }}</p>
                <p><strong>Location:</strong> {{ $property->location }}</p>
                <p><strong>Date:</strong> {{ $booking->booking_date ? $booking->booking_date->format('F j, Y') : 'Not specified' }}</p>
                <p><strong>Time:</strong> {{ $booking->booking_time ? date('g:i A', strtotime($booking->booking_time)) : 'Not specified' }}</p>
                <p><strong>Landlord:</strong> {{ $landlord->first_name }} {{ $landlord->last_name }}</p>
                <p><strong>Status:</strong> {{ ucfirst($booking->status) }}</p>
                @if($booking->terms)
                    <p><strong>Your Notes:</strong> {{ $booking->terms }}</p>
                @endif
                @if($booking->landlord_terms)
                    <p><strong>Landlord's Terms:</strong> {{ $booking->landlord_terms }}</p>
                @endif
            </div>

            <p>The next step is to finalize the contract. Please review the landlord's terms and conditions, then proceed to sign the contract.</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('bookings.finalize', $booking->id) }}" class="btn">Finalize Contract</a>
                <a href="{{ route('bookings.tenant.index') }}" class="btn">View My Bookings</a>
            </div>

            <p>If you need to cancel or reschedule this booking, you can use the options below:</p>

            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ route('bookings.cancel.page', $booking->id) }}" class="btn" style="background: #e74c3c; margin: 5px;">Cancel Booking</a>
                <a href="{{ route('bookings.reschedule.page', $booking->id) }}" class="btn" style="background: #f39c12; margin: 5px;">Reschedule Booking</a>
            </div>

            <div class="footer">
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; {{ date('Y') }} Property Rental System. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
