<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_number',
        'nik',
        'name',
        'position',
        'department',
        'status_kepegawaian',
        'tahun_masuk',
        'date_joined',
        'golongan',
        'pangkat',
        'job_level',
        'place_of_birth',
        'date_of_birth',
        'alamat',
        'jenis_kelamin',
        'marital_status',
        'jml_anggota_keluarga',
        'jumlah_anak',
        'phone',
        'email',
        'npwp',
        'bank_account',
        'bpjs_number',
        'profile_photo_path',
        'kesehatan',
        'pendidikan_terakhir',
        'ijazah_tambahan',
        'lembaga',
        'aktif',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_joined' => 'date',
        'npwp' => 'encrypted',
        'bank_account' => 'encrypted',
        'bpjs_number' => 'encrypted',
    ];

    protected static function booted(): void
    {
        static::created(function (self $employee) {
            \App\Models\EmployeeChange::create([
                'employee_id' => $employee->id,
                'user_id' => Auth::id(),
                'action' => 'created',
                'changes' => $employee->getAttributes(),
            ]);
        });

        static::updated(function (self $employee) {
            $changes = $employee->getChanges();
            unset($changes['updated_at']);
            if (! empty($changes)) {
                \App\Models\EmployeeChange::create([
                    'employee_id' => $employee->id,
                    'user_id' => Auth::id(),
                    'action' => 'updated',
                    'changes' => $changes,
                ]);
            }
        });

        static::deleted(function (self $employee) {
            \App\Models\EmployeeChange::create([
                'employee_id' => $employee->id,
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'changes' => $employee->getAttributes(),
            ]);
        });
    }

    public function changes()
    {
        return $this->hasMany(\App\Models\EmployeeChange::class);
    }
}
