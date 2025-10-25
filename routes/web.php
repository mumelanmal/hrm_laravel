<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResignedEmployeeController;

Route::get('/', function () {
    return redirect('/admin');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Employees management (CSV import)
    Volt::route('employees', 'employees.index')->name('employees.index');
    Volt::route('employees/magang', 'employees.magang')->name('employees.magang');
    Volt::route('employees/resign', 'employees.resign')->name('employees.resign');
    Volt::route('employees/resign/{resigned}', 'employees.resigned-show')->name('resigned.show');
    Volt::route('employees/purna-magang', 'employees.purna-magang')->name('employees.purnaMagang');
    Volt::route('employees/create', 'employees.create')->name('employees.create');
    Volt::route('employees/{employee}/edit', 'employees.edit')->name('employees.edit');
    Volt::route('employees/{employee}', 'employees.show')->name('employees.show');
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Resigned employees import
    Route::post('resigned-employees/import', [ResignedEmployeeController::class, 'import'])->name('resignedEmployees.import');
    Route::post('resigned-employees/cleanup', [ResignedEmployeeController::class, 'cleanup'])->name('resignedEmployees.cleanup');
    Route::post('resigned-employees/purge', [ResignedEmployeeController::class, 'purge'])->name('resignedEmployees.purge');
});
