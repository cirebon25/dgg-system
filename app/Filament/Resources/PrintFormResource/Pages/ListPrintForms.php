<?php
 
namespace App\Filament\Resources\PrintFormResource\Pages;
 
use App\Filament\Resources\PrintFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
 
class ListPrintForms extends ListRecords
{
    protected static string $resource = PrintFormResource::class;
 
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}