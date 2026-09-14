<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MyGroup;
use App\Models\GroupMembership;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index($groupId)
    {
        $user = Auth::user();
        
        // Check if user is in this group
        $membership = GroupMembership::where('user_id', $user->id)
            ->where('group_id', $groupId)
            ->first();

        if (!$membership) {
            return redirect()->route('dashboard')->with('error', 'You are not a member of this group.');
        }

        $isAdmin = $membership->is_admin;

        // Fetch the group object
        $group = MyGroup::where('group_id', $groupId)->first();

        // Fetch messages with user and admin status
        $messages = Message::with(['user', 'group'])
            ->where('group_id', $groupId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                $message->is_admin = GroupMembership::where('user_id', $message->user_id)
                    ->where('group_id', $message->group_id)
                    ->value('is_admin');
                return $message;
            });

        return view('chat.index', [
            'messages' => $messages,
            'groupId' => $groupId,
            'isAdmin' => $isAdmin,
            'group' => $group,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            // Check if user is in this group
            $membership = GroupMembership::where('user_id', $user->id)
                ->where('group_id', $request->group_id)
                ->first();

            if (!$membership) {
                return response()->json([
                    'success' => false,
                    'error' => 'You are not a member of this group.'
                ], 403);
            }

            $request->validate([
                'message' => 'required|string|max:1000',
                'group_id' => 'required|exists:my_group,group_id'
            ]);

            $message = Message::create([
                'user_id' => Auth::id(),
                'group_id' => $request->group_id,
                'message' => $request->message
            ]);

            // Load the user relationship
            $message->load('user');

            $isAdmin = $membership->is_admin;

            // Create and broadcast the event
            $event = new NewChatMessage($message, $isAdmin);

            broadcast($event)->toOthers();

            return response()->json([
                'success' => true,
                'message' => $message->load('user')
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ChatController@store: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to send message'
            ], 500);
        }
    }
} 