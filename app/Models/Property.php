<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        $allRatings = $this->reviews->pluck('rating');
        return $allRatings->count() ? round($allRatings->avg(), 1) : null;
    }

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'price',
        'room_count',
        'latitude',
        'longitude',
    ];

    
}
