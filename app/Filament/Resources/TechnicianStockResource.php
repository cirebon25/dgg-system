<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechnicianStockResource\Pages;
use App\Models\TechnicianStock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group; // MANTRA GROUPING PENTING

class TechnicianStockResource extends Resource
{
    protected static ?string $model = TechnicianStock::class;

    protected static ?string $navigationLabel = 'Kartu Stok Teknisi';
    protected static ?string $pluralModelLabel = 'Kartu Stok Teknisi';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Gudang & Stok';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom Nama Teknisi kita sembunyikan dari baris, karena sudah ada di Judul Grup
                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Sparepart')
                    ->iconColor('primary')
                    ->weight('bold')
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
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->since() // Mengubah tanggal jadi "2 jam yang lalu", "5 menit yang lalu"
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('technician_id')
                    ->relationship('technician', 'nama_technician')
                    ->label('Filter Teknisi'),
            ])
            ->actions([])
            ->bulkActions([])
            
            // INI DIA JURUS PAMUNGKASNYA (GROUP PER TEKNISI)
            ->defaultGroup(
                Group::make('technician.nama_technician')
                    ->label('Tas Milik Teknisi')
                    ->collapsible() // Biar bisa dibuka-tutup (keren!)
                    ->titlePrefixedWithLabel(false) // Biar tulisannya bersih, cuma nama teknisinya aja
            );
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTechnicianStocks::route('/'),
        ];
    }
}