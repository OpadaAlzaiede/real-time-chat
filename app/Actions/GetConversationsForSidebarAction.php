<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Collection;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Repositories\GroupRepository;

class GetConversationsForSidebarAction {

    public function __construct(
        protected UserRepository $userRepository,
        protected GroupRepository $groupRepository
    ) {
        //
    }

    public function handle(): array|Collection {

        $user = Auth::user();

        if(!$user) {
            return [];
        }

        $users = $this->userRepository->getUserConversations($user);
        $groups = $this->groupRepository->getUserGroupsConversations($user);

        return $users->map(function(User $user) {
            return $user->toConversationArray();
        })->concat($groups->map(function(Group $group) {
            return $group->toConversationArray();
        }));
    }
}
