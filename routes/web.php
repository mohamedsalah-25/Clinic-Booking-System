<?php

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AdminController;
use App\Models\Reservation;
use Carbon\Carbon;


Route::get('/', [DoctorController::class, 'index']); 
Route::get('listofdoctors', [DoctorController::class, 'list'])->name('listDoctors'); 
Route::get('/doctor/{doctorId}/appointments', [DoctorController::class, 'getAppointments']);
Route::get('makeAppointment/{doctor?}', [DoctorController::class, 'booking'])->name('makeAppointment'); 
Route::get('/search', [DoctorController::class, 'search'])->name('search');
Route::get('about', [DoctorController::class, 'about'])->name('about');  
Route::post('/reservation/store', [DoctorController::class, 'reserve'])->name('reservations.store');
Route::get('/reservations/{doctor?}', [DoctorController::class, 'reserveList'])->name('reservation');

// Admin Routes
Route::get('dashboard', [AdminController::class, 'Admin'])->middleware('admin')->name('dashboard'); 
    
Route::get('adddoctors', [AdminController::class, 'show'])->middleware('admin')->name('showDoctors');  
Route::post('adddoctors', [AdminController::class, 'store'])->middleware('admin')->name('doctors.store');  

Route::get('/addDoctors/{id}/edit', [AdminController::class, 'edit'])->name('doctor.edit');
Route::put('/addDoctors/{id}', [AdminController::class, 'update'])->name('doctor.update');
Route::delete('/addDoctors/{id}', [AdminController::class, 'destroy'])->name('doctor.destroy');

Route::patch('/reservations/{id}/confirm', [AdminController::class, 'confirm'])->name('reservations.confirm')->middleware('auth');
Route::patch('/reservations/{id}/cancel', [AdminController::class, 'cancel'])->name('reservations.cancel')->middleware('auth');

Route::get('allusers', [AdminController::class, 'AllUsers'])->middleware('admin')->name('AllUsers'); 
Route::delete('allusers/{id}', [AdminController::class, 'userDelete'])->middleware('admin')->name('user.delete'); 

