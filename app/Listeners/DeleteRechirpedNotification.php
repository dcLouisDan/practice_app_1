<?php

namespace App\Listeners;

use App\Events\Unrechirped;
use App\Notifications\RechirpedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteRechirpedNotification
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
    public function handle(Unrechirped $event): void
    {
        $chirp = $event->chirp;
        $user = $event->user;

        $notification = $chirp->user->notifications()
            ->where('type', RechirpedNotification::class)
            ->where('data->chirp_id', $chirp->id)
            ->where('data->user_id', $user->id)
            ->first();
        // dd($notification);
        if ($notification) {
            $notification->delete();
        }
    }
}
