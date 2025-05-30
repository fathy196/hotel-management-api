<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Pagination\LengthAwarePaginator;

class RoomService
{
    public function getAllRooms(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Room::query();

        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price_per_night', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price_per_night', '<=', $filters['max_price']);
        }

        return $query->paginate($perPage);
    }

    public function createRoom(array $data): Room
    {
        return Room::create($data);
    }

    public function updateRoom(Room $room, array $data): Room
    {
        $room->update($data);
        return $room->fresh();
    }

    public function deleteRoom(Room $room): void
    {
        $room->delete();
    }

    public function getAvailableRooms(string $checkIn, string $checkOut, ?int $guests = null)
    {
        $query = Room::available();

        if ($guests) {
            $query->withCapacity($guests);
        }

        return $query->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
            $q->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<', $checkIn)
                            ->where('check_out', '>', $checkOut);
                    });
            })
                ->whereNotIn('status', ['cancelled', 'completed']);
        })->get();
    }
}

