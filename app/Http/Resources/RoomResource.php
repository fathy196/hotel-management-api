<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_number' => $this->room_number,
            'type_label' => ucfirst($this->type),
            'formatted_price' => '$' . number_format($this->price_per_night, 2),
            'status' => $this->status,
            'max_occupancy' => $this->max_occupancy,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'bookings' => BookingResource::collection($this->whenLoaded('bookings'))
        ];
    }
}
