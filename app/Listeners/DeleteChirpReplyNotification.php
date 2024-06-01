<?php

namespace App\Listeners;

use App\Events\ChirpReplied;
use App\Events\ReplyDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteChirpReplyNotification
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
    public function handle(ReplyDeleted $event): void
    {
        $chirp = $event->chirp;
        $user = $event->user;
        $notification = $chirp->user->notifications()
            ->where('type', ChirpReplied::class)
            ->where('data->chirp_id', $chirp->id)
            ->where('data->user_id', $user->id)
            ->first();

        if ($notification) {
            $notification->delete();
        }
    }
}
