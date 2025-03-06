<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserTwitchSubscription;
use App\Enums\TwitchSubscription;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        $user = User::first();

        UserTwitchSubscription::insert([
            "user_id" => $user->id,
            "broadcaster_id" => "123456789",
            "twitch_subscription" => TwitchSubscription::Tier1,
        ]);

        UserTwitchSubscription::insert([
            "user_id" => $user->id,
            "broadcaster_id" => "987654321",
            "twitch_subscription" => TwitchSubscription::Tier3,
        ]);

        $questions = [];
        for ($i = 0; $i < 100; $i++) {
            $questions[] = [
                "user_id" => $user->id,
                "question" => fake()->sentence(),
                "created_at" => now(),
                "updated_at" => now(),
            ];
        }
        DB::table("questions")->insert($questions);
    }
}
