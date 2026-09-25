<?php

namespace App\Filament\Resources\DeployMachineRoResource\Pages;

use App\Filament\Resources\DeployMachineRoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeployMachineRos extends ListRecords
{
    protected static string $resource = DeployMachineRoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
