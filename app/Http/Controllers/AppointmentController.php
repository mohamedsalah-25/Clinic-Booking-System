<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return Appointment::all();
    }

    public function show($id) {
        return Appointment::findOrFail($id);
    }

    public function store(Request $request) {
        $appointment = Appointment::create($request->all());
        return response()->json($appointment, 201);
    }

    public function update(Request $request, $id) {
        $appointment = Appointment::findOrFail($id);
        $appointment->update($request->all());
        return response()->json($appointment);
    }

    public function destroy($id) {
        Appointment::destroy($id);
        return response()->json(null, 204);
    }
}
