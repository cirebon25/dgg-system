<?php

namespace App\Filament\Widgets;

use App\Models\MachinePart;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class PartLifespanAlert extends BaseWidget
{
    protected static ?string $heading = '🚨 Peringatan Umur Part (>90%)';

    protected static bool $isLazy = true;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    // Solusi Error TypeError: Return value must be string
    public function getTableRecordKey($record): string
    {
        return (string) $record->id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Kueri satu arah yang bersih
                MachinePart::query()
                    ->whereColumn('current_usage', '>=', DB::raw('limit_usage * 0.9'))
                    ->with(['machine', 'sparepart'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN Mesin')
                    ->description(fn ($record) => $record->machine->model_mesin ?? '-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Part')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('usage_percentage')
                    ->label('Pemakaian')
                    ->getStateUsing(function ($record) {
                        if ($record->limit_usage <= 0) {
                            return '0%';
                        }
                        $percent = ($record->current_usage / $record->limit_usage) * 100;

                        return number_format($percent, 0).'%';
                    })
                    ->badge()
                    ->color(fn ($state) => (int) $state >= 95 ? 'danger' : 'warning'),

                Tables\Columns\TextColumn::make('remaining')
                    ->label('Sisa Umur')
                    ->getStateUsing(fn ($record) => ($record->limit_usage - $record->current_usage).' Klik')
                    ->icon('heroicon-m-clock'),

                Tables\Columns\TextColumn::make('machine.deployment.customer.nama_customer')
                    ->label('Lokasi Customer')
                    ->color('gray'),
            ])
            ->paginated([5, 10]);
    }
}
