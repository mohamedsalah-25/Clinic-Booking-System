<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\Reservation;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Notifications\NewAppointmentNotification;
use Illuminate\Support\Facades\Notification;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;


class DoctorController extends Controller
{
    public function index(){
        $doctors = Doctor::orderByDesc('price')->take(3)->get();
        return  view('index',compact('doctors'));
    }
     
    public function show(){
        return  view('addDoctors');
    }
    

    public function list(){
        $doctors = Doctor::paginate(5);
        $appointments = Appointment::all();
        return  view('ListOfDoc',compact('doctors','appointments') );
    }

    public function getAppointments($doctorId){
    
        $appointments = Appointment::where('doctor_id', $doctorId)->get(['id','day','time_start','time_end']);

    // نرجعهم بصيغة 12 ساعة AM/PM
    $appointments = $appointments->map(function($appointment){
        return [
            'id' => $appointment->id,
            // Format the date to ISO 8601 (JS-safe format)
            'day' => \Carbon\Carbon::parse($appointment->day)->format('Y-m-d'),
            'time_start' => \Carbon\Carbon::createFromFormat('H:i:s', $appointment->time_start)->format('g:i A'),
            'time_end' => \Carbon\Carbon::createFromFormat('H:i:s', $appointment->time_end)->format('g:i A'),
        ];
        });

        return response()->json($appointments); 
    }

    public function booking(Doctor $doctor =null){
        $doctors = Doctor::all();
        $appointments = Appointment::all();
        return  view('makeApp',compact('doctors','appointments','doctor'));
    }

    public function reserve(Request $request){
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'required|exists:appointments,id',
            ]);

            $reservation= Reservation::create([
                'user_id' => auth()->id(),
                'doctor_id' => $request->doctor_id,
                'appointment_id' => $request->appointment_id,
                'status' => 'pending',
            ]);
            $doctor = Doctor::find($request->doctor_id);
            
            activity()
            ->causedBy(auth()->user()) // who did it
            ->performedOn($reservation) // which model it affected (optional)
            ->withProperties(['doctor_id' => $doctor->id])
            ->log(auth()->user()->name .' booked an appointment with Dr. ' . $doctor->name);

             // send notification to admin (id = 1 for example)
                 $admin = User::where('is_admin', true)->first();
                      if ($admin) {
                           $admin->notify(new NewAppointmentNotification($reservation));
                         }

                  // Send Email
                    $user = Auth::user();
                    $data = [
                        'name' => $user->name,
                        'message' => 'Your reservation for Dr.' . $doctor->name . 'has been registered ,  Please wait for a reserve confirmation message',
                    ];

                    Mail::to($user->email)->send(new WelcomeMail($data));           

            return redirect()->route('reservation')->with('success', 'Reservation created successfully!');

    }
          
    public function reserveList( Request $request) {
    
        $query = Reservation::with(['doctor','appointment','user'])->latest();
        
        if (auth()->check() && auth()->user()->is_admin) {
            
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
    
        // Admin يشوف كل الحجوزات
    } else {
        // المستخدم العادي يشوف حجوزاته بس
        $query->where('user_id', auth()->id());
           
    }
    $reservations = $query->paginate(10);
    return view('reservation', compact('reservations'));
}

   
        public function search(Request $request) {
        
            $query = $request->input('q'); 

                    //  البحث في البوستات
                $doctors = Doctor::where('name', 'like', "%{$query}%")
                 ->orWhere('address', 'like', "%{$query}%")
                 ->orWhere('price', 'like', "%{$query}%")
                 ->paginate(5);
     

            
    return view('search', compact('query', 'doctors'));
}
     public function about(){
         return  view('about');
     }

    
}
