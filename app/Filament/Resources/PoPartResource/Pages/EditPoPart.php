<?php

namespace App\Filament\Resources\PoPartResource\Pages;

use App\Filament\Resources\PoPartResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPoPart extends EditRecord
{
    protected static string $resource = PoPartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $formItems = $this->data['items'] ?? [];

        foreach ($this->record->items as $index => $item) {
            $formData = $formItems[$index] ?? null;
            if (!$formData) continue;

            $dariDropdown = $formData['dari_dropdown'] ?? true;

            if (!$dariDropdown) {
                $namaPart = $formData['nama_part_manual'] ?? '';
                if (filled($namaPart)) {
                    $item->update(['nama_part' => $namaPart]);
                }
            }
        }
    }
}
