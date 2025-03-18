<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
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
        return self::query()
            ->leftJoin('question_votes', 'questions.id', '=', 'question_votes.question_id')
            ->selectRaw('questions.*, coalesce(sum(question_votes.count), 0) as votes')
            ->where('questions.id', $this->id)
            ->groupBy('questions.id')
            ->first()->votes;
    }
}
