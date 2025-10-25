<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\ResignedEmployee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('aktif', 'Aktif')->count();
        $inactiveEmployees = Employee::where('aktif', 'Nonaktif')->count();
        $resignedTotal = ResignedEmployee::count();
        
        return [
            Stat::make('Total Pegawai', $totalEmployees)
                ->description('Seluruh data pegawai')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            
            Stat::make('Pegawai Aktif', $activeEmployees)
                ->description('Pegawai yang masih aktif')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Pegawai Nonaktif', $inactiveEmployees)
                ->description('Pegawai yang sudah nonaktif')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('warning'),
            
            Stat::make('Data Resign', $resignedTotal)
                ->description('Total data pegawai resign')
                ->descriptionIcon('heroicon-m-arrow-right-start-on-rectangle')
                ->color('danger'),
        ];
    }
}
