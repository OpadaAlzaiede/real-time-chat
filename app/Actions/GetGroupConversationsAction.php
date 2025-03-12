<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Collection;
use App\Repositories\GroupRepository;

class GetGroupConversationsAction {

    public function __construct(
        protected GroupRepository $groupRepository
    ) {
        //
    }

    public function handle(?User $user): Collection {

        if(!$user) {
            return collect([]);
        }

        return $this->groupRepository->getUserGroupsConversations($user);
    }
}
