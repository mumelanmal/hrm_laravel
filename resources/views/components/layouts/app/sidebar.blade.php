<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">
        <div>
            <!-- Static sidebar -->
            <div class="fixed inset-y-0 z-40 flex w-72 flex-col">
                <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-6 pb-4">
                    <div class="flex h-16 shrink-0 items-center">
                        <a href="{{ route('dashboard') }}" wire:navigate>
                            <x-app-logo class="h-8 w-auto" />
                        </a>
                    </div>
                    <nav class="flex flex-1 flex-col">
                        <ul role="list" class="flex flex-1 flex-col gap-y-7">
                            <li>
                                <div class="text-xs font-semibold leading-6 text-zinc-400">{{ __('Menu') }}</div>
                                <ul role="list" class="-mx-2 mt-2 space-y-1">
                                    <li><x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</x-nav-link></li>
                                    <li><x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.index')" wire:navigate>{{ __('Pegawai') }}</x-nav-link></li>
                                    <li><x-nav-link :href="route('employees.magang')" :active="request()->routeIs('employees.magang')" wire:navigate>{{ __('Pegawai Magang') }}</x-nav-link></li>
                                    <li><x-nav-link :href="route('employees.resign')" :active="request()->routeIs('employees.resign')" wire:navigate>{{ __('Pegawai Resign') }}</x-nav-link></li>
                                    <li><x-nav-link :href="route('employees.purnaMagang')" :active="request()->routeIs('employees.purnaMagang')" wire:navigate>{{ __('Purna Magang') }}</x-nav-link></li>
                                </ul>
                            </li>
                            <li class="mt-auto" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
                                <div class="relative">
                                    <button @click="open = !open" class="flex items-center w-full text-left rounded-md p-2 text-sm font-semibold text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-zinc-200 text-zinc-900 dark:bg-zinc-700 dark:text-white">
                                            {{ auth()->user()->initials() }}
                                        </span>
                                        <span class="ml-4 flex-1 truncate">{{ auth()->user()->name }}</span>
                                        <svg class="ml-2 h-5 w-5 text-zinc-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.24a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute bottom-full mb-2 w-full rounded-md bg-white dark:bg-zinc-800 shadow-lg ring-1 ring-zinc-900/5">
                                        <a href="{{ route('profile.edit') }}" wire:navigate class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">{{ __('Settings') }}</a>
                                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                                {{ __('Log Out') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <div class="pl-72">
                <main class="py-10">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="mb-4">
                             <h1 class="text-2xl font-semibold leading-tight my-auto">{{ $title ?? '' }}</h1>
                        </div>
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
