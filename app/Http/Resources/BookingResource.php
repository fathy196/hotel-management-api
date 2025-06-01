<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'room' => new RoomResource($this->whenLoaded('room')),
            'check_in' => $this->check_in->format('Y-m-d'),
            'check_out' => $this->check_out->format('Y-m-d'),
            'guests' => $this->guests,
            'status' => $this->status,
            // 'status_label' => ucfirst(str_replace('_', ' ', $this->status)),
            'special_requests' => $this->special_requests,
            'duration_nights' => $this->check_in->diffInDays($this->check_out),
            'total_price' => optional($this->room)->price_per_night * $this->check_in->diffInDays($this->check_out),
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
