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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('status_kepegawaian')->nullable();
            $table->string('tahun_masuk')->nullable();
            $table->string('golongan')->nullable();
            $table->string('pangkat')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('alamat')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('marital_status')->nullable();
            $table->integer('jml_anggota_keluarga')->nullable();
            $table->integer('jumlah_anak')->nullable();
            $table->string('phone')->nullable();
            $table->string('kesehatan')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('ijazah_tambahan')->nullable();
            $table->string('lembaga')->nullable();
            $table->string('aktif')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
