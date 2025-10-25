<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'nik')) {
                $table->string('nik')->nullable()->after('employee_number');
            }
            if (! Schema::hasColumn('employees', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('employees', 'department')) {
                $table->string('department')->nullable()->after('position');
            }
            if (! Schema::hasColumn('employees', 'date_joined')) {
                $table->date('date_joined')->nullable()->after('tahun_masuk');
            }
            if (! Schema::hasColumn('employees', 'npwp')) {
                $table->text('npwp')->nullable()->after('email');
            }
            if (! Schema::hasColumn('employees', 'bank_account')) {
                $table->text('bank_account')->nullable()->after('npwp');
            }
            if (! Schema::hasColumn('employees', 'bpjs_number')) {
                $table->text('bpjs_number')->nullable()->after('bank_account');
            }
            if (! Schema::hasColumn('employees', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('bpjs_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            foreach (['nik','email','department','date_joined','npwp','bank_account','bpjs_number','profile_photo_path'] as $col) {
                if (Schema::hasColumn('employees', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
