<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionVote extends Model
{
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
     * @return QuestionVote
     */
    public static function upvote(int $questionId, int $userId): QuestionVote
    {
        return static::updateOrCreate(
            [
                'question_id' => $questionId,
                'user_id' => $userId,
            ],
            [
                'count' => 1,
            ]
        );
    }

    /**
     * Downvote a question for a user.
     *
     * @param int $questionId
     * @param int $userId
     * @return QuestionVote
     */
    public static function downvote(int $questionId, int $userId): QuestionVote
    {
        return static::updateOrCreate(
            [
                'question_id' => $questionId,
                'user_id' => $userId,
            ],
            [
                'count' => -1,
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
