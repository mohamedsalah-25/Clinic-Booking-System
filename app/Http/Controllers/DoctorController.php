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

class DoctorController extends Controller
{
    public function index(){
        $doctors = Doctor::orderByDesc('price')->take(3)->get();
        return  view('index',compact('doctors'));
    }
    public function Admin() {

        $reservations = Reservation::with(['doctor', 'appointment', 'user'])
        ->latest() // order by created_at descending
        ->take(5)  // limit to 5 records
        ->get();
        $totalEarnings = \App\Models\Reservation::with('doctor')
        ->where('status', '=', 'confirmed')
        ->get()
        ->sum(fn($r) => optional($r->doctor)->price ?? 0);
        
        $totalToday = Reservation::whereDate('reservations.created_at', Carbon::today())
        ->join('doctors', 'reservations.doctor_id', '=', 'doctors.id')
        ->where('status', '=', 'confirmed')
        ->sum('doctors.price');
        $todayReservationsCount = Reservation::whereDate('created_at', Carbon::today())
        ->where('status', '!=', 'cancelled')
        ->count();
        
        // Top 5 doctors by total income

        $topDoctors = DB::table('reservations')
                ->join('doctors', 'reservations.doctor_id', '=', 'doctors.id')
                ->select(
                'doctors.name',
                'doctors.image',
                DB::raw('SUM(doctors.price) as total_income')
                )
                    ->groupBy('doctors.id', 'doctors.name','doctors.image')
                    ->orderByDesc('total_income')
                    ->where('status', '=', 'confirmed')
                    ->take(5)
                    ->get();
        
        $activities = Activity::with('causer') // eager load user (who did it)
                ->latest()
                ->paginate(5);
                    
         $latestUsers = User::latest()->where('is_admin', false)->take(5)->get();            
         
         $notifications = Auth::user()->notifications()->latest()->take(10)->get();

            
         $user = Auth::user();          // جلب المستخدم (كائن User)
         if ($user && $user->is_admin) { // لو موجود ومسجل دخول وهو admin
            return view('dashboard', compact('topDoctors','user','reservations','totalEarnings','totalToday','todayReservationsCount','latestUsers','activities','notifications'));
         }else{
            return redirect('/');
         }
        }  
    public function show(){
        return  view('addDoctors');
    }
    public function store(Request $request){
        $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|numeric',
        'address' => 'required|string',
        'price' => 'required|numeric',
        'date' => 'date',
        'appointments.*.day' => 'required|string',
        'appointments.*.time_start' => 'required',
        'appointments.*.time_end' => 'required|after:appointments.*.time_start',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('doctors', 'public');
        }
        $doctor = Doctor::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'price' => $request->price,
            'image' => $imagePath,
        ]);
        if ($request->has('appointments')) {
            foreach ($request->appointments as $appointment) {
                Appointment::create([
                    'doctor_id' => $doctor->id,
                    'day' => $appointment['day'],
                    'date' => $request->date,
                    'time_start' => $appointment['time_start'],
                    'time_end' => $appointment['time_end'],
                ]);
            }
        }
        return redirect()->route('dashboard');
    }

    public function list(){
        $doctors = Doctor::paginate(5);
        $appointments = Appointment::all();
        return  view('ListOfDoc',compact('doctors','appointments') );
    }

    public function getAppointments($doctorId)
{
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

            return redirect()->route('reservation')->with('success', 'Reservation created successfully!');

    }
    public function reserveList()
{
    if (auth()->check() && auth()->user()->is_admin) {
        // Admin يشوف كل الحجوزات
        $reservations = Reservation::with(['doctor','appointment','user'])->latest()->paginate(10);
    } else {
        // المستخدم العادي يشوف حجوزاته بس
        $reservations = Reservation::with(['doctor','appointment'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
           
    }
    return view('reservation', compact('reservations'));
}

public function confirm($id)
{
    $reservation = Reservation::findOrFail($id);

    // فقط admin يقدر يعمل confirm
    if(auth()->user()->is_admin) {
        $reservation->status = 'confirmed';
        $reservation->save();

        return redirect()->back()->with('success', 'Reservation confirmed successfully!');
    }

    return redirect()->back()->with('error', 'Unauthorized action.');
}
public function cancel($id)
{
    $reservation = Reservation::findOrFail($id);
    $reservation->status = 'cancelled';
    $reservation->save();

    return redirect()->back()->with('success', 'Reservation cancelled successfully.');
}
public function search(Request $request)
{
    $query = $request->input('q'); 

    // البحث في البوستات
    $doctors = Doctor::where('name', 'like', "%{$query}%")
                 ->orWhere('address', 'like', "%{$query}%")
                 ->orWhere('price', 'like', "%{$query}%")
                 ->paginate(5);
     

            
    return view('search', compact('query', 'doctors'));
}
     public function about(){
         return  view('about');
     }

     public function AllUsers(Request $request){
        $query = $request->input('q');

         $users = User::query()->where('is_admin', false)
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'LIKE', "%{$query}%")
                         ->orWhere('phone', 'LIKE', "%{$query}%");
        })
        ->get();

    return view('AllUsers', compact('users'));
    }
    
    public function edit($id){
    $doctor = Doctor::with('appointments')->findOrFail($id);
    return view('addDoctors', compact('doctor'));
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);
    
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required',
            'address' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);
    
        $doctor->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'price' => $request->price,
        ]);
    
        foreach ($request->appointments as $appointmentData) {
            if (isset($appointmentData['id'])) {
                // Update existing
                $appointment = Appointment::find($appointmentData['id']);
                if ($appointment) {
                    $appointment->update($appointmentData);
                }
            } else {
                // Add new appointment
                $doctor->appointments()->create($appointmentData);
            }
        }
    
        // تحديث الصورة لو تم رفع واحدة جديدة
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('doctors', 'public');
            $doctor->image = $path;
            $doctor->save();
        }
    
        return redirect()->route('listDoctors')->with('success', 'Doctor updated successfully!');
    }

    public function destroy($id){
    
        $doctor = Doctor::with(['appointments', 'reservations'])->findOrFail($id);

        $doctor->appointments()->delete();
        $doctor->reservations()->delete();
        $doctor->delete();
    

    return redirect()->route('listDoctors')->with('success', 'Doctor deleted successfully!');
}

}
