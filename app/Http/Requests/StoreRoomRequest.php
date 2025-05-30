<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'room_number' => 'required|string|unique:rooms,room_number,' . $this->room?->id,
            'type' => 'required|in:single,double,suite',
            'price_per_night' => 'required|numeric|min:0',
            'status' => 'required|in:available,booked,maintenance',
            'max_occupancy' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ];
    }
    public function messages(): array
    {
        return [
            'room_number.required' => 'Room number is required',
            'room_number.unique' => 'This room number already exists',
            'type.required' => 'Room type is required',
            'type.in' => 'Invalid room type. Must be single, double, or suite',
            'price_per_night.required' => 'Price per night is required',
            'price_per_night.numeric' => 'Price must be a number',
            'price_per_night.min' => 'Price cannot be negative',
            'status.required' => 'Room status is required',
            'status.in' => 'Invalid status. Must be available, booked, or maintenance',
            'max_occupancy.required' => 'Maximum occupancy is required',
            'max_occupancy.integer' => 'Occupancy must be a whole number',
            'max_occupancy.min' => 'Occupancy must be at least 1',
        ];
    }
}
