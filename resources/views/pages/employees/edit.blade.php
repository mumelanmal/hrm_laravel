<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Employee;

new class extends Component {
    use WithFileUploads;

    public Employee $employee;
    public ?\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $photo = null;

    // Form fields (scalar) for reliable binding/prefill
    public string $name = '';
    public string $nik = '';
    public string $employee_number = '';
    public string $email = '';
    public string $phone = '';
    public string $position = '';
    public string $department = '';
    public ?string $date_of_birth = null;
    public ?string $date_joined = null;
    public string $status_kepegawaian = '';
    public string $aktif = 'Aktif';

    public function mount(Employee $employee): void
    {
        $this->authorize('update', $employee);
        $this->employee = $employee;

        // Prefill form fields from model
        $this->name = (string) ($employee->name ?? '');
        $this->nik = (string) ($employee->nik ?? '');
        $this->employee_number = (string) ($employee->employee_number ?? '');
        $this->email = (string) ($employee->email ?? '');
        $this->phone = (string) ($employee->phone ?? '');
        $this->position = (string) ($employee->position ?? '');
        $this->department = (string) ($employee->department ?? '');
        $this->date_of_birth = optional($employee->date_of_birth)->format('Y-m-d');
        $this->date_joined = optional($employee->date_joined)->format('Y-m-d');
        $this->status_kepegawaian = (string) ($employee->status_kepegawaian ?? '');
        $this->aktif = (string) ($employee->aktif ?? 'Aktif');
    }

    public function save(): void
    {
        $this->authorize('update', $this->employee);

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50',
            'employee_number' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'date_joined' => 'nullable|date',
            'status_kepegawaian' => 'nullable|string|max:100',
            'aktif' => 'required|string|max:50',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Assign back to model and save
        $this->employee->fill($validated);

        if ($this->photo) {
            $path = $this->photo->storePublicly('profiles', ['disk' => 'public']);
            $this->employee->profile_photo_path = $path;
        }

        $this->employee->save();
        session()->flash('status', 'Data pegawai berhasil disimpan');
    }
}; ?>

<section class="w-full">
    <div class="mx-auto max-w-3xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <flux:heading size="xl">Edit Pegawai</flux:heading>
            <flux:link :href="route('employees.index')" wire:navigate>Kembali</flux:link>
        </div>

        @if(session('status'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-3 py-2 text-green-700">{{ session('status') }}</div>
        @endif

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <form wire:submit="save" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:input wire:model="name" :label="__('Nama')" required class="md:col-span-2" />
                <flux:input wire:model="nik" :label="__('NIK/KTP')" />
                <flux:input wire:model="employee_number" :label="__('NIP/Nomor Pegawai')" />
                <flux:input wire:model="email" :label="__('Email')" type="email" />
                <flux:input wire:model="phone" :label="__('No HP')" />
                <flux:input wire:model="position" :label="__('Jabatan')" />
                <flux:input wire:model="department" :label="__('Departemen')" />
                <flux:input wire:model="date_of_birth" :label="__('Tanggal Lahir')" type="date" />
                <flux:input wire:model="date_joined" :label="__('Tanggal Masuk')" type="date" />
                <flux:input wire:model="status_kepegawaian" :label="__('Status Kepegawaian')" placeholder="Pegawai Tetap/Kontrak" />

                <div>
                    <flux:select wire:model="aktif" :label="__('Status')">
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </flux:select>
                </div>

                <div class="md:col-span-2">
                    <flux:input wire:model="photo" :label="__('Foto Profil')" type="file" accept="image/*" />
                    @if($employee->profile_photo_path)
                        <div class="mt-2 text-xs text-zinc-500">Foto saat ini:</div>
                        <img src="{{ Storage::disk('public')->url($employee->profile_photo_path) }}" alt="Foto profil" class="mt-1 h-20 w-20 rounded object-cover" />
                    @endif
                </div>

                <div class="md:col-span-2 mt-2 flex items-center justify-end gap-3">
                    <flux:link :href="route('employees.index')" wire:navigate>Batal</flux:link>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </div>
    @fluxScripts
</section>