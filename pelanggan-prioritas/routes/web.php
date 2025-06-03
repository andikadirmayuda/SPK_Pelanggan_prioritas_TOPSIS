<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\SubKriteriaController;
use App\Http\Controllers\PenilaianController;
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
    
    // User Management Routes (hanya untuk admin)
    Route::middleware(['can:manage-users'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Pelanggan Routes
    Route::resource('pelanggan', PelangganController::class);

    // Kriteria Management Routes
    Route::resource('kriteria', KriteriaController::class)->parameters([
        'kriteria' => 'kriteria'
    ]);

    // Sub Kriteria Management Routes
    Route::resource('sub-kriteria', SubKriteriaController::class)->parameters([
        'sub-kriteria' => 'sub_kriteria'
    ]);

    // Penilaian Routes
    Route::resource('penilaian', PenilaianController::class);
    Route::get('/get-sub-kriteria/{kriteria_id}', [PenilaianController::class, 'getSubKriteria'])->name('get-sub-kriteria');
});

require __DIR__.'/auth.php';