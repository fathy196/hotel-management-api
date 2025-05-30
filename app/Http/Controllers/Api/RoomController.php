<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Services\RoomService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function __construct(protected RoomService $roomService)
    {
    }

    public function index(Request $request)
    {
        $rooms = $this->roomService->getAllRooms($request->all());
        return ApiResponseHelper::apiResponse(true, RoomResource::collection($rooms), 'Rooms fetched successfully');
    }

    public function store(StoreRoomRequest $request)
    {
        $room = $this->roomService->createRoom($request->validated());
        return ApiResponseHelper::apiResponse(true, new RoomResource($room), 'Room created successfully', 201);
    }

    public function show(Room $room)
    {
        return ApiResponseHelper::apiResponse(true, new RoomResource($room->load('bookings')), 'Room fetched successfully');
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        $updatedRoom = $this->roomService->updateRoom($room, $request->validated());
        return ApiResponseHelper::apiResponse(true, new RoomResource($updatedRoom), 'Room updated successfully');
    }

    public function destroy($id)
    {
        $room = Room::withTrashed()->where('id', $id)->first();

        if (!$room) {
            return ApiResponseHelper::apiResponse(
                false,
                null,
                'Room not found',
                404
            );
        }

        if ($room->trashed()) {
            return ApiResponseHelper::apiResponse(
                false,
                null,
                'Room is already deleted',
                409
            );
        }

        $this->roomService->deleteRoom($room);
        return ApiResponseHelper::apiResponse(
            true,
            null,
            'Room deleted successfully'
        );
    }

    public function restore($id)
    {
        $room = Room::withTrashed()->find($id);

        // Check if the room exists (including trashed)
        if (!$room) {
            return ApiResponseHelper::apiResponse(
                false,
                null,
                'Room not found',
                404
            );
        }

        // Check if the room is not soft-deleted
        if (!$room->trashed()) {
            return ApiResponseHelper::apiResponse(
                false,
                null,
                'Room is not deleted',
                409 // Conflict status code
            );
        }

        $room->restore();
        return ApiResponseHelper::apiResponse(
            true,
            new RoomResource($room),
            'Room restored successfully'
        );
    }

    public function available(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'nullable|integer|min:1'
        ]);

        $rooms = $this->roomService->getAvailableRooms(
            $request->check_in,
            $request->check_out,
            $request->guests
        );

        return ApiResponseHelper::apiResponse(true, RoomResource::collection($rooms), 'Available rooms fetched successfully');
    }

    public function trashed()
    {
        $rooms = Room::onlyTrashed()->get();

        if ($rooms->isEmpty()) {
            return ApiResponseHelper::apiResponse(false, null, 'No trashed rooms found', 404);
        }

        return ApiResponseHelper::apiResponse(
            true,
            RoomResource::collection($rooms),
            'Trashed rooms fetched successfully'
        );
    }
}
