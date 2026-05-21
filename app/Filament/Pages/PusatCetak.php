<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PusatCetak extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-printer';

    protected static ?string $navigationLabel = 'Pusat Cetak Dokumen';

    protected static ?string $title = '🖨️ Pusat Cetak Dokumen';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.pusat-cetak';
    
    protected static ?string $navigationGroup = 'Pusat Cetak';
}
