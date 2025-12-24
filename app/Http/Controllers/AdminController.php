<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

class AdminController extends Controller
{
    public function show(){
        return  view('addDoctors');
    }

    public function Admin() {
        $reservations = Reservation::with(['doctor', 'appointment', 'user'])
        ->latest() // order by created_at descending
        ->take(5)  // limit to 5 records
        ->get();
       
        $totalEarnings = Reservation::with('doctor')
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

        public function confirm($id) {
    
            $reservation = Reservation::findOrFail($id);
        
            // فقط admin يقدر يعمل confirm
            if(auth()->user()->is_admin) {
                $reservation->status = 'confirmed';
                $reservation->save();
        
                $doctor = $reservation->doctor;
                          // Send Email
                          $user = $reservation->user;
                          $data = [
                              'name' => $user->name,
                              'message' => 'Your reservation with Dr.' . $doctor->name . ' has been confirmed , wait us to connect you soon .',
                                  ];
        
                          Mail::to($user->email)->send(new WelcomeMail($data));           
        
                return redirect()->back()->with('success', 'Reservation confirmed successfully!');
            }
        
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        public function cancel($id) {

            $reservation = Reservation::findOrFail($id);
            $reservation->status = 'cancelled';
            $reservation->save();
        
            $doctor = $reservation->doctor;
            // Send Email
            $user = $reservation->user;
            $data = [
                'name' => $user->name,
                'message' => 'Your reservation with Dr. ' . $doctor->name . ' has been cancelled ,Please schedule another appointment.        .',
                    ];
        
            Mail::to($user->email)->send(new WelcomeMail($data));     
        
            return redirect()->back()->with('success', 'Reservation cancelled successfully.');
        }

        public function edit($id) {
            
            $doctor = Doctor::with('appointments')->findOrFail($id);
            return view('addDoctors', compact('doctor'));
            }
        
        public function update(Request $request, $id)   {
            
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

            //UPDATE APPOINTMENTS
            // تصفية ال IDs الي موجودة في الفورم
            $submittedAppointmentIds = collect($request->appointments)
            ->pluck('id')
            ->filter(); 
    
        // 2. حذف المواعيد القديمة التي لم تعد موجودة في الطلب الجديد
        // يتم حذف كل موعد تابع لهذا الطبيب وغير موجود في قائمة $submittedAppointmentIds
        $doctor->appointments()
            ->whereNotIn('id', $submittedAppointmentIds)
            ->delete(); // استخدام delete() للحذف الفعلي من قاعدة البيانات

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
    public function userDelete($id){
        
        $user = User::with([ 'reservations'])->findOrFail($id);

        $user->reservations()->delete();
        $user->delete();
    

    return redirect()->route('AllUsers')->with('success', 'user deleted successfully!');
}
    }
