<?php

use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserTwitchSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class VoteComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_question_creates_question_and_upvotes_it(): void
    {
        // Create a user with a Tier1 subscription
        $user = User::factory()->create();
        UserTwitchSubscription::factory()->create([
            'user_id' => $user->id,
        ]);

        // Create a topic (required for canSubmitQuestion to return true)
        Topic::factory()->create();

        // Create the Livewire component and set the user
        Volt::actingAs($user)
            ->test('vote')
            ->set('question', 'This is a test question that is longer than 10 characters')
            ->call('saveQuestion');

        $question = Question::whereUserId($user->id)->first();
        $this->assertNotNull($question);
        $this->assertEquals('This is a test question that is longer than 10 characters', $question->question);
        $this->assertEquals($user->id, $question->user_id);

        $this->assertDatabaseHas('questions', [
            'user_id' => $user->id,
            'question' => 'This is a test question that is longer than 10 characters',
        ]);

        // Get the created question
        $question = Question::where('user_id', $user->id)->first();

        // Assert that an upvote was created for the question
        $this->assertDatabaseHas('question_votes', [
            'user_id' => $user->id,
            'question_id' => $question->id,
            'count' => 1,
        ]);
    }

    public function test_save_question_validates_question_length(): void
    {
        // Create a user with a Tier1 subscription
        $user = User::factory()->create();
        UserTwitchSubscription::factory()->create([
            'user_id' => $user->id,
        ]);

        // Create a topic (required for canSubmitQuestion to return true)
        Topic::factory()->create();

        // Test with a question that's too short
        Livewire::actingAs($user)
            ->test('vote')
            ->set('question', 'Too short')
            ->call('saveQuestion')
            ->assertHasErrors(['question' => 'min']);

        // Test with a question that's too long (over 420 characters)
        $longQuestion = str_repeat('a', 421);
        Livewire::actingAs($user)
            ->test('vote')
            ->set('question', $longQuestion)
            ->call('saveQuestion')
            ->assertHasErrors(['question' => 'max']);
    }

    public function test_save_question_enforces_question_limit(): void
    {
        // Create a user with a Tier1 subscription (max 1 question)
        $user = User::factory()->create();
        UserTwitchSubscription::factory()->create([
            'user_id' => $user->id,
        ]);

        // Create a topic (required for canSubmitQuestion to return true)
        Topic::factory()->create();

        // Create an existing question for the user
        Question::factory()->create([
            'user_id' => $user->id,
            'question' => 'Existing question',
        ]);

        // Test that user can't create another question
        Livewire::actingAs($user)
            ->test('vote')
            ->set('question', 'This is another test question that should fail')
            ->call('saveQuestion')
            ->assertHasErrors(['question' => 'You have reached the question limit']);
    }

    public function test_save_question_requires_subscription(): void
    {
        // Create a user with no subscription
        $user = User::factory()->create();
        
        // Create a topic (required for canSubmitQuestion to return true)
        Topic::factory()->create();

        // Test that user without subscription can't create a question
        Livewire::actingAs($user)
            ->test('vote')
            ->set('question', 'This is a test question that should fail')
            ->call('saveQuestion')
            ->assertHasErrors(['question' => 'You have reached the question limit']);
    }
}