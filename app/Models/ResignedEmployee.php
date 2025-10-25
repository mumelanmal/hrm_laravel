<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResignedEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'employee_number',
        'amanah_pokok',
        'status_kepegawaian',
        'nik',
        'email',
        'phone',
        'kesehatan',
        'position',
        'lembaga',
        'aktif',
        'date_joined',
        'tahun_masuk',
        'date_resigned',
        'golongan',
        'pangkat',
        'job_level',
        'place_of_birth',
        'date_of_birth',
        'jenis_kelamin',
        'marital_status',
        'jml_anggota_keluarga',
        'jumlah_anak',
        'alamat',
        'pendidikan_terakhir',
        'ijazah_tambahan',
        'alasan_resign',
        'keterangan',
    ];

    protected $casts = [
        'date_joined' => 'date',
        'date_resigned' => 'date',
        'date_of_birth' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
