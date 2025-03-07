<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isGroupMessage = $this->faker->boolean(50);

        $senderId = rand(0, 1);

        $senderId = $this->faker->randomElement(User::where('id', '!=', 1)->pluck('id')->toArray());
        $receiverId = 1;
        $groupId = null;

        if($isGroupMessage) {
            $group = $this->faker->randomElement(Group::with('users')->get());
            $groupId = $group->id;
            $senderId = $this->faker->randomElement($group->users->pluck('id')->toArray());
            $receiverId = null;
        } else {
            if ($this->faker->boolean(50)) {
                $senderId = 1;
                $receiverId = $this->faker->randomElement(User::where('id', '!=', 1)->pluck('id')->toArray());
            }
        }

        return [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'group_id' => $groupId,
            'message' => $this->faker->realText(200),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }
}
