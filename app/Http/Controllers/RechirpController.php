<?php

namespace App\Http\Controllers;

use App\Events\Rechirped;
use App\Events\Unrechirped;
use App\Models\Chirp;
use App\Models\Rechirp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RechirpController extends Controller
{
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
    public function store(Request $request, Chirp $chirp): JsonResponse
    {
        if ($chirp->rechirps()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Already Rechirped'], 422);
        }

        $chirp->rechirps()->create([
            'user_id' => auth()->id()
        ]);

        $chirp->refresh();
        if ($chirp->user->id !== auth()->id()) {
            event(new Rechirped($chirp, auth()->user()));
        }
        return response()->json($chirp->load(['user', 'likes', 'replies', 'parent', 'media', 'rechirps']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rechirp $rechirp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rechirp $rechirp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rechirp $rechirp)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp): JsonResponse
    {
        if ($chirp->user->id !== auth()->id()) {
            event(new Unrechirped($chirp, auth()->user()));
        }
        $chirp->rechirps()->where('user_id', auth()->id())->delete();
        $chirp->refresh();

        return response()->json($chirp->load(['user', 'likes', 'replies', 'media', 'rechirps']), 201);
    }
}
