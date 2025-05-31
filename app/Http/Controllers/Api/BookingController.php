<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService)
    {
    }

    public function index(Request $request)
    {
        $bookings = $this->bookingService->getAllBookings($request->all());
        return ApiResponseHelper::apiResponse(true, BookingResource::collection($bookings), 'Bookings fetched successfully');
    }
    public function store(StoreBookingRequest $request)
    {
        $room = Room::findOrFail($request->room_id);

        if (!$this->bookingService->checkRoomAvailability($room, $request->check_in, $request->check_out)) {
            return ApiResponseHelper::apiResponse(false, null, 'Room not available for selected dates', 409);
        }

        $booking = $this->bookingService->createBooking($request->validated());
        return ApiResponseHelper::apiResponse(true, new BookingResource($booking), 'Booking created successfully', 201);
    }
}
