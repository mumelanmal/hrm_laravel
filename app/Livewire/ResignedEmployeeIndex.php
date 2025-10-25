<?php

namespace App\Livewire;

use App\Models\ResignedEmployee;
use Livewire\Component;
use Livewire\WithPagination;

class ResignedEmployeeIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 15;
    public string $sortField = 'date_resigned';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'date_resigned'],
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
        $q = ResignedEmployee::query();

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
            'name','employee_number','nik','position','lembaga','date_joined','date_resigned'
        ];
        $field = in_array($this->sortField, $allowedSorts) ? $this->sortField : 'date_resigned';
        $dir = $this->sortDirection === 'asc' ? 'asc' : 'desc';
        $q->orderBy($field, $dir)->orderBy('id', 'desc');

        $resigned = $q->paginate($this->perPage);

        return view('livewire.resigned-employee-index', [
            'resigned' => $resigned,
        ]);
    }
}
