<?php

namespace App\Filament\Widgets;

use App\Models\Deployment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestDeployments extends BaseWidget
{
    protected static ?string $heading = '📦 10 Penempatan Mesin Terakhir';
    protected static ?int $sort = 7;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Deployment::query()->latest()->with(['customer', 'machine', 'technician'])->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Nama Customer')
                    ->icon('heroicon-m-user')
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('Serial Number')
                    ->icon('heroicon-m-cpu-chip')
                    ->copyable()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('tanggal_instal')
                    ->label('Tanggal Pasang')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->badge()
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
