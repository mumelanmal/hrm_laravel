<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate data from resigned_employees to employees
        $resignedEmployees = DB::table('resigned_employees')->get();

        foreach ($resignedEmployees as $resigned) {
            // Update the corresponding employee record
            DB::table('employees')
                ->where('id', $resigned->employee_id)
                ->update([
                    'date_resigned' => $resigned->date_resigned,
                    'alasan_resign' => $resigned->alasan_resign,
                    'keterangan' => $resigned->keterangan,
                    'deleted_at' => $resigned->date_resigned, // Soft delete with resign date
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset the resigned employee data back
        DB::table('employees')
            ->whereNotNull('deleted_at')
            ->update([
                'date_resigned' => null,
                'alasan_resign' => null,
                'keterangan' => null,
                'deleted_at' => null,
            ]);
    }
};
