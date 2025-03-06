<?php

namespace App;

use App\Enums\TwitchSubscription;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class Twitch
{

    public static function checkUserSubscription(string $accessToken, string $channelId, string $userId): TwitchSubscription
    {
        $response = Http::withHeaders([
            'Client-ID' => Config::get('services.twitch.client_id'),
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.twitch.tv/helix/subscriptions/user', [
            'broadcaster_id' => $channelId,
            'user_id' => $userId,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (empty($data['data'])) {
                return TwitchSubscription::None;
            }

            $subscription = $data['data'][0];
            return TwitchSubscription::tryFrom($subscription['tier']);
        }

        return TwitchSubscription::None;
    }

    /**
     * @param string $accessToken
     * @param array $channels
     * @param string $userId
     * @return Collection<string, TwitchSubscription>
     */
    public static function checkUserSubscriptions(string $accessToken, array $channels, string $userId): Collection
    {
        $headers = [
            'Client-ID' => config('services.twitch.client_id'),
            'Authorization' => 'Bearer ' . $accessToken,
        ];

        $responses = Http::pool(
            fn(Pool $pool) =>
            collect($channels)->map(
                fn($channel) =>
                $pool
                    ->as($channel)
                    ->withHeaders($headers)
                    ->get('https://api.twitch.tv/helix/subscriptions/user', [
                        'broadcaster_id' => $channel,
                        'user_id' => $userId,
                    ])
            )
        );

        return collect($responses)->map(function ($response) {
            if ($response->successful()) {
                $data = $response->json();
                if (empty($data['data'])) {
                    return TwitchSubscription::None;
                }

                $subscription = $data['data'][0];
                return TwitchSubscription::tryFrom($subscription['tier']);
            }

            logger()->warning($response->json());
            return TwitchSubscription::None;
        });
    }
}
