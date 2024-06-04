<?php

use App\Http\Controllers\ChirpController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RechirpController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\SearchController;
use App\Models\Chirp;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
    // return xdebug_info();
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/recommend/users', [ProfileController::class, 'recommendUsers'])->name('users.recommend');
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/account', [ProfileController::class, 'index'])->name('profile.view');
    Route::get('/account/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('profile/picture/update', [ProfileController::class, 'updateProfilePicture'])->name('profilePicture.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Likes
    Route::post('/chirps/{chirp}/like', [LikeController::class, 'store'])->name('chirps.like');
    Route::delete('/chirps/{chirp}/unlike', [LikeController::class, 'destroy'])->name('chirps.unlike');

    // Rechirp
    Route::post('/chirp/{chirp}/rechirp', [RechirpController::class, 'store'])->name('chirp.rechirp');
    Route::delete('/chirp/{chirp}/unrechirp', [RechirpController::class, 'destroy'])->name('chirp.unrechirp');

    // Follow
    Route::post('/account/{user}/follow', [FollowController::class, 'follow'])->name('user.follow');
    Route::delete('/account/{user}/unfollow', [FollowController::class, 'unfollow'])->name('user.unfollow');

    //Chirps
    Route::get('/chirp/{chirp}', function (Chirp $chirp) {
        return Inertia::render('ChirpPage', ['chirp' => $chirp->load(['user', 'likes', 'replies', 'parent', 'media', 'rechirps'])]);
    })->name('chirp.show');
    Route::get('/chirp/{chirp}/data', [ChirpController::class, 'show'])->name('chirp.show.data');
    Route::post('/chirp/{chirp}/reply', [ChirpController::class, 'reply'])->name('chirp.reply');

    // Accounts
    Route::get('/profile/chirps', [ChirpController::class, 'showMyChirps'])->name('chirps.showMine');
    Route::get('/account/{user}/chirps', [ChirpController::class, 'showUserChirps'])->name('profile.chirps.show');

    // Notifications
    Route::get('/notifications/get', [NotificationController::class, 'index'])->name('notifications.get');
    Route::get('/notifications/get/unread', [NotificationController::class, 'unread'])->name('notifications.get.unread');
    Route::get('/notifications', function () {
        return Inertia::render('Notifications');
    })->name('notifications.show');
});



Route::resource('chirps', ChirpController::class)
    ->only(['index', 'store', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

require __DIR__ . '/auth.php';
