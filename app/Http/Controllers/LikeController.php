<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use App\Models\Like;
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

        $chirps = Chirp::with('user:id,name')->with('likes')->latest()->get();

        return response()->json($chirps, 201);
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
    public function destroy(Chirp $chirp)
    {
        $chirp->likes()->where('user_id', auth()->id())->delete();

        $chirps = Chirp::with('user:id,name')->with('likes')->latest()->get();
        return response()->json($chirps, 201);
    }
}
