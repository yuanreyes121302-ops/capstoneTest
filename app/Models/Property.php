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

    public function getAverageRatingAttribute()
    {
        $allRatings = $this->rooms->flatMap->reviews->pluck('rating');
        return $allRatings->count() ? round($allRatings->avg(), 1) : null;
    }

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'price',
        'room_count',
        'image',
        'latitude',
        'longitude',
    ];

    
}
