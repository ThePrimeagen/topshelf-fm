<?php

namespace Database\Factories;

use App\Enums\TwitchSubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserTwitchSubscription>
 */
class UserTwitchSubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'broadcaster_id' => User::getBroadcasterID(),
            'twitch_subscription' => TwitchSubscription::Tier1->value,
        ];
    }
}
