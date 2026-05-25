<?php

namespace App\Filament\Widgets;

use App\Models\Sparepart;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopSpareparts extends BaseWidget
{
    protected static ?string $heading = '📊 Top 5 Sparepart Terlaris (Keluar Terbanyak)';
    protected static bool $isLazy = true;
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Menggunakan 'saldo_keluar' sesuai hasil cek skema database Anda
                Sparepart::query()
                    ->orderBy('saldo_keluar', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_sparepart')
                    ->label('Nama Sparepart')
                    ->weight('semibold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('saldo_keluar')
                    ->label('Total Keluar')
                    ->badge()
                    ->color('warning')
                    ->suffix(' Pcs')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('stok')
                    ->label('Sisa Stok Gudang')
                    ->color('gray'),
            ])
            // Menghilangkan opsi "5, 10, 25, All" agar layout tidak rusak
            ->paginated(false)

            // Pengaman Anti-Glitch: Menjaga tinggi kotak tetap stabil & estetik saat data masih kosong (null)
            ->emptyStateHeading('Belum Ada Data Suku Cadang')
            ->emptyStateDescription('Data sparepart terlaris akan muncul otomatis setelah saldo keluar terisi.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver');
    }
}
