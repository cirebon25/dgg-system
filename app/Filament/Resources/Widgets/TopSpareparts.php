<?php

namespace App\Filament\Widgets;

use App\Models\ServiceLogSparepart;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopSpareparts extends BaseWidget
{
    protected static ?string $heading = '📊 Sparepart Terlaris (Keluar Terbanyak)';

    protected static bool $isLazy = true;

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ServiceLogSparepart::query()
                    ->select('sparepart_id', DB::raw('SUM(jumlah) as total_out'))
                    ->groupBy('sparepart_id')
                    ->orderBy('total_out', 'desc')
                    ->limit(5)
            )
            // ->recordKey(fn ($record) => $record->sparepart_id)
            ->columns([
                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Barang')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('total_out')
                    ->label('Total Keluar')
                    ->badge()
                    ->color('info')
                    ->suffix(' Unit'),
                
                Tables\Columns\TextColumn::make('sparepart.stok')
                    ->label('Sisa Gudang')
                    ->numeric()
                    ->alignCenter(),
            ]);
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->sparepart_id;
    }
}