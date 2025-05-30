<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public function rules(): array
    {
        $roomId = $this->route('room')?->id; // Safe way to get current room ID

        return [
            'room_number' => 'sometimes|string|unique:rooms,room_number,' . $roomId,
            'type' => 'sometimes|in:single,double,suite',
            'price_per_night' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:available,booked,maintenance',
            'max_occupancy' => 'sometimes|integer|min:1',
            'description' => 'nullable|string',
        ];
    }
}
