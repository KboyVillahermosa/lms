<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegistrarController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Role-based dashboards (organized controllers/views)
Route::middleware(['auth','role:student'])->get('/dashboard/student', [StudentController::class, 'index'])->name('dashboard.student');
Route::middleware(['auth','role:instructor'])->get('/dashboard/instructor', [InstructorController::class, 'index'])->name('dashboard.instructor');
Route::middleware(['auth','role:admin'])->get('/dashboard/admin', [AdminController::class, 'index'])->name('dashboard.admin');
Route::middleware(['auth','role:registrar'])->get('/dashboard/registrar', [RegistrarController::class, 'index'])->name('dashboard.registrar');

require __DIR__.'/auth.php';
