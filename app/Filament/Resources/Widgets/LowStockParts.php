<?php

namespace App\Filament\Widgets;

use App\Models\Sparepart;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockParts extends BaseWidget
{
    protected static ?string $heading = '⚠️ Peringatan Sparepart Kritis (Stok < 5)';
    protected static ?int $sort = 3;
    protected static bool $isLazy = true;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Sparepart::query()->where('stok', '<', 5)->orderBy('stok', 'asc'))
            ->columns([
                Tables\Columns\TextColumn::make('nama_sparepart')
                    ->label('Nama Barang')
                    ->weight('semibold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('stok')
                    ->label('Sisa')
                    ->badge()
                    ->color('danger')
                    ->suffix(' Unit')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('code_part')
                    ->label('Kode Part')
                    ->copyable()
                    ->icon('heroicon-m-clipboard')
                    ->color('gray'),
            ])
            ->paginated([5]);
    }
}
