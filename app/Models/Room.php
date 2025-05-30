<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'room_number',
        'type',
        'price_per_night',
        'status',
        'max_occupancy',
        'description'
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
    ];

    // Scopes
    public function scopeAvailable(Builder $query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOfType(Builder $query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithCapacity(Builder $query, int $guests)
    {
        return $query->where('max_occupancy', '>=', $guests);
    }

    // Relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Helpers
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function markAsBooked(): void
    {
        $this->update(['status' => 'booked']);
    }

    public function markAsAvailable(): void
    {
        $this->update(['status' => 'available']);
    }
}
