<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resigned_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('name')->index();
            $table->string('employee_number')->nullable()->index();
            $table->string('nik', 100)->nullable()->index();
            $table->string('email')->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('position')->nullable();
            $table->string('lembaga')->nullable();
            $table->date('date_joined')->nullable();
            $table->date('date_resigned')->nullable()->index();
            $table->string('golongan')->nullable();
            $table->string('pangkat')->nullable();
            $table->string('job_level')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->text('alasan_resign')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resigned_employees');
    }
};
