<?php

namespace App\Listeners;

use App\Events\ChirpDisliked;
use App\Events\ChirpLiked;
use App\Notifications\ChirpLikedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteChirpLikeNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ChirpDisliked $event): void
    {
        $chirp = $event->chirp;
        $user = $event->user;
        $notification = $chirp->user->notifications()
            ->where('type', ChirpLikedNotification::class)
            ->where('data->chirp_id', $chirp->id)
            ->where('data->user_id', $user->id)
            ->first();
        // dd($notification);
        if ($notification) {
            $notification->delete();
        }
    }
}
