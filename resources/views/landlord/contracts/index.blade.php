@extends('layouts.app')

@push('styles')
<style>
    /* Landlord-specific modern design for contracts page */
    .landlord-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .landlord-header h2 {
        font-weight: 300;
        margin-bottom: 0;
        font-size: 2.5rem;
    }

    .landlord-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .contract-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        border: none;
        margin-bottom: 2rem;
        transition: transform 0.3s ease;
    }

    .contract-card:hover {
        transform: translateY(-5px);
    }

    .contract-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .contract-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .property-icon {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .contract-details h5 {
        margin: 0;
        font-weight: 600;
    }

    .contract-details p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.9rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .contract-body {
        padding: 2rem;
    }

    .contract-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-card {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        border-left: 4px solid #667eea;
    }

    .info-card h6 {
        margin: 0 0 0.5rem 0;
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-card p {
        margin: 0;
        color: #34495e;
        font-size: 0.95rem;
    }

    .terms-section {
        background: #f8f9ff;
        padding: 1.5rem;
        border-radius: 10px;
        border: 1px solid #e1e8ed;
        margin-bottom: 2rem;
    }

    .terms-section h6 {
        color: #667eea;
        margin-bottom: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .terms-content {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e1e8ed;
        max-height: 150px;
        overflow-y: auto;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-action {
        border-radius: 25px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        border: none;
        min-width: 120px;
    }

    .btn-terminate {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
    }

    .btn-terminate:hover {
        background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(231, 76, 60, 0.5);
    }

    .btn-complete {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
    }

    .btn-complete:hover {
        background: linear-gradient(135deg, #229954 0%, #27ae60 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(39, 174, 96, 0.5);
    }

    .no-contracts {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .no-contracts i {
        font-size: 4rem;
        color: #bdc3c7;
        margin-bottom: 1rem;
    }

    .no-contracts h4 {
        color: #7f8c8d;
        margin-bottom: 0.5rem;
    }

    .no-contracts p {
        color: #95a5a6;
    }

    .back-btn {
        position: absolute;
        top: 2rem;
        left: 2rem;
        z-index: 10;
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .back-btn:hover {
        background: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }

    .termination-form {
        margin-top: 1rem;
        display: none;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border: 2px solid #e1e8ed;
        border-radius: 10px;
        padding: 0.8rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    @media (max-width: 768px) {
        .contract-grid {
            grid-template-columns: 1fr;
        }

        .contract-info {
            flex-direction: column;
            text-align: center;
            gap: 0.5rem;
        }

        .contract-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
        }
    }

    /* Notification styles */
    .alert {
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .alert-success {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        color: white;
    }

    .alert-danger {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
    }

    .alert-warning {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="landlord-header">
    <div class="landlord-container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <a href="{{ route('landlord.dashboard') }}" class="btn btn-light me-3" style="border-radius: 8px; color: #2c3e50; background: white; border: 1px solid #e1e8ed; transition: all 0.3s ease; font-weight: 500;">
                    ← Back
                </a>
                <h2>Contract Management</h2>
            </div>
            <a href="{{ route('landlord.properties.index') }}" class="btn btn-light">
                <i class="fas fa-home"></i> Manage Properties
            </a>
        </div>
    </div>
</div>

<div class="landlord-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($bookings->count() > 0)
        @foreach ($bookings as $booking)
            <div class="contract-card">
                <div class="contract-header">
                    <div class="contract-info">
                        <div class="property-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="contract-details">
                            <h5>{{ $booking->property->title }}</h5>
                            <p><i class="fas fa-user me-1"></i>{{ $booking->tenant->first_name }} {{ $booking->tenant->last_name }}</p>
                        </div>
                    </div>
                    <span class="status-badge bg-{{ $booking->contract_status ? $booking->getStatusBadgeClass() : 'secondary' }}">
                        {{ $booking->contract_status ? $booking->getStatusText() : 'Pending' }}
                    </span>
                </div>

                <div class="contract-body">
                    <div class="contract-grid">
                        <div class="info-card">
                            <h6><i class="fas fa-map-marker-alt"></i>Property Location</h6>
                            <p>{{ $booking->property->location }}</p>
                        </div>
                        <div class="info-card">
                            <h6><i class="fas fa-door-open"></i>Room Details</h6>
                            @if ($booking->room)
                                <p>₱{{ number_format($booking->room->price, 2) }} per month<br>
                                Capacity: {{ $booking->room->capacity }} person(s)</p>
                            @else
                                <p class="text-muted">Room info not available</p>
                            @endif
                        </div>
                        <div class="info-card">
                            <h6><i class="fas fa-calendar-check"></i>Contract Start</h6>
                            <p>{{ $booking->finalized_at ? $booking->finalized_at->format('F d, Y') : 'Not finalized' }}</p>
                        </div>
                        <div class="info-card">
                            <h6><i class="fas fa-clock"></i>Last Updated</h6>
                            <p>{{ $booking->updated_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>

                    @if($booking->terms)
                        <div class="terms-section">
                            <h6><i class="fas fa-file-alt"></i>Tenant's Terms</h6>
                            <div class="terms-content">{{ $booking->terms }}</div>
                        </div>
                    @endif

                    @if($booking->landlord_terms)
                        <div class="terms-section">
                            <h6><i class="fas fa-file-contract"></i>Your Terms</h6>
                            <div class="terms-content">{{ $booking->landlord_terms }}</div>
                        </div>
                    @endif

                    @if($booking->contract_status === \App\Models\Booking::CONTRACT_TERMINATED && $booking->termination_reason)
                        <div class="terms-section">
                            <h6><i class="fas fa-exclamation-triangle"></i>Termination Reason</h6>
                            <div class="terms-content">{{ $booking->termination_reason }}</div>
                        </div>
                    @endif

                    @if($booking->contract_status === \App\Models\Booking::CONTRACT_ACTIVE)
                        <div class="action-buttons">
                            <button class="btn btn-action btn-complete" onclick="showCompleteForm({{ $booking->id }})">
                                <i class="fas fa-check-circle me-1"></i>Mark Complete
                            </button>
                            <button class="btn btn-action btn-terminate" onclick="showTerminateForm({{ $booking->id }})">
                                <i class="fas fa-times-circle me-1"></i>Terminate
                            </button>
                        </div>

                        <div id="completeForm{{ $booking->id }}" class="termination-form">
                            <hr>
                            <form action="{{ route('landlord.contracts.complete', $booking->id) }}" method="POST">
                                @csrf
                                <p>Are you sure you want to mark this contract as completed?</p>
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary me-2" onclick="hideCompleteForm({{ $booking->id }})">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-action btn-complete">
                                        <i class="fas fa-check-circle me-1"></i>Confirm Complete
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div id="terminateForm{{ $booking->id }}" class="termination-form">
                            <hr>
                            <form action="{{ route('landlord.contracts.terminate', $booking->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="termination_reason_{{ $booking->id }}" class="form-label">
                                        <i class="fas fa-edit me-1"></i>Termination Reason
                                    </label>
                                    <textarea name="termination_reason" id="termination_reason_{{ $booking->id }}"
                                              class="form-control" rows="3" required
                                              placeholder="Please provide a reason for termination..."></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary me-2" onclick="hideTerminateForm({{ $booking->id }})">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-action btn-terminate">
                                        <i class="fas fa-times-circle me-1"></i>Terminate Contract
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $bookings->links() }}
            </div>
        @endif
    @else
        <div class="no-contracts">
            <i class="fas fa-file-contract"></i>
            <h4>No Contracts Yet</h4>
            <p>You don't have any finalized contracts at the moment.</p>
            <a href="{{ route('landlord.dashboard') }}" class="btn btn-light" style="border-radius: 8px; color: #2c3e50; background: white; border: 1px solid #e1e8ed; transition: all 0.3s ease; font-weight: 500;">
                ← Back
            </a>
        </div>
    @endif
</div>

<script>
function showCompleteForm(contractId) {
    document.getElementById('completeForm' + contractId).style.display = 'block';
}

function hideCompleteForm(contractId) {
    document.getElementById('completeForm' + contractId).style.display = 'none';
}

function showTerminateForm(contractId) {
    document.getElementById('terminateForm' + contractId).style.display = 'block';
    document.querySelector('#terminateForm' + contractId + ' textarea').focus();
}

function hideTerminateForm(contractId) {
    document.getElementById('terminateForm' + contractId).style.display = 'none';
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
@endsection
