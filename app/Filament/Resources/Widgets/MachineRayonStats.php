<?php

namespace App\Filament\Widgets;

use App\Models\Rayon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class MachineRayonStats extends BaseWidget
{
    protected static ?string $heading = '🏢 Unit Per Rayon';
    protected static bool $isLazy = true;
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 1;

    public function getTableRecordKey($record): string
    {
        return (string) $record->id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Rayon::query()
                    ->join('customers', 'rayons.id', '=', 'customers.rayon_id')
                    ->join('deployments', 'customers.id', '=', 'deployments.customer_id')
                    ->select('rayons.id', 'rayons.nama_rayon', DB::raw('count(deployments.id) as total'))
                    ->groupBy('rayons.id', 'rayons.nama_rayon')
                    ->orderBy('total', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_rayon')
                    ->label('Nama Rayon')
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('total')
                    ->label('Jumlah Unit')
                    ->badge()
                    ->color('warning')
                    ->suffix(' Mesin'),
            ])
            ->paginated([5]);
    }
}
