<?php

namespace App\Listeners;

use App\Events\ChirpQuoted;
use App\Notifications\ChirpQuotedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateChirpQuotedNotification
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
    public function handle(ChirpQuoted $event): void
    {
        $chirp = $event->chirp;
        $user = $event->user;
        $message = $event->message;
        $quote_chirp = $event->quote_chirp;

        $chirp->user->notify(new ChirpQuotedNotification($chirp, $user, $message, $quote_chirp));
    }
}
