<?php

namespace App\Filament\Pages;

use App\Models\PrintForm;
use Filament\Pages\Page;

class CetakForm extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationLabel = 'Cetak Form';
    protected static ?string $navigationGroup = 'Pusat Cetak';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.cetak-form';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'teknisi', 'keuangan', 'manager']);
    }

    public function getForms(): array
    {
        return PrintForm::latest()->get()->toArray();
    }

    protected function getViewData(): array
    {
        return [
            'forms' => PrintForm::latest()->get(),
        ];
    }
}