<?php

namespace App\Listeners;

use App\Events\Unfollowed;
use App\Notifications\FollowedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteFollowedNotification
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
    public function handle(Unfollowed $event): void
    {
        $following = $event->following;
        $user = $event->user;
        $notification = $following->notifications()
            ->where('type', FollowedNotification::class)
            ->where('data->following_id', $following->id)
            ->where('data->user_id', $user->id)
            ->first();
        if ($notification) {
            $notification->delete();
        }
    }
}
