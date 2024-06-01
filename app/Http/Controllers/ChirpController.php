<?php

namespace App\Http\Controllers;

use App\Events\ChirpReplied;
use App\Events\ReplyDeleted;
use App\Models\Chirp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;
use Inertia\Inertia;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $authUserId = auth()->id();

        $ownChirps = Chirp::where('user_id', $authUserId)->with('user')->with('likes')->with('replies')->with('parent')->latest()->get()->toArray();

        $followedChirps = Chirp::whereIn('user_id', function ($query) use ($authUserId) {
            $query->select('following_id')
                ->from('followers')
                ->where('follower_id', $authUserId);
        })->with('user')->with('likes')->with('replies')->with('parent')->latest()->get()->toArray();

        $chirps = array_merge($ownChirps, $followedChirps);
        usort($chirps, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });


        return response()->json($chirps, 201);
    }

    public function reply(Request $request, Chirp $chirp)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:chirps,id'
        ]);

        $request->user()->chirps()->create($validated);
        $chirp->refresh()->load(['likes', 'user', 'replies', 'parent']);

        if ($chirp->user->id !== auth()->id()) {
            event(new ChirpReplied($chirp, auth()->user(), $validated['message']));
        }

        return response()->json($chirp, 201);
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
            'message' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:chirps,id'
        ]);

        $request->user()->chirps()->create($validated);

        return redirect(route('dashboard'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        $chirp = $chirp->load(['user', 'replies', 'likes', 'parent']);

        return response()->json($chirp, 201);
    }

    public function showMyChirps(): JsonResponse
    {
        $chirps = Chirp::where('user_id', auth()->id())->with('user')->with('likes')->with('replies')->with('parent')->latest()->get();

        return response()->json($chirps, 201);
    }

    public function showUserChirps(User $user): JsonResponse
    {
        $chirps = Chirp::where('user_id', $user->id)->with('user')->with('likes')->with('replies')->with('parent')->latest()->get();

        return response()->json($chirps, 201);
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
    public function update(Request $request, Chirp $chirp)
    {
        Gate::authorize('update', $chirp);

        $validated = $request->validate([
            'message' => 'required|string|max:255'
        ]);

        $chirp->update($validated);
        $context = $request->content;

        // dd($context);
        if ($context === 'reply' || $context === 'post') {
            return Inertia::location(route('chirp.show', $chirp->id));
        }

        return Inertia::location(route('dashboard'));;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Chirp $chirp)
    {
        Gate::authorize('delete', $chirp);
        $parent = $chirp->parent_id;
        $parentChirp = Chirp::find($parent);
        $chirp->delete();
        $context = $request->query('context');
        event(new ReplyDeleted($parentChirp, auth()->user()));
        if ($context === 'reply') {
            return Inertia::location(route('chirp.show', $parent));
        }

        return redirect(route('dashboard'));
    }
}
