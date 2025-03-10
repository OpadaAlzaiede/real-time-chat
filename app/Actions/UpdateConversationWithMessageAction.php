<?php

namespace App\Actions;

use App\Models\Message;
use App\Models\Conversation;

class UpdateConversationWithMessageAction {

    public function handle(int $userOneId, int $userTwoId, Message $message): void {

        $conversation = Conversation::query()
                ->where([
                    ['user_one_id', '=', $userOneId],
                    ['user_two_id', '=', $userTwoId]
                ])->orWhere(function($query) use($userOneId, $userTwoId){
                    $query->where([
                        ['user_one_id', '=', $userTwoId],
                        ['user_two_id', '=', $userOneId]
                    ]);
                })
                ->first();

        if ($conversation) {
            $conversation->update(['last_message_id' => $message->id]);
        }else {
            Conversation::create([
                'user_one_id' => $userOneId,
                'user_two_id' => $userTwoId,
                'last_message_id' => $message->id
            ]);
        }
    }
}
