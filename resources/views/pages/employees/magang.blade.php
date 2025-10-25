<?php

use Livewire\Volt\Component;

new class extends Component {
    // Page-level state not required
}; ?>

<section class="w-full">
    <div class="mx-auto max-w-7xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <flux:heading size="xl">Pegawai Magang</flux:heading>
            <div class="text-sm text-zinc-500">Daftar pegawai dengan status magang</div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-zinc-900">
            <livewire:employee-index :status="'Magang'" />
        </div>
    </div>
</section>
