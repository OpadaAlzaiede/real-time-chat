<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

class GroupRepository {

    public function getUserGroupsConversations(User $user): Collection {

        return Group::query()
            ->select([
                'groups.*',
                'messages.message as last_message',
                'messages.created_at as last_message_date',
            ])
            ->join('group_user', 'group_user.group_id', '=', 'groups.id')
            ->leftJoin('messages', 'messages.id', '=', 'groups.last_message_id')
            ->where('group_user.user_id', '=', $user->id)
            ->orderBy('messages.created_at', 'DESC')
            ->orderBy('groups.name')
            ->get();
    }

    public function updateGroupWithMessage(int $groupId, Message $message): void {

        Group::query()
            ->updateOrCreate(
                ['id' => $groupId],
                ['last_message_id' => $message->id]
            );
    }
}
