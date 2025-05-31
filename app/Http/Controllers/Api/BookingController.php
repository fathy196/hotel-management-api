<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
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
    public function show(Booking $booking)
    {
        return ApiResponseHelper::apiResponse(true, new BookingResource($booking->load(['room', 'user'])), 'Booking fetched successfully');
    }
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $updatedBooking = $this->bookingService->updateBooking($booking, $request->validated());
        return ApiResponseHelper::apiResponse(true, new BookingResource($updatedBooking), 'Booking updated successfully');
    }
    public function destroy($id)
    {
        $booking = Booking::withTrashed()->where('id', $id)->first();

        if (!$booking) {
            return ApiResponseHelper::apiResponse(false, null, 'Booking not found', 404);
        }

        if ($booking->trashed()) {
            return ApiResponseHelper::apiResponse(false, null, 'Booking is already deleted', 409);
        }

        $this->bookingService->deleteBooking($booking);
        return ApiResponseHelper::apiResponse(true, null, 'Booking deleted successfully');
    }
    public function confirm(Booking $booking)
    {
        $booking = $this->bookingService->confirmBooking($booking);
        return ApiResponseHelper::apiResponse(true, new BookingResource($booking), 'Booking confirmed successfully');
    }

    public function cancel(Booking $booking)
    {
        $booking = $this->bookingService->cancelBooking($booking);
        return ApiResponseHelper::apiResponse(true, new BookingResource($booking), 'Booking cancelled successfully');
    }
}
