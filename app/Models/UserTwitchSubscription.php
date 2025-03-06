<?php

namespace App\Models;

use App\Enums\TwitchSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
