<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('resigned_employees');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('resigned_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('name')->nullable();
            $table->string('employee_number')->nullable();
            $table->string('amanah_pokok')->nullable();
            $table->string('status_kepegawaian')->nullable();
            $table->string('nik')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('kesehatan')->nullable();
            $table->string('position')->nullable();
            $table->string('lembaga')->nullable();
            $table->string('aktif')->nullable();
            $table->date('date_joined')->nullable();
            $table->string('tahun_masuk')->nullable();
            $table->date('date_resigned')->nullable();
            $table->string('golongan')->nullable();
            $table->string('pangkat')->nullable();
            $table->string('job_level')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('marital_status')->nullable();
            $table->integer('jml_anggota_keluarga')->nullable();
            $table->integer('jumlah_anak')->nullable();
            $table->text('alamat')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('ijazah_tambahan')->nullable();
            $table->string('alasan_resign')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }
};
