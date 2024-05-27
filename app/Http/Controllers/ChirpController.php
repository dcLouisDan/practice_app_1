<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;
use Inertia\Inertia;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $authUserId = auth()->id();

        $ownChirps = Chirp::where('user_id', $authUserId)->with('user:id,name,profile_picture')->with('likes')->latest()->get()->toArray();

        $followedChirps = Chirp::whereIn('user_id', function ($query) use ($authUserId) {
            $query->select('following_id')
                ->from('followers')
                ->where('follower_id', $authUserId);
        })->with('user:id,name,profile_picture')->with('likes')->latest()->get()->toArray();

        $chirps = array_merge($ownChirps, $followedChirps);
        usort($chirps, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return Inertia::render('Dashboard', [
            'chirps' => $chirps
        ]);
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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255'
        ]);

        $request->user()->chirps()->create($validated);

        return redirect(route('dashboard'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp): RedirectResponse
    {
        Gate::authorize('update', $chirp);

        $validated = $request->validate([
            'message' => 'required|string|max:255'
        ]);

        $chirp->update($validated);

        return redirect(route('dashboard'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp): RedirectResponse
    {
        Gate::authorize('delete', $chirp);

        $chirp->delete();

        return redirect(route('dashboard'));
    }
}
