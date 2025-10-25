<?php

use Livewire\Volt\Component;

new class extends Component {
    // Page-level state not required
}; ?>

<section class="w-full">
    <div class="mx-auto max-w-7xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <flux:heading size="xl">Pegawai Resign</flux:heading>
            <div class="text-sm text-zinc-500">Daftar pegawai yang telah resign</div>
        </div>

        @if(session('status'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-3 py-2 text-green-700">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-red-700">{{ session('error') }}</div>
        @endif
        @error('csv_file')
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-red-700">{{ $message }}</div>
        @enderror

        <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-zinc-900">
            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                Import data resign kini terintegrasi pada import karyawan biasa. Sertakan kolom <code>date_resigned</code>, <code>alasan_resign</code>, dan <code>keterangan</code> untuk menandai resign. Data yang resign akan muncul sebagai soft-deleted.
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-zinc-900">
            <livewire:resigned-employee-index />
        </div>
    </div>
</section>
