<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <input
                    type="search"
                    wire:model.debounce.400ms="search"
                    placeholder="Cari nama, NIP/NIK, lembaga, alasan..."
                    class="w-80 rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900"
                />
            </div>
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
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-zinc-800">
                <tr class="text-left">
                    <th class="p-2 font-medium sticky left-0 top-0 z-40 bg-gray-50 dark:bg-zinc-800 w-16 min-w-[4rem]">
                        <button type="button" wire:click="sortBy('id')" class="inline-flex items-center gap-1">
                            #
                        </button>
                    </th>
                    <th class="p-2 font-medium sticky left-16 top-0 z-30 bg-gray-50 dark:bg-zinc-800">
                        <button type="button" wire:click="sortBy('name')" class="inline-flex items-center gap-1">
                            Nama
                        </button>
                    </th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">NIP/NIPY</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">NIK</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">Lembaga</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">Jabatan</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">Amanah Pokok</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">Status Kepegawaian</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">Tanggal Masuk</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">Tanggal Resign</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">Alasan</th>
                    <th class="p-2 font-medium sticky top-0 z-40 bg-gray-50 dark:bg-zinc-800">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resigned as $item)
                    <tr class="border-t border-neutral-200 hover:bg-gray-50 dark:border-neutral-700 dark:hover:bg-zinc-800">
                        <td class="p-2 sticky left-0 z-20 bg-white dark:bg-zinc-900 w-16 min-w-[4rem]">{{ $item->id }}</td>
                        <td class="p-2 sticky left-16 z-10 bg-white dark:bg-zinc-900">
                            <a href="{{ route('resigned.show', $item) }}" wire:navigate class="text-blue-700 hover:underline dark:text-blue-400">{{ $item->name }}</a>
                        </td>
                        <td class="p-2">{{ $item->employee_number }}</td>
                        <td class="p-2">{{ $item->nik }}</td>
                        <td class="p-2">{{ $item->lembaga }}</td>
                        <td class="p-2">{{ $item->position }}</td>
                        <td class="p-2 max-w-[240px] truncate" title="{{ $item->amanah_pokok }}">{{ $item->amanah_pokok }}</td>
                        <td class="p-2">{{ $item->status_kepegawaian }}</td>
                        <td class="p-2">{{ optional($item->date_joined)->format('Y-m-d') }}</td>
                        <td class="p-2">{{ optional($item->date_resigned)->format('Y-m-d') }}</td>
                        <td class="p-2 max-w-[240px] truncate" title="{{ $item->alasan_resign }}">{{ $item->alasan_resign }}</td>
                        <td class="p-2 max-w-[240px] truncate" title="{{ $item->keterangan }}">{{ $item->keterangan }}</td>
                    </tr>
                @empty
                    <tr class="border-t border-neutral-200 dark:border-neutral-700">
                        <td class="p-2 text-zinc-600 dark:text-zinc-300" colspan="10">Belum ada data resign.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $resigned->links() }}
    </div>
</div>
