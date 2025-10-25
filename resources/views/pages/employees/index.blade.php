<?php

use Livewire\Volt\Component;

new class extends Component {
    // No state required for this page yet
}; ?>

<section class="w-full">
    <div class="mx-auto max-w-7xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <flux:heading size="xl">Manajemen Pegawai</flux:heading>
            <div class="text-sm text-zinc-500">Kelola data pegawai dan impor CSV</div>
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
            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                @csrf
                <div class="flex items-center gap-4">
                    <input type="file" name="csv_file" accept=".csv" required class="block text-sm" />
                    <div class="text-xs text-zinc-500">Format: .csv — pastikan header kolom sesuai</div>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Import CSV</button>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-zinc-900">
            <livewire:employee-index />
        </div>
    </div>
</section>
