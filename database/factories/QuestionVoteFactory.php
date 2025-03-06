<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionVote>
 */
class QuestionVoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'user_id' => User::factory(),
            'count' => $this->faker->randomElement([1, -1]),
        ];
    }

    /**
     * Configure the model factory to create an upvote.
     *
     * @return $this
     */
    public function upvote()
    {
        return $this->state(function (array $attributes) {
            return [
                'count' => 1,
            ];
        });
    }

    /**
     * Configure the model factory to create a downvote.
     *
     * @return $this
     */
    public function downvote()
    {
        return $this->state(function (array $attributes) {
            return [
                'count' => -1,
            ];
        });
    }
} 