<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('target_user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Mark notifications as read
        Notification::where('target_user_id', auth()->id())
            ->where('status', 'unread')
            ->update(['status' => 'read']);

        return view('groups.notifications', compact('notifications'));
    }

    public function getUnreadCount()
    {
        $count = Notification::where('target_user_id', auth()->id())
            ->where('status', 'unread')
            ->count();

        return response()->json(['count' => $count]);
    }
} 