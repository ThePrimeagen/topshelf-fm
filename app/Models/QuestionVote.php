<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\TwitchSubscription;

/**
 * 
 *
 * @property int $user_id
 * @property int $question_id
 * @property int $count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Question $question
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\QuestionVoteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionVote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionVote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionVote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionVote whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionVote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionVote whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionVote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionVote whereUserId($value)
 * @mixin \Eloquent
 */
class QuestionVote extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'question_votes';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The primary key for the model.
     *
     * @var array
     */
    protected $primaryKey = ['user_id', 'question_id'];

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Get the question that the vote belongs to.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the user that the vote belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Upvote a question for a user.
     *
     * @param int $questionId
     * @param int $userId
     * @param TwitchSubscription $subscription
     * @return QuestionVote
     */
    public static function upvote(int $questionId, int $userId, TwitchSubscription $subscription = TwitchSubscription::None): QuestionVote
    {
        $voteWeight = $subscription->getVoteValue();
        return static::vote($questionId, $userId, $voteWeight);
    }

    /**
     * Downvote a question for a user.
     *
     * @param int $questionId
     * @param int $userId
     * @param TwitchSubscription $subscription
     * @return QuestionVote
     */
    public static function downvote(int $questionId, int $userId, TwitchSubscription $subscription = TwitchSubscription::None): QuestionVote
    {
        $voteWeight = $subscription->getVoteValue();
        return static::vote($questionId, $userId, -$voteWeight);
    }

    /**
     * Vote on a question with a specified count.
     *
     * @param int $questionId
     * @param int $userId
     * @param int $count
     * @return QuestionVote
     */
    private static function vote(int $questionId, int $userId, int $count): QuestionVote
    {
        return static::updateOrCreate(
            [
                'question_id' => $questionId,
                'user_id' => $userId,
            ],
            [
                'count' => $count,
            ]
        );
    }

    /**
     * Get the vote count for a question.
     *
     * @param int $questionId
     * @return int
     */
    public static function getVoteCount(int $questionId): int
    {
        return static::where('question_id', $questionId)->sum('count') ?: 0;
    }
}
