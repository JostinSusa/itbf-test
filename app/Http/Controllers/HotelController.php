<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Validator;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Hotel::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|unique:hotels|max:255',
                'address' => 'required|max:255',
                'city' => 'required|max:255',
                'nit' => 'required|unique:hotels|max:30',
                'rooms_quantity' => 'required|integer|min:1'
            ]
        );

        $hotel = Hotel::create($validated);

        return response()->json($hotel, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        return Hotel::with('rooms')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $hotel = Hotel::findOrFail($id);
        $validated = $request->validate(
            [
                'name' => 'required|max:255|unique:hotels,name,' . $hotel->id,
                'address' => 'required|max:255',
                'city' => 'required|max:255',
                'nit' => 'required|max:30|unique:hotels,nit,' . $hotel->id,
                'rooms_quantity' => 'required|integer|min:1'
            ]
        );
        $hotel->update($validated);

        return response()->json($hotel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $hotel = Hotel::find($id);
        $hotel->delete();
        return response()->json(["message" => "Usuario Eliminado correctamente"]);
    }
}
