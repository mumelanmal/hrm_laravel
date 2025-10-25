<?php

namespace App\Filament\Resources\ResignedEmployeeResource\Pages;

use App\Filament\Resources\ResignedEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateResignedEmployee extends CreateRecord
{
    protected static string $resource = ResignedEmployeeResource::class;
}
