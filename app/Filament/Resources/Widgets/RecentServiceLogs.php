<?php

namespace App\Filament\Widgets;

use App\Models\ServiceLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentServiceLogs extends BaseWidget
{
    protected static ?string $heading = '🔧 Aktivitas Servis Terakhir';
    protected static bool $isLazy = true;
    protected static ?int $sort = 6;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(ServiceLog::query()->latest()->with(['machine.deployment.customer', 'technician'])->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date('d/m/Y')
                    ->label('Tgl'),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN Mesin')
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('machine.deployment.customer.nama_customer')
                    ->label('Customer')
                    ->color('gray')
                    ->limit(15),

                Tables\Columns\TextColumn::make('perbaikan')
                    ->label('Tindakan')
                    ->limit(20),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->badge()
                    ->color('info'),
            ])
            ->paginated(false);
    }
}
