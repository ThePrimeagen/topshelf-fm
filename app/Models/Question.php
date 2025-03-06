<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $user_id
 * @property string $question
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionVote> $votes
 * @property-read int|null $votes_count
 * @method static \Database\Factories\QuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUserId($value)
 * @mixin \Eloquent
 */
class Question extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the votes for the question.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(QuestionVote::class);
    }

    public static function getSortedQuestions($limit = 50)
    {
        return self::query()
            ->leftJoin('question_votes', 'questions.id', '=', 'question_votes.question_id')
            ->selectRaw('questions.*, coalesce(sum(question_votes.count), 0) as votes')
            ->orderBy('votes', 'desc')
            ->groupBy('questions.id')
            ->limit($limit)
            ->with('user')
            ->get();
    }

    public static function getRecentQuestions()
    {
        return self::query()
            ->leftJoin('question_votes', 'questions.id', '=', 'question_votes.question_id')
            ->selectRaw('questions.*, coalesce(sum(question_votes.count), 0) as votes')
            ->orderBy('id', 'desc')
            ->groupBy('questions.id')
            ->limit(50)
            ->with('user')
            ->get();
    }

    public function voteCount(): int
    {
        return QuestionVote::getVoteCount($this->id);
    }
}
