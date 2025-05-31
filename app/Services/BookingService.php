<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingService
{
    public function getAllBookings(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Booking::with(['room', 'user']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('check_out', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('check_in', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function createBooking(array $data): Booking
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';
        return Booking::create($data);
    }

    public function checkRoomAvailability(Room $room, string $checkIn, string $checkOut): bool
    {
        // First check if room is marked as available
        if ($room->status !== 'available') {
            return false;
        }

        // Normalize dates
        $checkIn = date('Y-m-d 00:00:00', strtotime($checkIn));
        $checkOut = date('Y-m-d 23:59:59', strtotime($checkOut));

        // Check for conflicting bookings
        return !$room->bookings()
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    $q->where('check_in', '<', $checkOut)
                        ->where('check_out', '>', $checkIn);
                });
            })
            ->where('status', 'confirmed') // Only check confirmed bookings
            ->exists();
    }
}
