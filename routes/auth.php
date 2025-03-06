<?php

use App\Models\User;
use App\Models\UserTwitchSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Twitch;
use App\Enums\TwitchSubscription;

Route::middleware('guest')->group(function () {
    Route::get("login", function () {
        return Socialite::driver("twitch")->scopes([
            "user:read:chat",
            "user:read:subscriptions",
        ])->redirect();
    })->name("login");

    Route::get("twitch/auth", function () {
        try {
            $twitchUser = Socialite::driver("twitch")->user();

            $user = User::updateOrCreate([
                "twitch_id" => $twitchUser->id,
            ], [
                'name' => $twitchUser->name,
                'twitch_avatar_url' => $twitchUser->avatar,
            ]);

            $subscriptions = Twitch::checkUserSubscriptions(
                $twitchUser->token,
                User::getAllFriendIDs(),
                $twitchUser->id,
            );

            $subscriptions->each(
                fn($subscription, $broadcaster_id) =>
                UserTwitchSubscription::updateOrCreate([
                    "user_id" => $user->id,
                    "broadcaster_id" => $broadcaster_id,
                ], [
                    "twitch_subscription" => $subscription,
                ])
            );

            Auth::login($user);
        } catch (\Exception $e) {
            // TODO: Send this to Sentry, because i'm not sure if it should be or not
            /* report($e); */
            dd($e);
            return redirect('/?failed_to_login=1');
        }

        return redirect('/vote');
    });
});

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');
