<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('resigned_employees', function (Blueprint $table) {
            $table->string('amanah_pokok')->nullable()->after('employee_number');
            $table->string('status_kepegawaian')->nullable()->after('amanah_pokok');
            $table->string('tahun_masuk')->nullable()->after('date_joined');
            $table->string('kesehatan')->nullable()->after('phone');
            $table->string('pendidikan_terakhir')->nullable()->after('kesehatan');
            $table->string('ijazah_tambahan')->nullable()->after('pendidikan_terakhir');
            $table->string('aktif', 20)->nullable()->after('lembaga');
            $table->unsignedInteger('jml_anggota_keluarga')->nullable()->after('marital_status');
            $table->unsignedInteger('jumlah_anak')->nullable()->after('jml_anggota_keluarga');
        });
    }

    public function down(): void
    {
        Schema::table('resigned_employees', function (Blueprint $table) {
            $table->dropColumn([
                'amanah_pokok',
                'status_kepegawaian',
                'tahun_masuk',
                'kesehatan',
                'pendidikan_terakhir',
                'ijazah_tambahan',
                'aktif',
                'jml_anggota_keluarga',
                'jumlah_anak',
            ]);
        });
    }
};
