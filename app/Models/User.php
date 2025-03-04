<?php

namespace App\Models;

use App\TwitchSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'twitch_id',
        'twitch_access_token',
        'twitch_refresh_token',
        'twitch_expires_in',
        'twitch_avatar_url',
        'twitch_subscription',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'twitch_access_token',
        'twitch_refresh_token',
        'twitch_expires_in',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'twitch_subscription' => TwitchSubscription::class,
            'poki_sub' => TwitchSubscription::class,
        ];
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function isAdminUser(): bool {
        return $this->twitch_id === env("TWITCH_CHANNEL_ID");
    }

    public function canSubmitQuestion(): bool
    {
        if (!$this->twitch_subscription->isSubscribed()) {
            return false;
        }

        return DB::table("topics")->count() > 0 && $this->questions()->doesntExist();
    }

    public function votes()
    {
        return $this->hasMany(QuestionVote::class);
    }


    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
}
