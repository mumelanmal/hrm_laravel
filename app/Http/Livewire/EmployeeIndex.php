<?php

namespace App\Http\Livewire;

use App\Models\Employee;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Employee::query();

        if ($this->search) {
            $q = trim($this->search);
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                    ->orWhere('employee_number', 'like', "%{$q}%")
                    ->orWhere('position', 'like', "%{$q}%")
                    ->orWhere('lembaga', 'like', "%{$q}%");
            });
        }

        $employees = $query->orderBy('id', 'desc')->paginate($this->perPage);

        $counts = [
            'total' => Employee::count(),
            'aktif' => Employee::where('aktif', 'Aktif')->count(),
            'kontrak' => Employee::where('status_kepegawaian', 'Kontrak')->count(),
            'tetap' => Employee::where('status_kepegawaian', 'Pegawai Tetap')->count(),
        ];

        return view('livewire.employee-index', compact('employees', 'counts'));
    }
}
