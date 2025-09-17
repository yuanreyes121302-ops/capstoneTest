<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tenant_id', 'landlord_id', 'property_id', 'room_id',
        'tenant_name', 'contact_number', 'email',
        'booking_date', 'booking_time',
        'terms', 'status', 'signed_by_tenant', 'signed_by_landlord', 'finalized_at', 'landlord_terms',
        'contract_status', 'termination_reason', 'terminated_at'
    ];

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    protected $casts = [
        'finalized_at' => 'datetime',
        'terminated_at' => 'datetime',
        'booking_date' => 'date',
        'booking_time' => 'datetime:H:i',
    ];

    // Contract status constants
    const CONTRACT_ACTIVE = 'active';
    const CONTRACT_COMPLETED = 'completed';
    const CONTRACT_TERMINATED = 'terminated';
    const CONTRACT_EXPIRED = 'expired';

    // Helper methods for contract status
    public function isActive()
    {
        return $this->contract_status === self::CONTRACT_ACTIVE;
    }

    public function isCompleted()
    {
        return $this->contract_status === self::CONTRACT_COMPLETED;
    }

    public function isTerminated()
    {
        return $this->contract_status === self::CONTRACT_TERMINATED;
    }

    public function isExpired()
    {
        return $this->contract_status === self::CONTRACT_EXPIRED;
    }

    public function getStatusBadgeClass()
    {
        return match($this->contract_status) {
            self::CONTRACT_ACTIVE => 'success',
            self::CONTRACT_COMPLETED => 'primary',
            self::CONTRACT_TERMINATED => 'danger',
            self::CONTRACT_EXPIRED => 'warning',
            default => 'secondary'
        };
    }

    public function getStatusText()
    {
        return match($this->contract_status) {
            self::CONTRACT_ACTIVE => 'Active',
            self::CONTRACT_COMPLETED => 'Completed',
            self::CONTRACT_TERMINATED => 'Terminated',
            self::CONTRACT_EXPIRED => 'Expired',
            default => 'Unknown'
        };
    }

}
