<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileImageRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Chirp;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $followers = $user->followers;
        $following = $user->following;
        return Inertia::render('Profile/View', [
            'chirps' => Chirp::where('user_id', auth()->id())->with('user')->with('likes')->with('replies')->with('parent')->latest()->get(),
            'followers' => $followers,
            'following' => $following,
        ]);
    }

    public function show(User $user)
    {
        if (auth()->check() && auth()->id() === $user->id) {
            return Redirect::route('profile.view');
        }
        return Inertia::render('Profile/Show', [
            'user' => $user->load('followers', 'following'),
            'chirps' => Chirp::where('user_id', $user->id)->with('user')->with('likes')->with('replies')->with('parent')->latest()->get()
        ]);
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateProfilePicture(ProfileImageRequest $request): RedirectResponse
    {
        $user = User::find(auth()->id());

        if ($request->hasFile('profile_picture')) {
            // Store the uploaded file

            $file =  $request->file('profile_picture');
            $path = $file->store('profile_pictures', 'public');

            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Update user profile picture path
            $user->profile_picture = $path;
            $user->save();
        }

        return Redirect::route('profile.view');
    }

    public function recommendUsers(Request $request)
    {
        $user = User::find(auth()->id());

        $perPage = $request->input('per_page', 5);
        $unfollowedUsers = $user->unfollowedUsers($perPage);
        // dd($unfollowedUsers);
        return response()->json($unfollowedUsers, 201);
    }
}
