<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="/vote" class="ml-2 mr-5 flex items-center space-x-2 lg:ml-0" wire:navigate>
                <x-app-logo class="size-8" href="#"></x-app-logo>
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="hand-thumb-up" href="/vote" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Votes') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />
            <flux:spacer />
            <flux:spacer />

            <flux:spacer />

            <div class="flex items-center gap-4 mr-4">
                <a class="focus-visible:shadow-xs-selected rounded-md focus:outline-hidden" href="https://sentry.io"">
<svg class="css-lfbo6j e1igk8x04" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 66" width="25" height="23"><path d="M29,2.26a4.67,4.67,0,0,0-8,0L14.42,13.53A32.21,32.21,0,0,1,32.17,40.19H27.55A27.68,27.68,0,0,0,12.09,17.47L6,28a15.92,15.92,0,0,1,9.23,12.17H4.62A.76.76,0,0,1,4,39.06l2.94-5a10.74,10.74,0,0,0-3.36-1.9l-2.91,5a4.54,4.54,0,0,0,1.69,6.24A4.66,4.66,0,0,0,4.62,44H19.15a19.4,19.4,0,0,0-8-17.31l2.31-4A23.87,23.87,0,0,1,23.76,44H36.07a35.88,35.88,0,0,0-16.41-31.8l4.67-8a.77.77,0,0,1,1.05-.27c.53.29,20.29,34.77,20.66,35.17a.76.76,0,0,1-.68,1.13H40.6q.09,1.91,0,3.81h4.78A4.59,4.59,0,0,0,50,39.43a4.49,4.49,0,0,0-.62-2.28Z" transform="translate(11, 11)" fill="#362d59"></path></svg>
                </a>

                <a class="focus-visible:shadow-xs-selected rounded-md focus:outline-hidden" href="https://cloud.laravel.com">
                    <svg class="text-strong size-6 shrink-0" viewBox="0 0 98 98" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M97.466 64.97 64.98 97.457H0v-64.97L32.485 0h64.98v64.98-.01Zm-64.98-45.477v6.501h38.986v38.987h6.5V19.493H32.486Zm0-6.5h51.978V64.97h6.5V6.49H32.486v6.501Zm0 19.492v32.486H64.97V32.485H32.485Z" fill="currentColor" data-darkreader-inline-fill="" style="--darkreader-inline-fill: currentColor;">
                        </path>
                    </svg>
                </a>
            </div>

            <!-- Desktop User Menu -->
            <flux:dropdown position="top" align="end">
                <flux:profile
                    class="cursor-pointer"
                    :avatar="auth()->user()->twitch_avatar_url"
                    :initials="auth()->user()->initials()"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        <img src="{{ auth()->user()->twitch_avatar_url }}" />
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item href="{{ route('settings') }}" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>

        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="/vote" class="ml-1 flex items-center space-x-2" wire:navigate>
                <x-app-logo class="size-8" href="#"></x-app-logo>
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group heading="Topshelf.fm">
                    <flux:navlist.item icon="hand-thumb-up" href="/vote" wire:navigate>
                    {{ __('Votes') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>
        </flux:sidebar>


        {{ $slot }}

        @fluxScripts
    </body>
</html>
