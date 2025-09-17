<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'gender',
        'dob',
        'role',
        'password',
        'profile_image',
        'is_approved',
        'contact_number',
        'is_online',
        'last_seen',
        'location',
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'tenant_id');
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Set user as online
     */
    public function setOnline()
    {
        try {
            // Only update if the model exists and has an ID
            if ($this->exists && $this->id) {
                $this->update([
                    'is_online' => true,
                    'last_seen' => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Log the error but don't throw it to avoid breaking the request
            \Log::error('Failed to set user online', [
                'user_id' => $this->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Set user as offline
     */
    public function setOffline()
    {
        $this->update([
            'is_online' => false,
            'last_seen' => now(),
        ]);
    }

    /**
     * Check if user is currently online
     */
    public function isCurrentlyOnline()
    {
        try {
            if ($this->is_online) {
                return true;
            }

            // Consider user online if last seen within last 5 minutes
            return $this->last_seen && $this->last_seen->diffInMinutes(now()) <= 5;
        } catch (\Exception $e) {
            // Log the error but return false as fallback
            \Log::error('Error checking if user is online', [
                'user_id' => $this->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get online status text
     */
    public function getOnlineStatus()
    {
        return $this->isCurrentlyOnline() ? 'online' : 'offline';
    }

}
