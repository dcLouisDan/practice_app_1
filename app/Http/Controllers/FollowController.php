<?php

namespace App\Http\Controllers;

use App\Events\Followed;
use App\Events\Unfollowed;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(User $user)
    {
        auth()->user()->following()->attach($user->id);
        event(new Followed($user, auth()->user()));

        return response()->json($user->fresh()->load(['followers', 'following']), 201);
    }

    public function unfollow(User $user)
    {
        auth()->user()->following()->detach($user->id);
        event(new Unfollowed($user, auth()->user()));
        return response()->json($user->fresh()->load(['followers', 'following']), 201);
    }
}
