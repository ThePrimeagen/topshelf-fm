<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Twitch;
use App\TwitchSubscription;

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
            $twitchSub = Twitch::checkUserSubscription(
                $twitchUser->token,
                env("TWITCH_CHANNEL_ID"),
                $twitchUser->id,
            );

            $user = User::updateOrCreate([
                "twitch_id" => $twitchUser->id,
            ], [
                'name' => $twitchUser->name,
                'twitch_id' => $twitchUser->id,
                'twitch_access_token' => $twitchUser->token,
                'twitch_refresh_token' => $twitchUser->refreshToken,
                'twitch_expires_in' => $twitchUser->expiresIn,
                'twitch_avatar_url' => $twitchUser->avatar,
                'twitch_subscription' => $twitchSub,
                'poki_sub' => TwitchSubscription::None,
            ]);

            Auth::login($user);
        } catch (\Exception $e) {
            return redirect('/?failed_to_login=1');
        }

        return redirect('/vote');
    });
});

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');
