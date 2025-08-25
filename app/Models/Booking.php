<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tenant_id', 'landlord_id', 'property_id', 'room_id',
        'terms', 'status', 'signed_by_tenant', 'signed_by_landlord', 'finalized_at', 'landlord_terms'
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
    ];

}
