<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class MachineLocationStats extends BaseWidget
{
    protected static ?string $heading = '📍 Sebaran Unit Per Wilayah';
    protected static bool $isLazy = true;
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 1;

    public function getTableRecordKey($record): string
    {
        return (string) $record->kota;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::query()
                    ->select('kota', DB::raw('count(*) as total_unit'))
                    ->whereNotNull('kota')
                    ->groupBy('kota')
                    ->orderBy('total_unit', 'desc')
                    ->limit(5) // Mengunci tinggi kotak maksimal 5 baris data
            )
            ->columns([
                Tables\Columns\TextColumn::make('kota')
                    ->label('Kota / Kabupaten')
                    ->icon('heroicon-m-map-pin')
                    ->iconColor('danger'),

                Tables\Columns\TextColumn::make('total_unit')
                    ->label('Unit Terpasang')
                    ->badge()
                    ->color('success')
                    ->suffix(' Mesin'),
            ])
            ->paginated(false); // Menghilangkan tulisan "5, 10, 25, All" yang merusak tampilan
    }
}
