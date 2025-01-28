<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Hotel;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Show rooms for a specific hotel.
     */
    public function showByHotel($hotelId)
    {
        $rooms = Room::where('hotel_id', $hotelId)->get();
        return response()->json($rooms);
    }

    /**
     * Store a newly created room.
     */
    public function store(Request $request, $hotelId)
    {
        $hotel = Hotel::findOrFail($hotelId);

        $validated = $request->validate([
            'type' => 'required|in:Estándar,Junior,Suite',
            'accommodation' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $validOptions = match ($request->type) {
                        'Estándar' => ['Sencilla', 'Doble'],
                        'Junior' => ['Triple', 'Cuádruple'],
                        'Suite' => ['Sencilla', 'Doble', 'Triple'],
                        default => [],
                    };
                    if (!in_array($value, $validOptions)) {
                        $fail("La acomodación {$value} no es válida para el tipo de habitación {$request->type}.");
                    }
                },
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($hotel) {
                    $currentQuantity = Room::where('hotel_id', $hotel->id)->sum('quantity');
                    if (($currentQuantity + $value) > $hotel->rooms_quantity) {
                        $fail("La cantidad total de habitaciones no puede superar el máximo permitido por el hotel ({$hotel->rooms_quantity}).");
                    }
                },
            ],
        ]);

        // Verificar duplicados
        $duplicate = Room::where('hotel_id', $hotel->id)
            ->where('type', $request->type)
            ->where('accommodation', $request->accommodation)
            ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => 'Ya existe una habitación con el mismo tipo y acomodación en este hotel.'
            ], 422);
        }

        $room = Room::create(array_merge($validated, ['hotel_id' => $hotelId]));

        return response()->json($room, 201);
    }

    /**
     * Update the specified room.
     */
    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $hotel = $room->hotel;

        $validated = $request->validate([
            'type' => 'required|in:Estándar,Junior,Suite',
            'accommodation' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $validOptions = match ($request->type) {
                        'Estándar' => ['Sencilla', 'Doble'],
                        'Junior' => ['Triple', 'Cuádruple'],
                        'Suite' => ['Sencilla', 'Doble', 'Triple'],
                        default => [],
                    };
                    if (!in_array($value, $validOptions)) {
                        $fail("La acomodación {$value} no es válida para el tipo de habitación {$request->type}.");
                    }
                },
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($hotel, $room) {
                    $currentQuantity = Room::where('hotel_id', $hotel->id)
                        ->where('id', '!=', $room->id)
                        ->sum('quantity');
                    if (($currentQuantity + $value) > $hotel->rooms_quantity) {
                        $fail("La cantidad total de habitaciones no puede superar el máximo permitido por el hotel ({$hotel->rooms_quantity}).");
                    }
                },
            ],
        ]);

        // Verificar duplicados excluyendo la habitación actual
        $duplicate = Room::where('hotel_id', $hotel->id)
            ->where('type', $request->type)
            ->where('accommodation', $request->accommodation)
            ->where('id', '!=', $room->id)
            ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => 'Ya existe una habitación con el mismo tipo y acomodación en este hotel.'
            ], 422);
        }

        $room->update($validated);

        return response()->json($room);
    }

    /**
     * Remove the specified room.
     */
    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return response()->json(["message" => "Habitación eliminada correctamente."]);
    }
}
