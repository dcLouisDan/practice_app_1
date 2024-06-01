<?php

namespace App\Listeners;

use App\Events\ChirpLiked;
use App\Notifications\ChirpLikedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateNotificationForChirpLiked
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
    public function handle(ChirpLiked $event): void
    {
        $chirp = $event->chirp;
        $user = $event->user;
        $chirp->user->notify(new ChirpLikedNotification($chirp, $user));
    }
}
