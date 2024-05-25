<?php

use App\Http\Controllers\ChirpController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return Inertia::render('Auth/Login', [
        'canResetPassword' => true
    ]);
});

Route::get('/dashboard', [ChirpController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/account', [ProfileController::class, 'index'])->name('profile.view');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/chirps/{chirp}/like', [LikeController::class, 'store'])->name('chirps.like');
    Route::delete('/chirps/{chirp}/unlike', [LikeController::class, 'destroy'])->name('chirps.unlike');
});

Route::resource('chirps', ChirpController::class)
    ->only(['index', 'store', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

require __DIR__ . '/auth.php';
