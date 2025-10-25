<?php

use Livewire\Volt\Component;
use App\Models\Employee;
use Illuminate\Support\Str;

new class extends Component {
    public Employee $employee;

    public function mount($employee): void
    {
        // Support viewing soft-deleted employees as "resigned"
        $this->employee = Employee::withTrashed()->findOrFail($employee);
    }
}; ?>

<section class="w-full">
    <div class="mx-auto max-w-6xl p-6 space-y-6">
        <!-- Header Card -->
        <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="relative h-24 w-24 overflow-hidden rounded-xl ring-2 ring-white/60 dark:ring-gray-700">
                    <div class="flex h-full w-full items-center justify-center rounded-xl bg-gray-500/10 text-2xl font-semibold text-gray-700 dark:text-gray-300">
                        {{ Str::of($employee->name ?? '??')->trim()->explode(' ')->map(fn($p)=>Str::substr($p,0,1))->take(2)->join('') }}
                    </div>
                </div>

                <div class="grid flex-1 gap-1">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $employee->name }}</h1>
                    <div class="text-gray-600 dark:text-gray-300">{{ $employee->position ?: '—' }} @if($employee->lembaga) • {{ $employee->lembaga }} @endif</div>
                    <div class="mt-1 flex flex-wrap items-center gap-2">
                        @if($employee->status_kepegawaian)
                            <span class="inline-flex items-center rounded-full border border-blue-200 bg-blue-50 px-2 py-0.5 text-xs text-blue-700 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-300">{{ $employee->status_kepegawaian }}</span>
                        @endif
                        @if($employee->aktif)
                            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ $employee->aktif }}</span>
                        @endif
                        @if($employee->date_joined)
                            <span class="inline-flex items-center rounded-full border border-gray-200 bg-white/70 px-2 py-0.5 text-xs text-gray-700 backdrop-blur dark:border-gray-700 dark:bg-gray-800/70 dark:text-gray-200">Masuk: {{ $employee->date_joined?->format('d M Y') }}</span>
                        @endif
                        @if($employee->date_resigned)
                            <span class="inline-flex items-center rounded-full border border-red-200 bg-red-50 px-2 py-0.5 text-xs text-red-700 dark:border-red-800 dark:bg-red-950/50 dark:text-red-300">Keluar: {{ $employee->date_resigned?->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>

                <div class="ms-auto flex items-center gap-2">
                    <a href="{{ route('employees.resign') }}" wire:navigate class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Left: Details -->
            <div class="md:col-span-2 space-y-6">
                <!-- Identitas & Kontak -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Identitas & Kontak</h2>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <div class="text-xs text-gray-500">NIP/NIPY</div>
                            <div class="text-sm">{{ $employee->employee_number ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">NIK</div>
                            <div class="text-sm">{{ $employee->nik ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">No HP</div>
                            <div class="text-sm">{{ $employee->phone ?: '—' }}</div>
                        </div>
                        <div class="sm:col-span-3">
                            <div class="text-xs text-gray-500">Alamat</div>
                            <div class="text-sm">{{ $employee->alamat ?: '—' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Kepegawaian -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Kepegawaian</h2>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <div class="text-xs text-gray-500">Lembaga</div>
                            <div class="text-sm">{{ $employee->lembaga ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Jabatan</div>
                            <div class="text-sm">{{ $employee->position ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Amanah Pokok</div>
                            <div class="text-sm">{{ $employee->amanah_pokok ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Golongan</div>
                            <div class="text-sm">{{ $employee->golongan ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Pangkat</div>
                            <div class="text-sm">{{ $employee->pangkat ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Job Level</div>
                            <div class="text-sm">{{ $employee->job_level ?: '—' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Pribadi -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Pribadi</h2>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <div class="text-xs text-gray-500">Tempat, Tanggal Lahir</div>
                            <div class="text-sm">{{ $employee->place_of_birth ?: '—' }}, {{ optional($employee->date_of_birth)->format('d M Y') ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Jenis Kelamin</div>
                            <div class="text-sm">{{ $employee->jenis_kelamin ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Status Pernikahan</div>
                            <div class="text-sm">{{ $employee->marital_status ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Anggota Keluarga</div>
                            <div class="text-sm">{{ $employee->jml_anggota_keluarga ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Jumlah Anak</div>
                            <div class="text-sm">{{ $employee->jumlah_anak ?: '—' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Edukasi & Kesehatan -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Edukasi & Kesehatan</h2>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <div class="text-xs text-gray-500">Pendidikan Terakhir</div>
                            <div class="text-sm">{{ $employee->pendidikan_terakhir ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Ijazah Tambahan</div>
                            <div class="text-sm">{{ $employee->ijazah_tambahan ?: '—' }}</div>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="text-xs text-gray-500">Kesehatan</div>
                            <div class="text-sm">{{ $employee->kesehatan ?: '—' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Resign Info -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Informasi Resign</h2>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <div class="text-xs text-gray-500">Tanggal Resign</div>
                            <div class="text-sm">{{ optional($employee->date_resigned)->format('d M Y') ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Status Kepegawaian</div>
                            <div class="text-sm">{{ $employee->status_kepegawaian ?: '—' }}</div>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="text-xs text-gray-500">Alasan</div>
                            <div class="text-sm">{{ $employee->alasan_resign ?: '—' }}</div>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="text-xs text-gray-500">Keterangan</div>
                            <div class="text-sm">{{ $employee->keterangan ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Aksi</h2>
                    @if($employee->trashed())
                        <form method="POST" action="{{ route('employees.restore', $employee->id) }}">
                            @csrf
                            <button class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700">Pulihkan Pegawai</button>
                        </form>
                        <form class="mt-2" method="POST" action="{{ route('employees.forceDestroy', $employee->id) }}" onsubmit="return confirm('Hapus permanen data ini? Tindakan tidak dapat dibatalkan.');">
                            @csrf
                            @method('DELETE')
                            <button class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">Hapus Permanen</button>
                        </form>
                    @else
                        <div class="text-sm text-gray-500">Pegawai ini tidak dalam status resign.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>