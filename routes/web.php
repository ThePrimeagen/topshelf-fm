<?php

use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Livewire\Volt\Component;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/vote');
    } else {
        return view('welcome');
    }
})->name('home');

Route::get('vote', function () {
    return view('vote', []);
})
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('top-vote', function () {
    return view("top-vote", []);
})
    ->name('top-vote');

Route::middleware(['auth'])->group(function () {
    // volt route for settings.profile
    Route::get('settings', function () {
        return view('livewire.settings.profile');
    })->name('settings');

    // volt route for settings.profile
    Route::get('game-token', function () {
        return view('livewire.settings.game-token');
    })->name('game-token');
});

require __DIR__ . '/auth.php';
