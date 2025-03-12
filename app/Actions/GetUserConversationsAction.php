<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Collection;
use App\Repositories\UserRepository;

class GetUserConversationsAction {

    public function __construct(
        protected UserRepository $userRepository
    ) {
        //
    }

    public function handle(?User $user): Collection {

        if(!$user) {
            return collect([]);
        }

        return $this->userRepository->getUserConversations($user);
    }
}
