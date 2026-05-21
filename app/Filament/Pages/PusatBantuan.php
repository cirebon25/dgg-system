<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PusatBantuan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationLabel = 'Pusat Bantuan';
    protected static ?string $title = 'Pusat Bantuan & Panduan Sistem DGG';
    protected static ?string $navigationGroup = 'Bantuan';
    protected static string $view = 'filament.pages.pusat-bantuan';
}