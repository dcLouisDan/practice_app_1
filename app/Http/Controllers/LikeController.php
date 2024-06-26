<?php

namespace App\Http\Controllers;

use App\Events\ChirpDisliked;
use App\Events\ChirpLiked;
use App\Models\Chirp;
use App\Models\Like;
use App\Notifications\ChirpLikedNotification;
use Illuminate\Http\Request;

class LikeController extends Controller
{

    public function unlike(Chirp $chirp)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Chirp $chirp)
    {
        if ($chirp->likes()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Already Liked'], 422);
        }

        $chirp->likes()->create([
            'user_id' => auth()->id(),
        ]);

        $chirp->refresh();
        if ($chirp->user->id !== auth()->id()) {
            event(new ChirpLiked($chirp, auth()->user()));
        }
        return response()->json($chirp->load(['user', 'likes', 'replies', 'parent', 'media', 'rechirps']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Like $like)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Like $like)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Like $like)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Chirp $chirp)
    {
        if ($chirp->user->id !== auth()->id()) {
            event(new ChirpDisliked($chirp, auth()->user()));
        }
        $chirp->likes()->where('user_id', auth()->id())->delete();
        $chirp->refresh();

        return response()->json($chirp->load(['user', 'likes', 'replies', 'parent', 'media', 'rechirps']), 201);
    }
}
