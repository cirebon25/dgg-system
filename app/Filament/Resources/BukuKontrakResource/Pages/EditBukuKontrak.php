<?php

namespace App\Filament\Resources\BukuKontrakResource\Pages;

use App\Filament\Resources\BukuKontrakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBukuKontrak extends EditRecord
{
    protected static string $resource = BukuKontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
