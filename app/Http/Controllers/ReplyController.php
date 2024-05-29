<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use App\Models\Reply;
use Illuminate\Http\Request;

class ReplyController extends Controller
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
    public function store(Request $request, Chirp $chirp)
    {
        $request->validate([
            'message' => 'required|max:255',
            'parent_id' => 'nullable|exists:replies,id'
        ]);

        $reply = new Reply([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'parent_id' => $request->parent_id,
        ]);

        $chirp->replies()->save($reply);

        return response()->json($chirp->refresh()->load(['user', 'likes', 'replies']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reply $reply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reply $reply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reply $reply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reply $reply)
    {
        //
    }
}
