<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
    'tenant_id',
    'room_id',
    'comment',
    'rating',
    'reply'
];

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

}
