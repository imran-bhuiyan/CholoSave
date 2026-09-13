<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('group.{groupId}', function ($user, $groupId) {
    // Check if user is a member of the group
    return \App\Models\GroupMembership::where('user_id', $user->id)
        ->where('group_id', $groupId)
        ->exists();
});