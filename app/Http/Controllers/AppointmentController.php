<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email'=> 'required|email|unique:users',
            'phone'=> 'required',
            'password'=> 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'phone'=>$request->phone,
            'password'=> Hash::make($request->password)
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json(['user'=>$user,'token'=>$token]);
    }

    public function login(Request $request){
        $user = User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json(['message'=>'Invalid credentials'],401);
        }

        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json(['user'=>$user,'token'=>$token]);
    }
    public function user(Request $request){
        return response()->json($request->user());
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out']);
    }
}
