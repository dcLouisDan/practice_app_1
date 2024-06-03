<?php

namespace App\Http\Controllers;

use App\Events\ChirpReplied;
use App\Events\ReplyDeleted;
use App\Models\Chirp;
use App\Models\ChirpMedia;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
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

        $ownChirps = Chirp::where('user_id', $authUserId)->with('user')->with('likes')->with('replies')->with('parent')->with('media')->latest()->get()->toArray();

        $followedChirps = Chirp::whereIn('user_id', function ($query) use ($authUserId) {
            $query->select('following_id')
                ->from('followers')
                ->where('follower_id', $authUserId);
        })->with('user')->with('likes')->with('replies')->with('parent')->with('media')->latest()->get()->toArray();

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
            'parent_id' => 'nullable|exists:chirps,id',
            'media.*' => 'nullable|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:25600'
        ]);

        $media = $request->file('media');
        if ($media) {
            $images = array_filter($media, fn ($file) => str_starts_with($file->getMimeType(), 'image/'));
            $videos = array_filter($media, fn ($file) => str_starts_with($file->getMimeType(), 'video/'));

            if (count($videos) > 1) {
                return back()->withErrors(['media' => 'You can only upload one video']);
            }

            if (count($videos) > 1 && count($images) > 0) {
                return back()->withErrors(['media' => 'You can only multiple images or one video, but not both']);
            }
        }
        if (isset($validated['parent_id'])) {
            $chirp = $request->user()->chirps()->create([
                'message' => $validated['message'],
                'parent_id' => $validated['parent_id'],
            ]);
        } else {
            $chirp = $request->user()->chirps()->create([
                'message' => $validated['message'],
            ]);
        }

        if ($media) {
            foreach ($request->file('media') as $file) {
                $mediaPath = $file->store('chirp_media', 'public');
                $mediaType = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';

                ChirpMedia::create([
                    'chirp_id' => $chirp->id,
                    'media_path' => $mediaPath,
                    'media_type' => $mediaType
                ]);
            }
        }

        return redirect(route('dashboard'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        $chirp = $chirp->load(['user', 'replies', 'likes', 'parent', 'media']);

        return response()->json($chirp, 201);
    }

    public function showMyChirps(): JsonResponse
    {
        $chirps = Chirp::where('user_id', auth()->id())->with('user')->with('likes')->with('replies')->with('parent')->with('media')->latest()->get();

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
        if ($chirp->media) {
            foreach ($chirp->media as $media) {
                Storage::disk('public')->delete($media->media_path);
            }
        }
        $chirp->delete();
        $context = $request->query('context');
        if ($parentChirp) {
            event(new ReplyDeleted($parentChirp, auth()->user()));
        }
        if ($context === 'reply') {
            return Inertia::location(route('chirp.show', $parent));
        }

        return redirect(route('dashboard'));
    }
}
