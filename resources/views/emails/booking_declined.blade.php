<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Request Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #e74c3c; }
        .btn { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 25px; margin: 10px 5px; }
        .btn:hover { opacity: 0.9; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Booking Request Update</h1>
            <p>Update on your booking request status.</p>
        </div>

        <div class="content">
            <h2>Hello {{ $tenant->first_name }} {{ $tenant->last_name }},</h2>

            <p>We regret to inform you that your booking request has been declined. Here are the details:</p>

            <div class="booking-details">
                <h3>Booking Details</h3>
                <p><strong>Property:</strong> {{ $property->title }}</p>
                <p><strong>Location:</strong> {{ $property->location }}</p>
                <p><strong>Requested Date:</strong> {{ $booking->booking_date ? $booking->booking_date->format('F j, Y') : 'Not specified' }}</p>
                <p><strong>Requested Time:</strong> {{ $booking->booking_time ? date('g:i A', strtotime($booking->booking_time)) : 'Not specified' }}</p>
                <p><strong>Landlord:</strong> {{ $landlord->first_name }} {{ $landlord->last_name }}</p>
                <p><strong>Status:</strong> {{ ucfirst($booking->status) }}</p>
                @if($booking->terms)
                    <p><strong>Your Notes:</strong> {{ $booking->terms }}</p>
                @endif
            </div>

            <p>We apologize for any inconvenience this may cause. You may want to explore other available properties or contact the landlord directly to discuss alternative arrangements.</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('messages.show', $landlord->id) }}" class="btn">Contact Landlord</a>
                <a href="{{ route('tenant.properties.index') }}" class="btn">Browse Properties</a>
            </div>

            <div class="footer">
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; {{ date('Y') }} Property Rental System. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
