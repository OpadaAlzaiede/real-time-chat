<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository {

    public function getUserConversations(User $user): Collection {

        $userId = $user->id;

        return User::query()
            ->select([
                'users.*',
                'messages.message as last_message',
                'messages.created_at as last_message_date',
            ])
            ->where('users.id', '!=', $userId)
            ->when(!$user->is_admin, function($query) {
                $query->whereNull('blocked_at');
            })
            ->leftJoin('conversations', function($join) use ($userId) {
                $join->on('conversations.user_one_id', '=', 'users.id')
                    ->where('conversations.user_two_id', '=', $userId)
                    ->orWhere(function($query) use($userId){
                        $query->on('conversations.user_one_id', '=', 'users.id')
                            ->where('conversations.user_two_id', '=', $userId);
                    });
            })
            ->leftJoin('messages', 'messages.id', '=', 'conversations.last_message_id')
            ->orderByRaw('IFNULL(users.blocked_at, 1)')
            ->orderBy('messages.created_at', 'DESC')
            ->orderBy('users.name')
            ->get();
    }
}
