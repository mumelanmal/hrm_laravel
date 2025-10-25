<?php

namespace App\Livewire;

use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;
    public $status = 'all'; // maps to status_kepegawaian exact values, e.g., 'Pegawai Tetap', 'Kontrak'
    public $aktif = 'all';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $viewMode = 'compact'; // 'compact' | 'detail'

    protected $updatesQueryString = ['search', 'perPage', 'status', 'aktif', 'sortField', 'sortDirection', 'viewMode'];

    protected function allowedSorts(): array
    {
        return [
            'id', 'name', 'employee_number', 'position', 'lembaga', 'status_kepegawaian', 'phone', 'created_at', 'date_joined'
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingAktif()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function submitSearch(): void
    {
        // Using defer on the search input means this method applies the new term
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if (! in_array($field, $this->allowedSorts(), true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function toggleView(): void
    {
        $this->viewMode = $this->viewMode === 'compact' ? 'detail' : 'compact';
        // Notify browser to persist preference
        $this->dispatch('view-mode-changed', mode: $this->viewMode);
    }

    public function render()
    {
        $query = Employee::query();

        if ($this->search) {
            $q = trim($this->search);
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                    ->orWhere('employee_number', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('position', 'like', "%{$q}%")
                    ->orWhere('lembaga', 'like', "%{$q}%")
                    ->orWhere('status_kepegawaian', 'like', "%{$q}%");
            });
        }

        if ($this->status !== 'all') {
            $query->where('status_kepegawaian', $this->status);
        }

        if ($this->aktif !== 'all') {
            $query->where('aktif', $this->aktif);
        }

    // Apply sorting
    $sortField = in_array($this->sortField, $this->allowedSorts(), true) ? $this->sortField : 'id';
    $sortDirection = $this->sortDirection === 'asc' ? 'asc' : 'desc';

    $employees = $query->orderBy($sortField, $sortDirection)->paginate($this->perPage);

        $counts = [
            'total' => Employee::count(),
            'aktif' => Employee::where('aktif', 'Aktif')->count(),
            'kontrak' => Employee::where('status_kepegawaian', 'Kontrak')->count(),
            'tetap' => Employee::where('status_kepegawaian', 'Pegawai Tetap')->count(),
        ];

        $statuses = Employee::query()->whereNotNull('status_kepegawaian')->distinct()->orderBy('status_kepegawaian')->pluck('status_kepegawaian');

        return view('livewire.employee-index', [
            'employees' => $employees,
            'counts' => $counts,
            'statuses' => $statuses,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
