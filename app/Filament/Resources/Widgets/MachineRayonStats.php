<?php

namespace App\Filament\Resources\Widgets;

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
    protected int|string|array $columnSpan = 'full';

    public function getTableRecordKey($record): string
    {
        return (string) $record->id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Rayon::query()
                    ->withCount(['customers as total' => function ($query) {
                        $query->join('deployments', 'customers.id', '=', 'deployments.customer_id');
                    }])
                    // Atau jika menggunakan relasi langsung dari customers ke deployments:
                    ->select('rayons.id', 'rayons.nama_rayon')
                    ->addSelect([
                        'total' => \App\Models\Deployment::query()
                            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
                            ->whereColumn('customers.rayon_id', 'rayons.id')
                            ->select(DB::raw('count(deployments.id)'))
                    ])
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
