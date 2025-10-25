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
        Schema::table('employees', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at column
            $table->date('date_resigned')->nullable()->after('aktif');
            $table->string('alasan_resign')->nullable()->after('date_resigned');
            $table->text('keterangan')->nullable()->after('alasan_resign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['date_resigned', 'alasan_resign', 'keterangan']);
        });
    }
};
