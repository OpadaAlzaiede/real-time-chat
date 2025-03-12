<?php

namespace App\Repositories;

use App\Actions\UpdateConversationWithMessageAction;
use App\Actions\UpdateGroupWithMessageAction;
use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use App\Models\Attachment;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class MessageRepository {

    public function __construct(
        protected ConversationRepository $conversationRepository,
        protected GroupRepository $groupRepository
    ) {
        //
    }

    public function getMessagesByUser(User $user): LengthAwarePaginator {

        return Message::query()
                ->where('sender_id', auth()->id())
                ->where('receiver_id', $user->id)
                ->orWhere(function($query) use($user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', auth()->id());
                })
                ->latest()
                ->paginate(10);
        }

    public function getMessagesByGroup(Group $group): LengthAwarePaginator {

        return Message::query()
            ->where('group_id', $group->id)
            ->latest()
            ->paginate(10);
    }

    public function loadOlder(Message $message): LengthAwarePaginator {

        $query = Message::query()
                        ->where('created_at', '<', $message->created_at);

        if($message->group_id) {
            $query->where('group_id', $message->group_id);
        }else {
            $query->where('sender_id', $message->sender_id)
                    ->where('receiver_id', $message->receiver_id)
                    ->orWhere(function($query) use($message) {
                        $query->where('sender_id', $message->receiver_id)
                            ->where('receiver_id', $message->sender_id);
                    });
        }

        return $query->latest()
                    ->paginate(10);
    }

    public function store(array $data): Message {

        $data['sender_id'] = auth()->id();
        $receiverId = $data['receiver_id'] ?? null;
        $groupId = $data['group_id'] ?? null;
        $files = $data['attachments'] ?? [];
        $message = Message::create($data);

        $attachments = [];
        if($files) {
            foreach($files as $file) {
                $directory = 'attachments/' . Str::random(32);
                Storage::makeDirectory($directory);

                $attachment = Attachment::create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $file->store($directory, 'public'),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'message_id' => $message->id
                ]);
                $attachments[] = $attachment;
            }
            $message->attachments()->saveMany($attachments);
        }

        if($receiverId) {
            $this->conversationRepository->updateConversationWithMessage($receiverId, auth()->id(), $message);
        }

        if($groupId) {
            $this->groupRepository->updateGroupWithMessage($groupId, $message);
        }

        return $message;
    }

    public function delete(Message $message) {

        $message->delete();
    }
}
