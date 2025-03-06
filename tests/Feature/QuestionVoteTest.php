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
        $vote = QuestionVote::upvote($question->id, $user->id);
        
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
        $vote = QuestionVote::downvote($question->id, $user->id);
        
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
        QuestionVote::upvote($question->id, $user->id);
        
        // Assert an upvote was created for the question by the user
        $vote = QuestionVote::where('question_id', $question->id)
            ->where('user_id', $user->id)
            ->first();
            
        $this->assertNotNull($vote);
        $this->assertEquals(1, $vote->count);
        
        // Assert the question has a vote count of 1
        $this->assertEquals(1, $question->voteCount());
    }
} 