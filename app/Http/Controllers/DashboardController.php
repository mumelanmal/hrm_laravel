<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalEmployees = Employee::count();
        $recentEmployees = Employee::orderBy('created_at', 'desc')->limit(8)->get();

        $aktifCount = Employee::where('aktif', 'Aktif')->count();
        $tetapCount = Employee::where('status_kepegawaian', 'Pegawai Tetap')->count();
        $kontrakCount = Employee::where('status_kepegawaian', 'Kontrak')->count();

        return view('dashboard', compact(
            'totalEmployees',
            'recentEmployees',
            'aktifCount',
            'tetapCount',
            'kontrakCount'
        ));
    }
}
