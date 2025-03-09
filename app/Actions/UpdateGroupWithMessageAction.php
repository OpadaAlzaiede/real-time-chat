<?php

namespace App\Actions;

use App\Models\Group;
use App\Models\Message;

class UpdateGroupWithMessageAction {

    public function handle(int $groupId, Message $message): void {

        Group::query()
            ->updateOrCreate(
                ['id' => $groupId],
                ['last_message_id' => $message->id]
            );
    }
}
