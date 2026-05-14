<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechnicianStockHistoryResource\Pages;
use App\Models\TechnicianStockHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TechnicianStockHistoryResource extends Resource
{
    protected static ?string $model = TechnicianStockHistory::class;

    // Nama Menu di Sidebar
    protected static ?string $navigationLabel = 'Histori Stok Teknisi';
    protected static ?string $pluralModelLabel = 'Histori Stok Teknisi';
    
    // Icon buku catatan
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    
    // Kita kumpulkan di grup Gudang
    protected static ?string $navigationGroup = 'Gudang & Stok';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal & Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Nama Teknisi')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Part')
                    ->searchable()
                    ->sortable(),
                    
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
                    ->label('Keterangan'),
            ])
            ->filters([
                // Filter cepat untuk mencari histori teknisi tertentu
                Tables\Filters\SelectFilter::make('technician_id')
                    ->relationship('technician', 'nama_technician')
                    ->label('Filter Teknisi'),
            ])
            ->actions([
                // MATIKAN EDIT & DELETE
            ])
            ->bulkActions([
                // MATIKAN HAPUS MASSAL
            ])
            ->defaultSort('created_at', 'desc'); // Urutan dari yang paling baru
    }

    // MATIKAN TOMBOL CREATE (Karena data ini otomatis masuk dari sistem)
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTechnicianStockHistories::route('/'),
        ];
    }
}