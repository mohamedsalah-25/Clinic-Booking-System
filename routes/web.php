<?php

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Models\Reservation;
use Carbon\Carbon;

Route::get('/', [DoctorController::class, 'index']); 

/*Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('', function () {
   
    });
    });
*/
Route::get('dashboard', [DoctorController::class, 'Admin'])->middleware('admin')->name('dashboard'); 

    
Route::get('adddoctors', [DoctorController::class, 'show'])->middleware('admin')->name('showDoctors');  
Route::post('adddoctors', [DoctorController::class, 'store'])->middleware('admin')->name('doctors.store');  

Route::get('listofdoctors', [DoctorController::class, 'list'])->name('listDoctors'); 

Route::get('/addDoctors/{id}/edit', [DoctorController::class, 'edit'])->name('doctor.edit');
Route::put('/addDoctors/{id}', [DoctorController::class, 'update'])->name('doctor.update');
Route::delete('/addDoctors/{id}', [DoctorController::class, 'destroy'])->name('doctor.destroy');



Route::get('/doctor/{doctorId}/appointments', [DoctorController::class, 'getAppointments']);

Route::get('makeAppointment/{doctor?}', [DoctorController::class, 'booking'])->name('makeAppointment'); 
//Route::get('reservation', [DoctorController::class, 'reserve'])->name('reservation'); 

Route::post('/reservation/store', [DoctorController::class, 'reserve'])->name('reservations.store');
Route::get('/reservations/{doctor?}', [DoctorController::class, 'reserveList'])->name('reservation');
Route::patch('/reservations/{id}/confirm', [DoctorController::class, 'confirm'])
    ->name('reservations.confirm')
    ->middleware('auth');
Route::patch('/reservations/{id}/cancel', [DoctorController::class, 'cancel'])
    ->name('reservations.cancel')
    ->middleware('auth');

Route::get('/search', [DoctorController::class, 'search'])->name('search');

Route::get('about', [DoctorController::class, 'about'])->name('about');  
Route::get('allusers', [DoctorController::class, 'AllUsers'])->middleware('admin')->name('AllUsers'); 
