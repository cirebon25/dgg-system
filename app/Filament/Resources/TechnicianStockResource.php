<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechnicianStockResource\Pages;
use App\Models\TechnicianStock;
use App\Models\Technician;
use Filament\Forms;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;

class TechnicianStockResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];

    protected static ?string $model = TechnicianStock::class;
    protected static ?string $navigationLabel = 'Kartu Stok Teknisi';
    protected static ?string $pluralModelLabel = 'Kartu Stok Teknisi';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Gudang & Stok';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Sparepart')
                    ->iconColor('primary')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Sisa Saldo (Di Tas)')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state > 5  => 'success',
                        $state > 0  => 'warning',
                        default     => 'danger',
                    })
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Item di Tas'),
                    ]),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->since()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('technician_id')
                    ->relationship('technician', 'nama_technician')
                    ->label('Filter Teknisi'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetak_semua')
                    ->label('Cetak Semua Kartu Stok')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->url(fn() => route('cetak.kartu-stok-semua'))
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(TechnicianStock $record) => route('cetak.kartu-stok', $record->technician_id))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([])
            ->defaultGroup(
                Group::make('technician.nama_technician')
                    ->label('Tas Milik Teknisi')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
            );
    }

    // ← OPTIMASI: Eager load technician & sparepart
    // Tanpa ini, setiap baris di tabel akan trigger 2 query terpisah
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['technician', 'sparepart']);
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
