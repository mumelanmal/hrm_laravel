<?php

use Livewire\Volt\Component;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

new class extends Component {
    public Employee $employee;

    public function mount(Employee $employee): void
    {
        $this->employee = $employee->load(['changes' => function($q){ $q->latest(); }]);
    }
}; ?>

<section class="w-full">
    <div class="mx-auto max-w-6xl p-6 space-y-6">
        <!-- Header Card -->
        <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="relative h-24 w-24 overflow-hidden rounded-xl ring-2 ring-white/60 dark:ring-gray-700">
                    @if($employee->profile_photo_path)
                        <img src="{{ Storage::disk('public')->url($employee->profile_photo_path) }}" alt="Foto {{ $employee->name }}" class="h-full w-full object-cover" />
                    @else
                        <div class="flex h-full w-full items-center justify-center rounded-xl bg-blue-500/10 text-2xl font-semibold text-blue-700 dark:text-blue-300">
                            {{ Str::of($employee->name ?? '??')->trim()->explode(' ')->map(fn($p)=>Str::substr($p,0,1))->take(2)->join('') }}
                        </div>
                    @endif
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
                    </div>
                </div>

                <div class="ms-auto flex items-center gap-2">
                    <a href="{{ route('employees.index') }}" wire:navigate class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        Kembali
                    </a>
                    <a href="{{ route('employees.edit', $employee) }}" wire:navigate class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Left: Details -->
            <div class="md:col-span-2 space-y-6">
                <!-- Kontak -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Kontak</h2>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <div class="text-xs text-gray-500">Email</div>
                            <div class="text-sm">{{ $employee->email ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">No HP</div>
                            <div class="text-sm">{{ $employee->phone ?: '—' }}</div>
                        </div>
                        <div class="sm:col-span-2">
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
                            <div class="text-xs text-gray-500">NIP/NIPY</div>
                            <div class="text-sm">{{ $employee->employee_number ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Lembaga</div>
                            <div class="text-sm">{{ $employee->lembaga ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Jabatan</div>
                            <div class="text-sm">{{ $employee->position ?: '—' }}</div>
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
                            <div class="text-xs text-gray-500">NIK</div>
                            <div class="text-sm">{{ $employee->nik ?: '—' }}</div>
                        </div>
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

                <!-- Dokumen -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Dokumen</h2>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <div class="text-xs text-gray-500">NPWP</div>
                            <div class="text-sm">{{ $employee->npwp ? str_pad(Str::substr($employee->npwp, -4), Str::length($employee->npwp), '•', STR_PAD_LEFT) : '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Rekening Bank</div>
                            <div class="text-sm">{{ $employee->bank_account ? str_pad(Str::substr($employee->bank_account, -4), Str::length($employee->bank_account), '•', STR_PAD_LEFT) : '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">BPJS</div>
                            <div class="text-sm">{{ $employee->bpjs_number ? str_pad(Str::substr($employee->bpjs_number, -4), Str::length($employee->bpjs_number), '•', STR_PAD_LEFT) : '—' }}</div>
                        </div>
                        <div class="sm:col-span-3">
                            <div class="text-xs text-gray-500">Pendidikan Terakhir</div>
                            <div class="text-sm">{{ $employee->pendidikan_terakhir ?: '—' }}</div>
                        </div>
                        <div class="sm:col-span-3">
                            <div class="text-xs text-gray-500">Ijazah Tambahan</div>
                            <div class="text-sm">{{ $employee->ijazah_tambahan ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Activity -->
            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                    <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Aktivitas</h2>
                    <ol class="relative ms-2 border-s border-gray-200 dark:border-gray-700">
                        @forelse($employee->changes as $chg)
                            <li class="mb-4 ms-4">
                                <div class="absolute -start-1.5 mt-1.5 h-3 w-3 rounded-full border border-white bg-blue-500 dark:border-gray-800"></div>
                                <div class="text-xs text-gray-500">{{ $chg->created_at?->format('d M Y H:i') }} • {{ ucfirst($chg->action) }}</div>
                                <div class="text-sm">@php
                                    $actor = optional(\App\Models\User::find($chg->user_id))->name ?? 'System';
                                    $count = is_array($chg->changes) ? count($chg->changes) : (is_string($chg->changes) ? strlen($chg->changes) : 0);
                                @endphp
                                Oleh: <span class="font-medium">{{ $actor }}</span>
                                @if(is_array($chg->changes))
                                    <span class="text-gray-500">(Perubahan: {{ $count }})</span>
                                @endif
                                </div>
                            </li>
                        @empty
                            <li class="ms-4 text-sm text-gray-500">Belum ada aktivitas.</li>
                        @endforelse
                    </ol>
                </div>

                <div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-950/30 shadow-sm">
                    <div class="mb-2 text-sm font-semibold text-red-700 dark:text-red-300">Tindakan</div>
                    <div class="text-sm text-red-700 dark:text-red-300">Hapus pegawai ini akan menghapus seluruh datanya secara permanen.</div>
                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="mt-3" onsubmit="return confirm('Yakin hapus pegawai ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Hapus Pegawai
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>