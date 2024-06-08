<?php

namespace App\Listeners;

use App\Events\ChirpQuoteDeleted;
use App\Notifications\ChirpQuotedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteChirpQuotedNotification
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
    public function handle(ChirpQuoteDeleted $event): void
    {
        $chirp = $event->chirp;
        $user = $event->user;
        $notification = $chirp->user->notifications()
            ->where('type', ChirpQuotedNotification::class)
            ->where('data->chirp_id', $chirp->id)
            ->where('data->user_id', $user->id)
            ->first();

        if ($notification) {
            $notification->delete();
        }
    }
}
