<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\ResignedEmployee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DashboardWidget extends Component
{
    public string $range = '30d'; // 7d|30d|90d|all
    public string $status = 'all'; // all|tetap|kontrak
    public string $aktif = 'all'; // all|Aktif|Tidak Aktif
    public string $lembaga = 'all';
    public string $search = '';

    protected $updatesQueryString = ['range', 'status', 'aktif', 'lembaga', 'search'];

    #[Computed]
    public function baseQuery()
    {
        $q = Employee::query();

        if ($this->status === 'tetap') {
            $q->where('status_kepegawaian', 'Pegawai Tetap');
        } elseif ($this->status === 'kontrak') {
            $q->where('status_kepegawaian', 'Kontrak');
        }

        if ($this->aktif !== 'all') {
            $q->where('aktif', $this->aktif);
        }

        if ($this->lembaga !== 'all') {
            $q->where('lembaga', $this->lembaga);
        }

        if (trim($this->search) !== '') {
            $s = trim($this->search);
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                    ->orWhere('employee_number', 'like', "%{$s}%")
                    ->orWhere('position', 'like', "%{$s}%")
                    ->orWhere('lembaga', 'like', "%{$s}%");
            });
        }

        return $q;
    }

    #[Computed]
    public function totals()
    {
        return [
            'all' => Employee::count(),
            'filtered' => (clone $this->baseQuery())->count(),
            'aktif' => Employee::where('aktif', 'Aktif')->count(),
            'tetap' => Employee::where('status_kepegawaian', 'Pegawai Tetap')->count(),
            'kontrak' => Employee::where('status_kepegawaian', 'Kontrak')->count(),
            'resign' => ResignedEmployee::count(),
        ];
    }

    #[Computed]
    public function lembagaOptions()
    {
        return Employee::query()
            ->whereNotNull('lembaga')
            ->distinct()
            ->orderBy('lembaga')
            ->pluck('lembaga')
            ->toArray();
    }

    #[Computed]
    public function recentEmployees()
    {
        return (clone $this->baseQuery())
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();
    }

    #[Computed]
    public function series()
    {
        // Determine date range
        $end = Carbon::today();
        $start = match ($this->range) {
            '7d' => $end->copy()->subDays(6),
            '30d' => $end->copy()->subDays(29),
            '90d' => $end->copy()->subDays(89),
            default => null,
        };

        $query = (clone $this->baseQuery());
        if ($start) {
            $query->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
        }

        // Group by date(created_at)
        $rows = $query
            ->select(DB::raw("date(created_at) as d"), DB::raw('count(*) as c'))
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        // Build continuous series
        $labels = [];
        $values = [];
        if ($start) {
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $d = $cursor->toDateString();
                $labels[] = $cursor->isoFormat('DD MMM');
                $values[] = (int) ($rows[$d]->c ?? 0);
                $cursor->addDay();
            }
        } else {
            // "all" — just take up to last 30 points for sanity
            foreach ($rows as $r) {
                $labels[] = Carbon::parse($r->d)->isoFormat('DD MMM');
                $values[] = (int) $r->c;
            }
            $labels = array_slice($labels, -30);
            $values = array_slice($values, -30);
        }

        return compact('labels', 'values');
    }

    #[Computed]
    public function trend()
    {
        $end = Carbon::today();
        $start = match ($this->range) {
            '7d' => $end->copy()->subDays(6),
            '30d' => $end->copy()->subDays(29),
            '90d' => $end->copy()->subDays(89),
            default => null,
        };

        $current = 0;
        $previous = 0;

        $qCurrent = (clone $this->baseQuery());
        if ($start) {
            $qCurrent->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
        }
        $current = (int) $qCurrent->count();

        if ($start) {
            $span = $start->diffInDays($end) + 1; // inclusive
            $prevEnd = $start->copy()->subDay();
            $prevStart = $prevEnd->copy()->subDays($span - 1);
            $qPrev = (clone $this->baseQuery())
                ->whereBetween('created_at', [$prevStart->startOfDay(), $prevEnd->endOfDay()]);
            $previous = (int) $qPrev->count();
        }

        $delta = $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0);

        return [
            'current' => $current,
            'previous' => $previous,
            'delta' => round($delta, 1),
        ];
    }

    #[Computed]
    public function topLembaga()
    {
        return (clone $this->baseQuery())
            ->select('lembaga', DB::raw('count(*) as c'))
            ->whereNotNull('lembaga')
            ->groupBy('lembaga')
            ->orderByDesc('c')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function filteredBreakdown()
    {
        $rows = (clone $this->baseQuery())
            ->select('status_kepegawaian as s', DB::raw('count(*) as c'))
            ->groupBy('s')
            ->get();

        $map = [
            'Pegawai Tetap' => 0,
            'Kontrak' => 0,
            'Lain' => 0,
        ];

        foreach ($rows as $r) {
            if ($r->s === 'Pegawai Tetap') {
                $map['Pegawai Tetap'] += (int) $r->c;
            } elseif ($r->s === 'Kontrak') {
                $map['Kontrak'] += (int) $r->c;
            } else {
                $map['Lain'] += (int) $r->c;
            }
        }

        return $map;
    }

    public function export()
    {
        $filename = 'pegawai_export_' . now()->format('Ymd_His') . '.csv';
        $columns = ['id', 'employee_number', 'name', 'position', 'status_kepegawaian', 'aktif', 'lembaga', 'created_at'];
        $query = (clone $this->baseQuery())->orderBy('id');

        return response()->streamDownload(function () use ($query, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            $query->chunk(500, function ($rows) use ($out, $columns) {
                foreach ($rows as $row) {
                    $line = [];
                    foreach ($columns as $col) {
                        $line[] = $row->{$col};
                    }
                    fputcsv($out, $line);
                }
            });
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function resetFilters(): void
    {
        $this->range = '30d';
        $this->status = 'all';
        $this->aktif = 'all';
        $this->lembaga = 'all';
        $this->search = '';
    }

    public function render()
    {
        return view('livewire.dashboard-widget');
    }
}
