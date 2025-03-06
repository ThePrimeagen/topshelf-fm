<?php

namespace App\Models;

use App\Enums\TwitchSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $user_id
 * @property string $broadcaster_id
 * @property TwitchSubscription $twitch_subscription
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\UserTwitchSubscriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTwitchSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTwitchSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTwitchSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTwitchSubscription whereBroadcasterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTwitchSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTwitchSubscription whereTwitchSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTwitchSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTwitchSubscription whereUserId($value)
 * @mixin \Eloquent
 */
class UserTwitchSubscription extends Model
{
    /** @use HasFactory<\Database\Factories\UserTwitchSubscriptionFactory> */
    use HasFactory;

    public $incrementing = false;
    public $primaryKey = ['user_id', 'broadcaster_id'];

    protected $fillable = [
        'user_id',
        'broadcaster_id',
        'twitch_subscription',
    ];

    public function casts(): array
    {
        return [
            'twitch_subscription' => TwitchSubscription::class,
        ];
    }
}
