<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Employee;

new class extends Component {
    use WithFileUploads;

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
    public ?\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $photo = null;

    public function save(): void
    {
        $this->authorize('create', Employee::class);

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

        $data = $validated;
        if ($this->photo) {
            $path = $this->photo->storePublicly('profiles', ['disk' => 'public']);
            $data['profile_photo_path'] = $path;
        }

        Employee::create($data);

        session()->flash('status', 'Pegawai berhasil ditambahkan');
        $this->redirect(route('employees.index'), navigate: true);
    }
}; ?>

<section class="w-full">
    <div class="mx-auto max-w-3xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <flux:heading size="xl">Tambah Pegawai</flux:heading>
            <flux:link :href="route('employees.index')" wire:navigate>Kembali</flux:link>
        </div>

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
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </flux:select>
                </div>

                <div class="md:col-span-2">
                    <flux:input wire:model="photo" :label="__('Foto Profil')" type="file" accept="image/*" />
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