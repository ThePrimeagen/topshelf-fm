<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
}; ?>

<x-layouts.app>
<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="hover:scale-105 duration-200 hover:shadow-lg shadow-zinc-800/50 dark:shadow-zinc-900/50">Settings</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage your profile and account settings') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <x-settings.layout heading="{{ __('Profile') }}" subheading="Update your settings">
            <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
                <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
                <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
            </flux:radio.group>

            <livewire:settings.delete-user-form />
        </x-settings.layout>
    </div>
</section>
</x-layouts.app>
