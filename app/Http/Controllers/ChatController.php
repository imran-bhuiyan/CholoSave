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
            Log::info('ChatController@store called', [
                'request' => $request->all(), 
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name
            ]);
            
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
            
            $isAdmin = GroupMembership::where('user_id', Auth::id())
                ->where('group_id', $request->group_id)
                ->value('is_admin');

            Log::info('Message created', [
                'message_id' => $message->id,
                'message_content' => $message->message,
                'group_id' => $request->group_id,
                'user_id' => Auth::id(),
                'is_admin' => $isAdmin
            ]);

            // Create and broadcast the event
            $event = new NewChatMessage($message, $isAdmin);
            
            Log::info('Broadcasting event', [
                'event_class' => get_class($event),
                'channel' => 'group.' . $request->group_id,
                'event_name' => 'new-message'
            ]);

            broadcast($event)->toOthers();
            
            Log::info('Message broadcast completed', [
                'message_id' => $message->id,
                'group_id' => $request->group_id,
                'user_id' => Auth::id(),
                'event_data' => [
                    'message' => $message->toArray(),
                    'user_id' => Auth::id(),
                    'username' => Auth::user()->name,
                    'is_admin' => $isAdmin
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => $message->load('user')
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ChatController@store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to send message'
            ], 500);
        }
    }
} 