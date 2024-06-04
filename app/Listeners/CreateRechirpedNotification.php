<?php

namespace App\Listeners;

use App\Events\Rechirped;
use App\Notifications\RechirpedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateRechirpedNotification
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
    public function handle(Rechirped $event): void
    {
        $chirp = $event->chirp;
        $user = $event->user;

        $chirp->user->notify(new RechirpedNotification($chirp, $user));
    }
}
