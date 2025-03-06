<?php

namespace App\Models;

use App\Enums\TwitchSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $twitch_id
 * @property string $twitch_avatar_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionVote> $votes
 * @property-read int|null $votes_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwitchAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwitchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        'twitch_avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    static public function getBroadcasterID(): string
    {
        return config('services.twitch.broadcaster_id');
    }

    static public function getAllFriendIDs(): array
    {
        return array_merge([self::getBroadcasterID()], config('services.twitch.friend_ids'));
    }

    public function isBroadcaster(): bool
    {
        return $this->twitch_id === self::getBroadcasterID();
    }

    public function isAdminUser(): bool
    {
        // Check if this user is in the list of friend ids
        /* return array_search($this->twitch_id, self::getAllAdminIDs()) !== false; */

        // For now, just let broadcaster be admin. Add mod status later, or include friends as well
        return $this->isBroadcaster();
    }

    public function getHighestSubscription(): TwitchSubscription
    {
        $subscription = UserTwitchSubscription::where('user_id', $this->id)
            ->where('twitch_subscription', '>=', TwitchSubscription::Tier1)
            ->where(function ($query) {
                $query->whereIn('broadcaster_id', config('services.twitch.friend_ids'))
                    ->orWhere('broadcaster_id', config('services.twitch.broadcaster_id'));
            })
            ->orderBy('twitch_subscription', 'desc')
            ->first();

        return $subscription?->twitch_subscription ?? TwitchSubscription::None;
    }

    public function canSubmitQuestion(): bool
    {
        $subscription = $this->getHighestSubscription();
        if ($subscription === TwitchSubscription::None) {
            return false;
        }

        return DB::table("topics")->count() > 0
            && $this->questions()->count() < $subscription->maxActiveQuestions();
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
