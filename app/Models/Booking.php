<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'room_id',
        'check_in',
        'check_out',
        'guests',
        'status',
        'special_requests',
        'payment_status',
        'payment_method',
        'payment_reference',
        'total_amount',
        'paid_at',
        'refund_amount',
        'refunded_at',
        'refund_reason'
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }
}
