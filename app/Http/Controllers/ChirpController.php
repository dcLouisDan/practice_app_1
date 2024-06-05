<?php

namespace App\Http\Controllers;

use App\Events\ChirpReplied;
use App\Events\ReplyDeleted;
use App\Models\Chirp;
use App\Models\ChirpMedia;
use App\Models\Rechirp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Inertia\Inertia;

class ChirpController extends Controller
{
    private $chirpRelations = ['user', 'likes', 'replies', 'parent', 'media', 'rechirps'];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $authUserId = auth()->id();
        $rechirpedChirps = [];
        $allChirps = Chirp::where(function ($query) use ($authUserId) {
            $query->where('user_id', $authUserId)
                ->orWhereIn('user_id', function ($subQuery) use ($authUserId) {
                    $subQuery->select('following_id')
                        ->from('followers')
                        ->where('follower_id', $authUserId);
                });
        })->with($this->chirpRelations)
            ->latest()
            ->get()->toArray();

        $rechirps = Rechirp::where(function ($query) use ($authUserId) {
            $query->where('user_id', $authUserId)
                ->orWhereIn('user_id', function ($subQuery) use ($authUserId) {
                    $subQuery->select('following_id')
                        ->from('followers')
                        ->where('follower_id', $authUserId);
                });
        })->with(['user', 'chirp' => function ($query) {
            $query->with($this->chirpRelations);
        }])->latest()->get();

        foreach ($rechirps as $rechirp) {
            $chirp = $rechirp->chirp;
            $chirp['rechirper'] = $rechirp->user->toArray();
            $chirp['created_at'] = $rechirp->created_at;
            $rechirpedChirps[] = $chirp->toArray();
        }

        $chirps = array_merge($allChirps, $rechirpedChirps);

        usort($chirps, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $start = ($currentPage - 1) * $perPage;
        $currentPageItems = array_slice($chirps, $start, $perPage);

        $paginatedChirps = new LengthAwarePaginator(
            $currentPageItems,
            count($chirps),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return response()->json($paginatedChirps, 201);
    }

    public function reply(Request $request, Chirp $chirp)
    {


        $media = $request->file('media');
        if ($media) {
            $validated = $request->validate([
                'message' => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:chirps,id',
                'media.*' => 'nullable|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:25600'
            ]);
            $validated['message'] = $validated['message'] ?? "";

            $images = array_filter($media, fn ($file) => str_starts_with($file->getMimeType(), 'image/'));
            $videos = array_filter($media, fn ($file) => str_starts_with($file->getMimeType(), 'video/'));

            if (count($videos) > 1) {
                return back()->withErrors(['media' => 'You can only upload one video']);
            }

            if (count($videos) > 1 && count($images) > 0) {
                return back()->withErrors(['media' => 'You can only multiple images or one video, but not both']);
            }
        } else {
            $validated = $request->validate([
                'message' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:chirps,id',
                'media.*' => 'nullable|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:25600'
            ]);
        }
        if (isset($validated['parent_id'])) {
            $chirpNew = $request->user()->chirps()->create([
                'message' => $validated['message'],
                'parent_id' => $validated['parent_id'],
            ]);
        } else {
            $chirpNew = $request->user()->chirps()->create([
                'message' => $validated['message'],
            ]);
        }

        if ($media) {
            foreach ($request->file('media') as $file) {
                $mediaPath = $file->store('chirp_media', 'public');
                $mediaType = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';

                ChirpMedia::create([
                    'chirp_id' => $chirpNew->id,
                    'media_path' => $mediaPath,
                    'media_type' => $mediaType
                ]);
            }
        }
        $chirp->refresh()->load($this->chirpRelations);
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
    public function store(Request $request): JsonResponse
    {


        $media = $request->file('media');
        if ($media) {
            $validated = $request->validate([
                'message' => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:chirps,id',
                'media.*' => 'nullable|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:25600'
            ]);
            $validated['message'] = $validated['message'] ?? "";
            $images = array_filter($media, fn ($file) => str_starts_with($file->getMimeType(), 'image/'));
            $videos = array_filter($media, fn ($file) => str_starts_with($file->getMimeType(), 'video/'));

            if (count($videos) > 1) {
                return back()->withErrors(['media' => 'You can only upload one video']);
            }

            if (count($videos) > 1 && count($images) > 0) {
                return back()->withErrors(['media' => 'You can only multiple images or one video, but not both']);
            }
        } else {
            $validated = $request->validate([
                'message' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:chirps,id',
                'media.*' => 'nullable|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:25600'
            ]);
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

        return response()->json($chirp->load($this->chirpRelations), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        $chirp = $chirp->load($this->chirpRelations);

        return response()->json($chirp, 201);
    }

    public function showMyChirps(): JsonResponse
    {
        $chirps = Chirp::where('user_id', auth()->id())->with($this->chirpRelations)->latest()->get();

        return response()->json($chirps, 201);
    }

    public function showUserChirps(User $user): JsonResponse
    {
        $chirps = Chirp::where('user_id', $user->id)->with($this->chirpRelations)->latest()->get();

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
        if ($context === 'profile') {
            return Inertia::location(route('profile.view'));
        }

        return redirect(route('dashboard'));
    }
}
