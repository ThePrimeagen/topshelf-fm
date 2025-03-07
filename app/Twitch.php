<?php

namespace App;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

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

    private static function makeTursoCall(string $sql) {
        $url = config('services.turso.url');
        $authToken = config('services.turso.token');
        $requests = [
            [
                'type' => 'execute',
                'stmt' => [
                    'sql' => $sql,
                ]
            ],
            [
                'type' => 'close'
            ]
        ];
        return Http::withHeaders(['Authorization' => 'Bearer ' . $authToken])
            ->asJson()
            ->post($url, ['requests' => $requests])
            ->json();
    }

    public static function getGameUUID($userId) {
        $sql = "SELECT uuid FROM user_mapping WHERE \"$userId\"";
        $resp = Twitch::makeTursoCall($sql);
        try {
            $results = $resp["results"][0];
            if ($results["type"] == "ok") {
                $inner = $results["response"]["result"]["rows"][0][0]["value"];
                return $inner;
            }
        } catch (\Exception $e) {
            return "unable to get token";
        }
        return $results;
    }

    public static function newGameToken(string $userId) {
        $uuidString = Uuid::uuid4()->toString();
        $sql = "UPDATE user_mapping
SET uuid = \"$uuidString\"
WHERE userId = \"$userId\";"; // yes this would normally be dangerous.. but its not because these are non dangerous values ever
        Twitch::makeTursoCall($sql);
        return $uuidString;
    }

    public static function saveGameToken(string $userId) {
        $uuidString = Uuid::uuid4()->toString();
        $sql = "INSERT OR IGNORE INTO user_mapping (uuid, userId)
VALUES (\"$uuidString\", \"$userId\");"; // yes this would normally be dangerous.. but its not because these are non dangerous values ever
        return Twitch::makeTursoCall($sql);
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
