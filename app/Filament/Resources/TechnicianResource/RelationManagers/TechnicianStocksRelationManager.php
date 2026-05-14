<?php

namespace App\Filament\Resources\TechnicianResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TechnicianStocksRelationManager extends RelationManager
{
    protected static string $relationship = 'technicianStocks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
    return $table
        ->recordTitleAttribute('id')
        ->columns([
            Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                ->label('Nama Sparepart')
                ->searchable()
                ->sortable(),
                
            Tables\Columns\TextColumn::make('jumlah')
                ->label('Sisa Saldo (Di Tas)')
                ->badge()
                ->color(fn (int $state): string => match (true) {
                    $state > 5 => 'success',
                    $state > 0 => 'warning',
                    $state <= 0 => 'danger',
                })
                ->sortable(),
        ])
        ->filters([
            //
        ])
        ->headerActions([
            // Matikan tombol Create di sini
        ])
        ->actions([
            // Matikan tombol Edit/Delete
        ])
        ->bulkActions([
            // Matikan tombol Hapus Massal
        ]);
    }
}
