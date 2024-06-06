<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $notifications = $user->notifications()->paginate(5);
        $user->unreadNotifications->markAsRead();
        return response()->json($notifications, 201);
    }

    public function unread(): JsonResponse
    {
        $user = auth()->user();
        $unreadNotifications = $user->unreadNotifications;
        // $user->unreadNotifications->markAsRead();
        return response()->json($unreadNotifications, 201);
    }
}
