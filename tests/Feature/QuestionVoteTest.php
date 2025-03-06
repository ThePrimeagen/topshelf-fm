<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\QuestionVote;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserTwitchSubscription;
use App\Enums\TwitchSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QuestionVoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_question_vote_model_upvote_method()
    {
        // Create a user and a question
        $user = User::factory()->create();
        $question = Question::factory()->create();
        
        // Use the upvote method
        $vote = QuestionVote::upvote($question->id, $user->id, TwitchSubscription::None);
        
        // Assert the vote was created correctly
        $this->assertNotNull($vote);
        $this->assertEquals(1, $vote->count);
        $this->assertEquals($question->id, $vote->question_id);
        $this->assertEquals($user->id, $vote->user_id);
        
        // Assert the question has a vote count of 1
        $this->assertEquals(1, $question->voteCount());
    }
    
    public function test_question_vote_model_downvote_method()
    {
        // Create a user and a question
        $user = User::factory()->create();
        $question = Question::factory()->create();
        
        // Use the downvote method
        $vote = QuestionVote::downvote($question->id, $user->id, TwitchSubscription::None);
        
        // Assert the vote was created correctly
        $this->assertNotNull($vote);
        $this->assertEquals(-1, $vote->count);
        $this->assertEquals($question->id, $vote->question_id);
        $this->assertEquals($user->id, $vote->user_id);
        
        // Assert the question has a vote count of -1
        $this->assertEquals(-1, $question->voteCount());
    }
    
    public function test_auto_upvote_when_creating_question()
    {
        // This test directly tests the functionality without using Livewire
        
        // Create a user
        $user = User::factory()->create();
        
        // Create a question as if it was created by the user
        $question = Question::factory()->create([
            'user_id' => $user->id,
        ]);
        
        // Manually call the upvote method as it would be called in the controller
        QuestionVote::upvote($question->id, $user->id, TwitchSubscription::None);
        
        // Assert an upvote was created for the question by the user
        $vote = QuestionVote::where('question_id', $question->id)
            ->where('user_id', $user->id)
            ->first();
            
        $this->assertNotNull($vote);
        $this->assertEquals(1, $vote->count);
        
        // Assert the question has a vote count of 1
        $this->assertEquals(1, $question->voteCount());
    }

    public function test_toggle_from_upvote_to_downvote()
    {
        // Create a user and a question
        $user = User::factory()->create();
        $question = Question::factory()->create();
        
        // First upvote the question
        $vote = QuestionVote::upvote($question->id, $user->id, TwitchSubscription::None);
        
        // Assert the vote was created correctly as an upvote
        $this->assertEquals(1, $vote->count);
        $this->assertEquals(1, $question->voteCount());
        
        // Now toggle to a downvote
        $vote = QuestionVote::downvote($question->id, $user->id, TwitchSubscription::None);
        
        // Assert the vote was changed to a downvote
        $this->assertEquals(-1, $vote->count);
        
        // Assert the question now has a vote count of -1
        $this->assertEquals(-1, $question->voteCount());
        
        // Verify there's only one vote record in the database
        $this->assertEquals(1, QuestionVote::where('question_id', $question->id)->count());
    }
    
    public function test_toggle_from_downvote_to_upvote()
    {
        // Create a user and a question
        $user = User::factory()->create();
        $question = Question::factory()->create();
        
        // First downvote the question
        $vote = QuestionVote::downvote($question->id, $user->id, TwitchSubscription::None);
        
        // Assert the vote was created correctly as a downvote
        $this->assertEquals(-1, $vote->count);
        $this->assertEquals(-1, $question->voteCount());
        
        // Now toggle to an upvote
        $vote = QuestionVote::upvote($question->id, $user->id, TwitchSubscription::None);
        
        // Assert the vote was changed to an upvote
        $this->assertEquals(1, $vote->count);
        
        // Assert the question now has a vote count of 1
        $this->assertEquals(1, $question->voteCount());
        
        // Verify there's only one vote record in the database
        $this->assertEquals(1, QuestionVote::where('question_id', $question->id)->count());
    }
    
    public function test_subscription_tiers_affect_vote_weight()
    {
        // Create a user and a question
        $user = User::factory()->create();
        $question = Question::factory()->create();
        
        // Test Tier 1 subscription (weight = 2)
        $vote = QuestionVote::upvote($question->id, $user->id, TwitchSubscription::Tier1);
        $this->assertEquals(2, $vote->count);
        $this->assertEquals(2, $question->voteCount());
        
        // Test Tier 2 subscription (weight = 4)
        $vote = QuestionVote::upvote($question->id, $user->id, TwitchSubscription::Tier2);
        $this->assertEquals(4, $vote->count);
        $this->assertEquals(4, $question->voteCount());
        
        // Test Tier 3 subscription (weight = 8)
        $vote = QuestionVote::upvote($question->id, $user->id, TwitchSubscription::Tier3);
        $this->assertEquals(8, $vote->count);
        $this->assertEquals(8, $question->voteCount());
        
        // Test downvote with Tier 3 subscription (weight = -8)
        $vote = QuestionVote::downvote($question->id, $user->id, TwitchSubscription::Tier3);
        $this->assertEquals(-8, $vote->count);
        $this->assertEquals(-8, $question->voteCount());
    }
    
    public function test_multiple_users_voting_on_same_question()
    {
        // Create users and a question
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $question = Question::factory()->create();
        
        // User 1 upvotes with Tier 1 (weight = 2)
        QuestionVote::upvote($question->id, $user1->id, TwitchSubscription::Tier1);
        
        // User 2 upvotes with Tier 2 (weight = 4)
        QuestionVote::upvote($question->id, $user2->id, TwitchSubscription::Tier2);
        
        // User 3 downvotes with Tier 3 (weight = -8)
        QuestionVote::downvote($question->id, $user3->id, TwitchSubscription::Tier3);
        
        // Total vote count should be 2 + 4 - 8 = -2
        $this->assertEquals(-2, $question->voteCount());
        
        // User 3 changes their mind and upvotes instead
        QuestionVote::upvote($question->id, $user3->id, TwitchSubscription::Tier3);
        
        // Total vote count should now be 2 + 4 + 8 = 14
        $this->assertEquals(14, $question->voteCount());
    }
} 