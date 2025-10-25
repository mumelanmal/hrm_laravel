<?php

namespace App\Filament\Resources\ResignedEmployeeResource\Pages;

use App\Filament\Resources\ResignedEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResignedEmployee extends EditRecord
{
    protected static string $resource = ResignedEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
