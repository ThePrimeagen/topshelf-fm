<x-layouts.app>
<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">Game Token</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('To participate in ThePrimeagen\'s and Teej\'s stream games') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <div>
        <x-settings.layout heading="{{ __('Game Token') }}" subheading="Your Game Token">
            <livewire:settings.delete-game-token />
        </x-settings.layout>
    </div>
</section>
</x-layouts.app>

