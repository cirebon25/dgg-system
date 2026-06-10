<?php
namespace App\Filament\Resources\PrintFormResource\Pages;

use App\Filament\Resources\PrintFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrintForm extends EditRecord
{
protected static string $resource = PrintFormResource::class;

protected function getHeaderActions(): array
{
return [
Actions\DeleteAction::make(),
];
}
}