<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function search(Request $request): Response
    {
        $query = $request->input('query');

        $users = User::where('id', '!=', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('username', 'LIKE', "%{$query}%");
            })->with('followers')->get();

        $chirps = Chirp::where('message', 'LIKE', "%{$query}%")
            ->with('user:id,name,profile_picture')
            ->with('likes')
            ->latest()
            ->get();

        return Inertia::render('SearchResult', [
            'query' => $query,
            'users' => $users,
            'chirps' => $chirps
        ]);
    }
}