@extends('layouts.app')

@push('styles')
<style>
    /* Form styling */
    .form-control {
        border: 2px solid #e1e8ed;
        border-radius: 10px;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-select {
        border: 2px solid #e1e8ed;
        border-radius: 10px;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .btn-tenant-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 25px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-tenant-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Reschedule Booking</h4>
                    <p>Update the date and time for your booking request.</p>
                </div>
                <div class="card-body">
                    <div class="booking-details mb-4">
                        <h5>Current Booking Details:</h5>
                        <p><strong>Property:</strong> {{ $booking->property->title }}</p>
                        <p><strong>Room:</strong> {{ $booking->room ? 'Room ' . $booking->room->id : 'N/A' }}</p>
                        <p><strong>Current Date:</strong> {{ $booking->booking_date ? $booking->booking_date->format('F j, Y') : 'Not specified' }}</p>
                        <p><strong>Current Time:</strong> {{ $booking->booking_time ? date('g:i A', strtotime($booking->booking_time)) : 'Not specified' }}</p>
                    </div>

                    @php
                        $propertyRooms = $booking->property->rooms;
                        $requireRoom = $propertyRooms->count() > 1;
                    @endphp

                    <form action="{{ route('bookings.reschedule', $booking->id) }}" method="POST" id="rescheduleForm">
                        @csrf
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">New Date *</label>
                            <input type="date" class="form-control" id="booking_date" name="booking_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ $booking->booking_date ? $booking->booking_date->format('Y-m-d') : '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="booking_time" class="form-label">New Time *</label>
                            <input type="time" class="form-control" id="booking_time" name="booking_time" required value="{{ $booking->booking_time }}">
                        </div>

                        <div class="mb-3">
                            @if($propertyRooms->count() === 1)
                                <label for="room_id" class="form-label">New Room</label>
                                <select class="form-select" id="room_id" name="room_id" style="display:none;">
                                    <option value="{{ $propertyRooms->first()->id }}" selected>{{ $propertyRooms->first()->name }}</option>
                                </select>
                                <span id="auto-room" class="form-control-plaintext" style="display:block;">Room: {{ $propertyRooms->first()->name }}</span>
                            @elseif($propertyRooms->count() > 1)
                                <label for="room_id" class="form-label">New Room *</label>
                                <select class="form-select" id="room_id" name="room_id" required style="display:block;">
                                    <option value="">Select a room</option>
                                </select>
                                <span id="auto-room" class="form-control-plaintext" style="display:none;"></span>
                            @else
                                <label for="room_id" class="form-label">New Room</label>
                                <select class="form-select" id="room_id" name="room_id" style="display:block;">
                                    <option value="">No rooms available for this property.</option>
                                </select>
                                <span id="auto-room" class="form-control-plaintext" style="display:none;"></span>
                            @endif
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-tenant-primary" id="updateButton">Update Booking</button>
                            <a href="{{ route('bookings.tenant.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const propertyId = {{ $booking->property->id }};
    const currentBookingId = {{ $booking->id }};
    const requireRoom = {{ $requireRoom ? 'true' : 'false' }};
    const dateInput = document.getElementById('booking_date');
    const timeInput = document.getElementById('booking_time');
    const roomSelect = document.getElementById('room_id');
    const currentRoomId = {{ $booking->room_id }};

    let availabilityData = null;

    // Load available dates and times on page load
    loadAvailability();

    function loadAvailability() {
        fetch(`/properties/${propertyId}/availability?exclude_booking_id=${currentBookingId}`, { credentials: 'same-origin' })
            .then(response => response.json())
            .then(data => {
                availabilityData = data;
                dateInput.disabled = false;
                timeInput.disabled = true;
                roomSelect.disabled = true;
                roomSelect.innerHTML = '<option value="">First select a time</option>';
            })
            .catch(error => {
                console.error('Error loading availability:', error);
            });
    }

    // When date is selected, validate and enable time
    dateInput.addEventListener('change', function() {
        const selectedDate = this.value;
        if (selectedDate && availabilityData) {
            if (!availabilityData.available_dates.includes(selectedDate)) {
                alert('Selected date is not available.');
                this.value = '';
                timeInput.disabled = true;
                roomSelect.disabled = true;
                roomSelect.innerHTML = '<option value="">First select a time</option>';
                document.getElementById('auto-room').style.display = 'none';
                return;
            }
            timeInput.disabled = false;
            roomSelect.disabled = true;
            roomSelect.innerHTML = '<option value="">First select a time</option>';
            document.getElementById('auto-room').style.display = 'none';
        } else {
            timeInput.disabled = true;
            roomSelect.disabled = true;
            roomSelect.innerHTML = '<option value="">First select a time</option>';
            document.getElementById('auto-room').style.display = 'none';
        }
    });

    // When time is selected, validate and load rooms
    timeInput.addEventListener('change', function() {
        const selectedDate = dateInput.value;
        const selectedTime = this.value;
        if (selectedDate && selectedTime && availabilityData) {
            const times = availabilityData.available_times[selectedDate] || [];
            if (!times.includes(selectedTime)) {
                alert('Selected time is not available.');
                this.value = '';
                roomSelect.disabled = true;
                roomSelect.innerHTML = '<option value="">First select a time</option>';
                return;
            }
            fetch(`/properties/${propertyId}/availability/${selectedDate}/${selectedTime}?exclude_booking_id=${currentBookingId}`, { credentials: 'same-origin' })
                .then(response => response.json())
                .then(data => {
                    if (data.available_rooms.length === 1) {
                        const room = data.available_rooms[0];
                        roomSelect.value = room.id;
                        roomSelect.style.display = 'none';
                        document.getElementById('auto-room').textContent = `Room: ${room.name}`;
                        document.getElementById('auto-room').style.display = 'block';
                        document.getElementById('updateButton').disabled = false;
                    } else if (data.available_rooms.length > 1) {
                        roomSelect.style.display = 'block';
                        document.getElementById('auto-room').style.display = 'none';
                        roomSelect.innerHTML = '<option value="">Select a room</option>';
                        data.available_rooms.forEach(room => {
                            const option = document.createElement('option');
                            option.value = room.id;
                            option.textContent = `${room.name} - ₱${room.price} (${room.capacity} person(s))`;
                            if (room.id == currentRoomId) {
                                option.selected = true;
                            }
                            roomSelect.appendChild(option);
                        });
                        document.getElementById('updateButton').disabled = false;
                    } else {
                        roomSelect.innerHTML = '<option value="">No available rooms for the selected date and time.</option>';
                        roomSelect.style.display = 'block';
                        document.getElementById('auto-room').style.display = 'none';
                        document.getElementById('updateButton').disabled = false;
                    }
                    roomSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading rooms:', error);
                });
        } else {
            roomSelect.disabled = true;
            roomSelect.innerHTML = '<option value="">First select a time</option>';
        }
    });

    // Form validation
    const rescheduleForm = document.getElementById('rescheduleForm');
    rescheduleForm.addEventListener('submit', function(e) {
        const requiredFields = ['booking_date', 'booking_time'];
        if (requireRoom) {
            requiredFields.push('room_id');
        }
        let isValid = true;

        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (!element.value.trim()) {
                element.classList.add('is-invalid');
                isValid = false;
            } else {
                element.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });

    // Remove invalid class on input
    document.querySelectorAll('#rescheduleForm input, #rescheduleForm select').forEach(element => {
        element.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
});
</script>

<!-- Success Modal -->
@if(session('success'))
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Reschedule Successful
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                <p class="mb-3">{{ session('success') }}</p>
                <p class="text-muted">The landlord will be notified of the changes.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('bookings.tenant.index') }}" class="btn btn-tenant-primary">View My Bookings</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Show success modal if session success exists
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        });
    @endif
</script>
@endif
@endsection
