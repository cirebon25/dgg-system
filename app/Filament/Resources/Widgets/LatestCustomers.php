<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestCustomers extends BaseWidget
{
    protected static ?string $heading = '🤝 10 Customer Baru';

    protected static bool $isLazy = true;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::query()->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_customer')
                    ->label('Nama Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('kota')
                    ->label('Wilayah')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Install')
                    ->dateTime('d M Y')
                    ->color('primary'),
            ]);
    }
}
