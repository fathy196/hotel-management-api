<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $room = Room::find($this->room_id);

            if (!$room)
                return;

            // Check guest capacity
            if ($this->guests > $room->max_occupancy) {
                $validator->errors()->add('guests', 'Guest count exceeds the room capacity.');
            }

            // Check availability
            $overlapping = $room->bookings()
                ->where(function ($q) {
                    $q->where('check_in', '<', $this->check_out)
                        ->where('check_out', '>', $this->check_in);
                })
                ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->exists();

            if ($overlapping) {
                $validator->errors()->add('room_id', 'The selected room is not available for the given dates.');
            }
        });
    }
}

