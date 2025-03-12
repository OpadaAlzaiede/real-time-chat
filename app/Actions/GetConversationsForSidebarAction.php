<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Collection;

class GetConversationsForSidebarAction {

    public function __construct(
        protected GetGroupConversationsAction $getGroupConversationsAction,
        protected GetUserConversationsAction $getUserConversationsAction
    ) {
        //
    }

    public function handle(?User $user): Collection {

        $userConversations = $this->getUserConversationsAction->handle($user);
        $groupConversations = $this->getGroupConversationsAction->handle($user);

        return $userConversations->map(function(User $user) {
            return $user->toConversationArray();
        })->concat($groupConversations->map(function(Group $group) {
            return $group->toConversationArray();
        }));
    }
}
