<?php

namespace App\Livewire;

use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;

class ResignedEmployeeIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 15;
    public string $sortField = 'deleted_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'deleted_at'],
        'sortDirection' => ['except' => 'desc'],
        'page' => ['except' => 1],
        'perPage' => ['except' => 15],
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $q = Employee::onlyTrashed();

        if ($this->search) {
            $term = '%' . str_replace(' ', '%', $this->search) . '%';
            $q->where(function($w) use ($term){
                $w->where('name', 'like', $term)
                  ->orWhere('employee_number', 'like', $term)
                  ->orWhere('nik', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('position', 'like', $term)
                  ->orWhere('lembaga', 'like', $term)
                  ->orWhere('alasan_resign', 'like', $term)
                  ->orWhere('keterangan', 'like', $term);
            });
        }

        $allowedSorts = [
            'name','employee_number','nik','position','lembaga','date_joined','date_resigned','deleted_at'
        ];
        $field = in_array($this->sortField, $allowedSorts) ? $this->sortField : 'deleted_at';
        $dir = $this->sortDirection === 'asc' ? 'asc' : 'desc';
        $q->orderBy($field, $dir)->orderBy('id', 'desc');

        $resigned = $q->paginate($this->perPage);

        return view('livewire.resigned-employee-index', [
            'resigned' => $resigned,
        ]);
    }
}
