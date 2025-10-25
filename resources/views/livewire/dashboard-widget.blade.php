<div class="flex h-full w-full flex-1 flex-col gap-6">
    <!-- Heading + actions -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <flux:heading size="xl">Dashboard</flux:heading>
            <div class="hidden text-sm text-gray-500 sm:block">Statistik pegawai dan aktivitas terkini</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.index') }}" wire:navigate class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">Import CSV</a>
            <a href="{{ route('employees.index') }}" wire:navigate class="inline-flex items-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-800 hover:bg-gray-50 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-800">Daftar Pegawai</a>
        </div>
    </div>

    <!-- Filters -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500">Rentang</span>
                    <select wire:model="range" class="rounded border border-gray-300 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="7d">7 hari</option>
                        <option value="30d">30 hari</option>
                        <option value="90d">90 hari</option>
                        <option value="all">Semua</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500">Kepegawaian</span>
                    <select wire:model="status" class="rounded border border-gray-300 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="all">Semua</option>
                        <option value="tetap">Pegawai Tetap</option>
                        <option value="kontrak">Kontrak</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500">Status</span>
                    <select wire:model="aktif" class="rounded border border-gray-300 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="all">Semua</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500">Lembaga</span>
                    <select wire:model="lembaga" class="rounded border border-gray-300 px-2 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="all">Semua</option>
                        @foreach($this->lembagaOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <flux:input wire:model.debounce.300ms="search" placeholder="Cari nama/NIP/jabatan/lembaga..." class="w-72" />
                <button wire:click="resetFilters" type="button" class="inline-flex items-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-800 hover:bg-gray-50 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-800">Reset</button>
                <button wire:click="export" type="button" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">Export CSV</button>
            </div>
        </div>
    </div>

    <!-- Active filter chips -->
    <div class="-mt-2 flex flex-wrap items-center gap-2 text-xs">
        @if($range !== '30d')
            <span class="inline-flex items-center gap-1 rounded-full border border-gray-300 px-2 py-1 dark:border-gray-700">
                Rentang: {{ $range }}
                <button wire:click="$set('range','30d')" class="ms-1">✕</button>
            </span>
        @endif
        @if($status !== 'all')
            <span class="inline-flex items-center gap-1 rounded-full border border-gray-300 px-2 py-1 dark:border-gray-700">
                Kepegawaian: {{ ucfirst($status) }}
                <button wire:click="$set('status','all')" class="ms-1">✕</button>
            </span>
        @endif
        @if($aktif !== 'all')
            <span class="inline-flex items-center gap-1 rounded-full border border-gray-300 px-2 py-1 dark:border-gray-700">
                Status: {{ $aktif }}
                <button wire:click="$set('aktif','all')" class="ms-1">✕</button>
            </span>
        @endif
        @if($lembaga !== 'all')
            <span class="inline-flex items-center gap-1 rounded-full border border-gray-300 px-2 py-1 dark:border-gray-700">
                Lembaga: {{ $lembaga }}
                <button wire:click="$set('lembaga','all')" class="ms-1">✕</button>
            </span>
        @endif
        @if(trim($search) !== '')
            <span class="inline-flex items-center gap-1 rounded-full border border-gray-300 px-2 py-1 dark:border-gray-700">
                Cari: “{{ $search }}”
                <button wire:click="$set('search','')" class="ms-1">✕</button>
            </span>
        @endif
    </div>

    <!-- Stats grid -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <button type="button" wire:click="resetFilters" class="text-left rounded-xl border border-gray-200 bg-white p-5 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-gray-500">Total Pegawai</div>
            <div class="mt-2 text-3xl font-semibold">{{ number_format($this->totals['all'] ?? 0) }}</div>
            <div class="mt-1 text-xs text-gray-500">Terfilter: {{ number_format($this->totals['filtered'] ?? 0) }}</div>
        </button>

        <a href="{{ route('employees.resign') }}" wire:navigate class="text-left rounded-xl border border-gray-200 bg-white p-5 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-gray-500">Karyawan Resign</div>
            <div class="mt-2 text-3xl font-semibold">{{ number_format($this->totals['resign'] ?? 0) }}</div>
        </a>

        <button type="button" wire:click="$set('status','tetap')" class="text-left rounded-xl border border-gray-200 bg-white p-5 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-gray-500">Pegawai Tetap</div>
            <div class="mt-2 text-3xl font-semibold">{{ number_format($this->totals['tetap'] ?? 0) }}</div>
        </button>

        <button type="button" wire:click="$set('status','kontrak')" class="text-left rounded-xl border border-gray-200 bg-white p-5 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-gray-500">Kontrak</div>
            <div class="mt-2 text-3xl font-semibold">{{ number_format($this->totals['kontrak'] ?? 0) }}</div>
        </button>
    </div>

    <!-- Charts: time series + donut -->
    <div class="grid gap-4 lg:grid-cols-3">
        <!-- Inline SVG chart -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 shadow-sm relative lg:col-span-2">
        @php
            $labels = $this->series['labels'] ?? [];
            $values = $this->series['values'] ?? [];
            $w = max(320, count($labels) * 20); // width per point
            $h = 140;
            $pad = 24;
            $max = max(1, max($values ?: [0]));
            $scaleY = ($h - $pad * 2) / $max;
            $points = [];
            foreach ($values as $i => $v) {
                $x = $pad + ($i * (($w - $pad*2) / max(1, count($values)-1)));
                $y = $h - $pad - ($v * $scaleY);
                $points[] = [$x, $y];
            }
        @endphp
        <div class="mb-2 flex items-center justify-between">
            <div class="text-sm font-medium">Penambahan Pegawai per Hari</div>
            <div class="flex items-center gap-3 text-xs text-gray-500">
                <span>{{ count($labels) }} hari</span>
                @php $delta = $this->trend['delta'] ?? 0; @endphp
                <span class="inline-flex items-center gap-1 {{ ($delta ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M3 10h14M10 3l7 7-7 7"/></svg>
                    {{ ($delta >= 0 ? '+' : '') . ($delta) }}%
                </span>
            </div>
        </div>
        <div class="overflow-x-auto" wire:loading.class="opacity-50">
            <svg viewBox="0 0 {{ $w }} {{ $h }}" width="100%" height="{{ $h }}" class="min-w-full">
                <!-- axes -->
                <line x1="{{ $pad }}" y1="{{ $h - $pad }}" x2="{{ $w - $pad }}" y2="{{ $h - $pad }}" stroke="#d1d5db" stroke-width="1" />
                <line x1="{{ $pad }}" y1="{{ $pad }}" x2="{{ $pad }}" y2="{{ $h - $pad }}" stroke="#d1d5db" stroke-width="1" />
                <!-- area -->
                @if(count($points) > 1)
                    @php
                        $path = 'M ' . $points[0][0] . ' ' . $points[0][1];
                        for ($i = 1; $i < count($points); $i++) {
                            $path .= ' L ' . $points[$i][0] . ' ' . $points[$i][1];
                        }
                        $area = $path . ' L ' . ($points[count($points)-1][0]) . ' ' . ($h - $pad) . ' L ' . $points[0][0] . ' ' . ($h - $pad) . ' Z';
                    @endphp
                    <path d="{{ $area }}" fill="#60a5fa22" />
                    <path d="{{ $path }}" fill="none" stroke="#3b82f6" stroke-width="2" />
                    @foreach($points as $idx => $pt)
                        <circle cx="{{ $pt[0] }}" cy="{{ $pt[1] }}" r="3" fill="#3b82f6">
                            <title>{{ $labels[$idx] ?? '' }}: {{ $values[$idx] ?? 0 }}</title>
                        </circle>
                    @endforeach
                @endif
                <!-- x labels -->
                @foreach($labels as $i => $lab)
                    @php
                        $x = $pad + ($i * (($w - $pad*2) / max(1, count($labels)-1)));
                    @endphp
                    @if($i % max(1, intdiv(count($labels), 6)) === 0)
                        <text x="{{ $x }}" y="{{ $h - ($pad - 6) }}" font-size="10" text-anchor="middle" fill="#6b7280">{{ $lab }}</text>
                    @endif
                @endforeach
            </svg>
        </div>
        <div wire:loading class="absolute inset-0 flex items-center justify-center bg-white/40 dark:bg-gray-900/40 rounded-xl">
            <div class="text-xs text-gray-600 dark:text-gray-300">Memuat grafik…</div>
        </div>
        <!-- Donut chart -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
            <div class="mb-2 flex items-center justify-between">
                <div class="text-sm font-medium">Komposisi Kepegawaian</div>
                <button type="button" class="text-xs text-blue-600 hover:underline" wire:click="$set('status','all')">Reset</button>
            </div>
            @php
                $data = $this->filteredBreakdown;
                $sum = max(0, array_sum($data));
                $r = 52; $cx = 64; $cy = 64; $circ = 2 * pi() * $r;
                $colors = ['Pegawai Tetap' => '#3b82f6', 'Kontrak' => '#f59e0b', 'Lain' => '#9ca3af'];
                $offset = 0;
            @endphp
            <div class="flex items-center gap-4">
                <svg viewBox="0 0 128 128" width="128" height="128">
                    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="transparent" stroke="#e5e7eb" stroke-width="16" />
                    @foreach($data as $label => $val)
                        @php
                            $frac = $sum > 0 ? $val / $sum : 0;
                            $len = $frac * $circ;
                        @endphp
                        @if($len > 0)
                            <circle
                                cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
                                fill="transparent"
                                stroke="{{ $colors[$label] ?? '#999' }}"
                                stroke-width="16"
                                stroke-dasharray="{{ $len }} {{ $circ - $len }}"
                                stroke-dashoffset="-{{ $offset }}"
                                transform="rotate(-90 {{ $cx }} {{ $cy }})"
                            >
                                <title>{{ $label }}: {{ $val }}</title>
                            </circle>
                            @php $offset += $len; @endphp
                        @endif
                    @endforeach
                </svg>
                <ul class="text-sm">
                    @foreach($data as $label => $val)
                        @php $pct = $sum > 0 ? round(($val/$sum)*100) : 0; @endphp
                        <li class="flex items-center gap-2">
                            <span class="inline-block h-3 w-3 rounded-sm" style="background: {{ $colors[$label] ?? '#999' }}"></span>
                            <button type="button" class="hover:underline" wire:click="$set('status','{{ $label === 'Pegawai Tetap' ? 'tetap' : ($label === 'Kontrak' ? 'kontrak' : 'all') }}')">
                                {{ $label }}
                            </button>
                            <span class="ms-2 text-gray-500">{{ $val }} ({{ $pct }}%)</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent employees & Top Lembaga -->
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800 shadow-sm lg:col-span-2 relative">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-medium">Recent Pegawai</h2>
            <a href="{{ route('employees.index') }}" wire:navigate class="text-sm text-blue-600 hover:underline">Lihat semua →</a>
        </div>

            @if($this->recentEmployees->count())
                <div class="overflow-x-auto" wire:loading.class="opacity-50">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr class="text-left">
                                <th class="p-2 font-medium">#</th>
                                <th class="p-2 font-medium">Name</th>
                                <th class="p-2 font-medium">Employee #</th>
                                <th class="p-2 font-medium">Position</th>
                                <th class="p-2 font-medium">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->recentEmployees as $emp)
                                <tr class="border-t border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700">
                                    <td class="p-2">{{ $emp->id }}</td>
                                    <td class="p-2">{{ $emp->name }}</td>
                                    <td class="p-2">{{ $emp->employee_number }}</td>
                                    <td class="p-2">{{ $emp->position }}</td>
                                    <td class="p-2">{{ optional($emp->created_at)->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-gray-600 dark:text-gray-300">Belum ada pegawai sesuai filter.</div>
            @endif
            <div wire:loading class="absolute inset-0 flex items-center justify-center bg-white/40 dark:bg-gray-800/40 rounded-xl">
                <div class="text-xs text-gray-600 dark:text-gray-300">Memuat data…</div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-medium">Top Lembaga</h2>
                <button type="button" class="text-xs text-blue-600 hover:underline" wire:click="$set('lembaga','all')">Reset</button>
            </div>
            @if($this->topLembaga->count())
                <ul class="space-y-2">
                    @foreach($this->topLembaga as $row)
                        <li class="flex items-center justify-between">
                            <button type="button" class="text-left hover:underline" wire:click="$set('lembaga','{{ $row->lembaga }}')">{{ $row->lembaga }}</button>
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs dark:bg-gray-700">{{ $row->c }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-gray-600 dark:text-gray-300">Tidak ada data lembaga.</div>
            @endif
        </div>
    </div>
</div>