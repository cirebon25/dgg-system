<?php

namespace App\Filament\Resources\TechnicianResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

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
            Tables\Columns\TextColumn::make('created_at')
                ->label('Waktu Transaksi')
                ->dateTime('d M Y H:i')
                ->sortable(),
                
            Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                ->label('Nama Part')
                ->searchable(),
                
            Tables\Columns\TextColumn::make('masuk')
                ->label('Masuk (+)')
                ->badge()
                ->color('success'),
                
            Tables\Columns\TextColumn::make('keluar')
                ->label('Keluar (-)')
                ->badge()
                ->color('danger'),
                
            Tables\Columns\TextColumn::make('saldo_akhir')
                ->label('Sisa Di Tas')
                ->weight('bold'),
                
            Tables\Columns\TextColumn::make('keterangan')
                ->label('Keterangan / Transaksi'),
        ])
        ->filters([
            //
        ])
        ->headerActions([])
        ->actions([])
        ->bulkActions([])
        ->defaultSort('created_at', 'desc'); // Urutkan dari yang terbaru
}
}
