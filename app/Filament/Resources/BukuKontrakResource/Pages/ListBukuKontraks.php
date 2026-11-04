<?php

namespace App\Filament\Resources\BukuKontrakResource\Pages;

use App\Filament\Resources\BukuKontrakResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBukuKontraks extends ListRecords
{
    protected static string $resource = BukuKontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
