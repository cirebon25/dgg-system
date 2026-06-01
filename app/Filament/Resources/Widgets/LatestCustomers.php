<?php

namespace App\Filament\Resources\Widgets;

use App\Models\Customer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestCustomers extends BaseWidget
{
    protected static ?string $heading = '🤝 5 Customer Baru Terdaftar';
    protected static bool $isLazy = true;
    protected static ?int $sort = 6;
      protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Customer::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('nama_customer')
                    ->label('Nama Customer')
                    ->weight('semibold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('kota')
                    ->label('Wilayah')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Gabung')
                    ->date('d M Y')
                    ->color('primary'),
            ])
            ->paginated(false);
    }
}
