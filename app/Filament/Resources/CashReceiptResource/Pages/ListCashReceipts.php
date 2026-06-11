<?php
namespace App\Filament\Resources\CashReceiptResource\Pages;
use App\Filament\Resources\CashReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListCashReceipts extends ListRecords {
    protected static string $resource = CashReceiptResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()->label('+ Input Kas Masuk')];
    }
}
