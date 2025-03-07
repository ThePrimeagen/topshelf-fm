<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use App\Twitch;

new class extends Component {
    public $token;
    public function mount() {
        $this->token = Twitch::getGameUUID(Auth::user()->twitch_id);
    }
    public function refresh() {
        $this->token = Twitch::newGameToken(Auth::user()->twitch_id);
    }
}; ?>

<div class="flex items-center gap-2">
    <flux:heading>Token: {{ $token }}</flux:heading>
    <flux:button variant="danger" wire:click="refresh"> Refresh </flux:button>
</div>
