<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use App\Events\SocketMessageEvent;
use App\Http\Resources\MessageResource;
use App\Repositories\MessageRepository;
use App\Http\Requests\StoreMessageRequest;

class MessageController extends Controller
{
    public function __construct(
        protected MessageRepository $messageRepository
    ) {
        //
    }

    public function byUser(User $user) {

        $messages = $this->messageRepository->getMessagesByUser($user);

        return inertia('Home', [
            'selectedConversation' => $user->toConversationArray(),
            'messages' => MessageResource::collection($messages)
        ]);
    }

    public function byGroup(Group $group) {

        $messages = $this->messageRepository->getMessagesByGroup($group);

        return inertia('Home', [
            'selectedConversation' => $group->toConversationArray(),
            'messages' => MessageResource::collection($messages)
        ]);
    }

    public function loadOlder(Message $message) {

        $messages = $this->messageRepository->loadOlder($message);

        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request) {

        $data = $request->validated();

        $message = $this->messageRepository->store($data);

        SocketMessageEvent::dispatch($message);

        return MessageResource::make($message);
    }

    public function destroy(Message $message) {

        if($message->sender_id !== auth()->id()) {
            return response()->json(['message' => 'You are not authorized to delete this message'], 403);
        }

        $this->messageRepository->delete($message);

        return response()->json(['message' => 'Message deleted successfully'], 200);
    }
}
