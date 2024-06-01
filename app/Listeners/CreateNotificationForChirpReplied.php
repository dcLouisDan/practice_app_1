<?php

namespace App\Listeners;

use App\Events\ChirpReplied;
use App\Notifications\ChirpRepliedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateNotificationForChirpReplied
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
    public function handle(ChirpReplied $event): void
    {
        $chirp = $event->chirp;
        $user = $event->user;
        $message = $event->message;

        $chirp->user->notify(new ChirpRepliedNotification($chirp, $user, $message));
    }
}
