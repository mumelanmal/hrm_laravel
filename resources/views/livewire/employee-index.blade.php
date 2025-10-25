<div class="space-y-4" x-data x-init="(() => { const saved = localStorage.getItem('employees_view_mode'); if (saved && saved !== $wire.viewMode) { $wire.set('viewMode', saved); } window.addEventListener('view-mode-changed', (e) => { try { localStorage.setItem('employees_view_mode', e.detail.mode); } catch (err) {} }); })()">
    <!-- Filters and quick stats -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-3">
            <form wire:submit.prevent="submitSearch" class="flex items-center gap-2">
                <input
                    type="search"
                    wire:model.defer="search"
                    placeholder="Cari nama, NIP, NIK, email, jabatan, lembaga..."
                    class="w-80 rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900"
                />
                <flux:button type="submit" color="zinc" size="sm">Cari</flux:button>
                @if($search)
                    <button type="button" wire:click="$set('search','')" class="rounded border border-zinc-300 px-2 py-1 text-xs text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Clear</button>
                @endif
            </form>

            <div class="flex items-center gap-2 text-sm">
                <span class="text-zinc-500">Per halaman</span>
                <select wire:model="perPage" class="rounded border border-zinc-300 px-2 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('employees.create') }}" wire:navigate>
                <flux:button color="primary" size="sm">Tambah Pegawai</flux:button>
            </a>
            <a
                href="{{ route('employees.export', array_filter([
                    'q' => $search ?: null,
                    'status' => $status !== 'all' ? $status : null,
                    'aktif' => $aktif !== 'all' ? $aktif : null,
                ])) }}"
                target="_blank"
            >
                <flux:button color="zinc" size="sm">Export CSV</flux:button>
            </a>
            <flux:button size="sm" wire:click="toggleView" color="zinc">
                {{ $viewMode === 'compact' ? 'Tampilan Detail' : 'Tampilan Ringkas' }}
            </flux:button>
            <button type="button" wire:click="$set('search',''); $set('status','all'); $set('aktif','all'); $set('sortField','id'); $set('sortDirection','desc')" class="rounded border border-zinc-300 px-2 py-1 text-xs text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Reset</button>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="rounded-full border border-neutral-200 bg-white px-2.5 py-1 text-zinc-700 dark:border-neutral-700 dark:bg-zinc-800 dark:text-zinc-200">Total: <strong class="ms-1">{{ number_format($counts['total'] ?? 0) }}</strong></span>
            <span class="rounded-full border border-neutral-200 bg-white px-2.5 py-1 text-zinc-700 dark:border-neutral-700 dark:bg-zinc-800 dark:text-zinc-200">Aktif: <strong class="ms-1">{{ number_format($counts['aktif'] ?? 0) }}</strong></span>
            <span class="rounded-full border border-neutral-200 bg-white px-2.5 py-1 text-zinc-700 dark:border-neutral-700 dark:bg-zinc-800 dark:text-zinc-200">Tetap: <strong class="ms-1">{{ number_format($counts['tetap'] ?? 0) }}</strong></span>
        </div>
    </div>

    <!-- More filters -->
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-zinc-500">Status Kepegawaian</span>
            <select wire:model="status" class="rounded border border-zinc-300 px-2 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                <option value="all">Semua</option>
                @foreach($statuses as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <span class="text-zinc-500">Aktif</span>
            <select wire:model="aktif" class="rounded border border-zinc-300 px-2 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                <option value="all">Semua</option>
                <option value="Aktif">Aktif</option>
                <option value="Nonaktif">Nonaktif</option>
            </select>
        </div>
    </div>

    <!-- Quick chips -->
    <div class="flex flex-wrap items-center gap-2 text-xs">
        <span class="text-zinc-500 me-1">Cepat:</span>
        <button type="button" wire:click="$set('status','all'); $set('aktif','all')" class="rounded-full border px-2.5 py-1 {{ $status==='all' && $aktif==='all' ? 'border-blue-300 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'border-neutral-200 bg-white text-zinc-700 dark:border-neutral-700 dark:bg-zinc-900 dark:text-zinc-200' }}">Semua</button>
        <button type="button" wire:click="$set('aktif','Aktif')" class="rounded-full border px-2.5 py-1 {{ $aktif==='Aktif' ? 'border-emerald-300 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'border-neutral-200 bg-white text-zinc-700 dark:border-neutral-700 dark:bg-zinc-900 dark:text-zinc-200' }}">Aktif</button>
        <button type="button" wire:click="$set('aktif','Nonaktif')" class="rounded-full border px-2.5 py-1 {{ $aktif==='Nonaktif' ? 'border-red-300 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300' : 'border-neutral-200 bg-white text-zinc-700 dark:border-neutral-700 dark:bg-zinc-900 dark:text-zinc-200' }}">Nonaktif</button>
        @php $common = collect(['Pegawai Tetap','Kontrak','Magang','Purna Magang']); @endphp
        @foreach($statuses as $s)
            @if($common->contains($s))
                <button type="button" wire:click="$set('status', @js($s))" class="rounded-full border px-2.5 py-1 {{ $status===$s ? 'border-blue-300 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'border-neutral-200 bg-white text-zinc-700 dark:border-neutral-700 dark:bg-zinc-900 dark:text-zinc-200' }}">{{ $s }}</button>
            @endif
        @endforeach
    </div>

    <!-- Data table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr class="text-left">
                    <th class="p-2 font-medium sticky left-0 top-0 z-40 bg-gray-50 dark:bg-zinc-800 w-16 min-w-[4rem]">
                        <button type="button" wire:click="sortBy('id')" class="inline-flex items-center gap-1">
                            #
                            @if(($sortField ?? null) === 'id')
                                <span class="text-xs">{{ ($sortDirection ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th class="p-2 font-medium sticky left-16 top-0 z-30 bg-gray-50 dark:bg-zinc-800">
                        <button type="button" wire:click="sortBy('name')" class="inline-flex items-center gap-1">
                            Nama
                            @if(($sortField ?? null) === 'name')
                                <span class="text-xs">{{ ($sortDirection ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">
                        <button type="button" wire:click="sortBy('employee_number')" class="inline-flex items-center gap-1">
                            NIP
                            @if(($sortField ?? null) === 'employee_number')
                                <span class="text-xs">{{ ($sortDirection ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">NIK</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Email</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">
                        <button type="button" wire:click="sortBy('phone')" class="inline-flex items-center gap-1">
                            No HP
                            @if(($sortField ?? null) === 'phone')
                                <span class="text-xs">{{ ($sortDirection ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">
                        <button type="button" wire:click="sortBy('position')" class="inline-flex items-center gap-1">
                            Jabatan
                            @if(($sortField ?? null) === 'position')
                                <span class="text-xs">{{ ($sortDirection ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">
                        <button type="button" wire:click="sortBy('lembaga')" class="inline-flex items-center gap-1">
                            Lembaga
                            @if(($sortField ?? null) === 'lembaga')
                                <span class="text-xs">{{ ($sortDirection ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">
                        <button type="button" wire:click="sortBy('status_kepegawaian')" class="inline-flex items-center gap-1">
                            Status Kepegawaian
                            @if(($sortField ?? null) === 'status_kepegawaian')
                                <span class="text-xs">{{ ($sortDirection ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">Aktif</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Tahun Masuk</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">
                        <button type="button" wire:click="sortBy('date_joined')" class="inline-flex items-center gap-1">
                            Tanggal Masuk
                            @if(($sortField ?? null) === 'date_joined')
                                <span class="text-xs">{{ ($sortDirection ?? 'asc') === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </button>
                    </th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Golongan</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Pangkat</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Job Level</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Tempat Lahir</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Tanggal Lahir</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Alamat</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Jenis Kelamin</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Status Pernikahan</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Jml Angg. Keluarga</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Jumlah Anak</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">NPWP</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Rekening Bank</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">BPJS</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Kesehatan</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Pendidikan Terakhir</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800 {{ $viewMode === 'compact' ? 'hidden' : '' }}">Ijazah Tambahan</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr class="border-t border-neutral-200 hover:bg-gray-50 dark:border-neutral-700 dark:hover:bg-zinc-800">
                        <td class="p-2 sticky left-0 z-20 bg-white dark:bg-zinc-900 w-16 min-w-[4rem]">{{ $employee->id }}</td>
                        <td class="p-2 sticky left-16 z-10 bg-white dark:bg-zinc-900">
                            <a href="{{ route('employees.show', $employee) }}" wire:navigate class="text-blue-700 hover:underline dark:text-blue-400">{{ $employee->name }}</a>
                        </td>
                        <td class="p-2">{{ $employee->employee_number }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->nik }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->email }}</td>
                        <td class="p-2">{{ $employee->phone }}</td>
                        <td class="p-2">{{ $employee->position }}</td>
                        <td class="p-2">{{ $employee->lembaga }}</td>
                        
                        <td class="p-2">{{ $employee->status_kepegawaian }}</td>
                        <td class="p-2">{{ $employee->aktif }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->tahun_masuk }}</td>
                        <td class="p-2">{{ optional($employee->date_joined)->format('Y-m-d') ?? '' }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->golongan }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->pangkat }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->job_level }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->place_of_birth }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ optional($employee->date_of_birth)->format('Y-m-d') }}</td>
                        <td class="p-2 max-w-[240px] truncate {{ $viewMode === 'compact' ? 'hidden' : '' }}" title="{{ $employee->alamat }}">{{ $employee->alamat }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->jenis_kelamin }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->marital_status }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->jml_anggota_keluarga }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->jumlah_anak }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->npwp }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->bank_account }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->bpjs_number }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->kesehatan }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->pendidikan_terakhir }}</td>
                        <td class="p-2 {{ $viewMode === 'compact' ? 'hidden' : '' }}">{{ $employee->ijazah_tambahan }}</td>
                        <td class="p-2">
                            @if($employee->profile_photo_path)
                                <img src="{{ Storage::disk('public')->url($employee->profile_photo_path) }}" alt="Foto" class="h-10 w-10 rounded object-cover" />
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="border-t border-neutral-200 dark:border-neutral-700">
                        <td class="p-2 text-zinc-600 dark:text-zinc-300" colspan="29">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $employees->links() }}
    </div>
</div>
