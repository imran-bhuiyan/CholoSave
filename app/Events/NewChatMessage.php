<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $groupId;
    public $userId;
    public $username;
    public $isAdmin;

    public function __construct(Message $message, $isAdmin)
    {
        $this->message = $message;
        $this->groupId = $message->group_id;
        $this->userId = $message->user_id;
        $this->username = $message->user->name;
        $this->isAdmin = $isAdmin;

        Log::info('NewChatMessage event constructed', [
            'message_id' => $message->id,
            'group_id' => $this->groupId,
            'user_id' => $this->userId,
            'username' => $this->username,
            'is_admin' => $this->isAdmin
        ]);
    }

    public function broadcastOn()
    {
        Log::info('Broadcasting on channel: group.' . $this->groupId);
        return new Channel('group.' . $this->groupId);
    }

    public function broadcastAs()
    {
        return 'new-message';
    }

    public function broadcastWith()
    {
        $data = [
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at->format('h:i A'),
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'profile_picture' => $this->message->user->profile_picture
                ]
            ],
            'user_id' => $this->userId,
            'username' => $this->username,
            'is_admin' => $this->isAdmin,
            'created_at' => $this->message->created_at->format('h:i A')
        ];

        Log::info('Broadcasting with data:', $data);
        return $data;
    }
} 