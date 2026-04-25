<?php

namespace App\Filament\Widgets;

use App\Models\Deployment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestDeployments extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '10 Penempatan Mesin Terakhir'; // Judulnya juga kita ganti

    public function table(Table $table): Table
    {
        return $table
            // Angka 5 diganti jadi 10 di sini
            ->query(Deployment::query()->latest()->limit(10)) 
            ->columns([
                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Nama Customer')
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('Serial Number')
                    ->icon('heroicon-m-cpu-chip')
                    ->copyable(),

                Tables\Columns\TextColumn::make('tanggal_instal')
                    ->label('Tanggal Pasang')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->badge()
                    ->color('gray'),
            ]);
    }
}