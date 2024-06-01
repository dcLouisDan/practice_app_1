<?php

namespace App\Listeners;

use App\Events\Followed;
use App\Notifications\FollowedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateFollowedNotification
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
    public function handle(Followed $event): void
    {
        $following = $event->following;
        $user = $event->user;

        $following->notify(new FollowedNotification($following, $user));
    }
}
