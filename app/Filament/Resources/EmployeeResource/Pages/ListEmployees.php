<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Http\Controllers\EmployeeController;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    FileUpload::make('csv_file')
                        ->label('File CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $controller = new EmployeeController();
                    $request = new \Illuminate\Http\Request();
                    $request->files->set('csv_file', $data['csv_file']);
                    
                    try {
                        $controller->import($request);
                        Notification::make()
                            ->success()
                            ->title('Import berhasil')
                            ->body('Data pegawai berhasil diimport')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Import gagal')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
            Actions\Action::make('export')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('employees.export')),
            Actions\CreateAction::make(),
        ];
    }
}
