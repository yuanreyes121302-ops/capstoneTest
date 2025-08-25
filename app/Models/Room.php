<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'name', 'price', 'capacity', 'available_slots'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        if ($this->reviews->count() === 0) {
            return null;
        }

        return round($this->reviews->avg('rating'), 1); // e.g., 4.3
    }


}
