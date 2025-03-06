<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/vote');
    } else {
        return view('welcome');
    }
})->name('home');

Volt::route('/vote', 'vote')
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
});

require __DIR__ . '/auth.php';
