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

    // Hak akses diberikan kepada admin dan teknisi
    protected static array $allowedRoles = ['admin', 'teknisi'];

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
                    // Kolom disembunyikan jika yang login adalah teknisi
                    ->hidden(fn () => auth()->user()?->hasRole('teknisi')),

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
                // Filter disembunyikan untuk teknisi karena datanya otomatis dikunci
                Tables\Filters\SelectFilter::make('technician_id')
                    ->relationship('technician', 'nama_technician')
                    ->label('Filter Teknisi')
                    ->hidden(fn () => auth()->user()?->hasRole('teknisi')),
            ])
            ->headerActions([
                // TOMBOL 1: Cetak Semua (Hanya muncul untuk Admin)
                Tables\Actions\Action::make('cetak_semua')
                    ->label('Cetak Semua Kartu Stok')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->url(fn() => route('cetak.kartu-stok-semua'))
                    ->openUrlInNewTab()
                    ->hidden(fn () => auth()->user()?->hasRole('teknisi')),

                // TOMBOL 2: Cetak Milik Saya (Hanya muncul untuk Teknisi - Aman dari error parameter)
                Tables\Actions\Action::make('cetak_milik_saya')
                    ->label('Cetak Kartu Stok Saya')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->url(fn() => auth()->user()?->technician_id 
                        ? route('cetak.kartu-stok', auth()->user()->technician_id) 
                        : '#'
                    )
                    ->openUrlInNewTab()
                    ->visible(fn () => auth()->user()?->hasRole('teknisi')),
            ])
            ->actions([
                Tables\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(TechnicianStock $record) => route('cetak.kartu-stok', $record->technician_id))
                    ->openUrlInNewTab()
                    ->hidden(fn () => auth()->user()?->hasRole('teknisi')),
            ])
            ->bulkActions([])
            // Matikan fitur grouping jika yang login adalah teknisi
            ->defaultGroup(
                auth()->user()?->hasRole('teknisi') ? null : 
                Group::make('technician.nama_technician')
                    ->label('Tas Milik Teknisi')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
            );
    }

    // SOLUSI: Mengunci query dengan pencarian berbasis Email/Nama User jika field ID kosong
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery()->with(['technician', 'sparepart']);

        if ($user && $user->hasRole('teknisi')) {
            // Skenario A: Jika user punya technician_id langsung gunakan id-nya
            if (!empty($user->technician_id)) {
                return $query->where('technician_id', $user->technician_id);
            }

            // Skenario B (Cadangan Amat Sangat Aman): Jika technician_id di user kosong,
            // kita cari ke tabel teknisi yang namanya/emailnya mirip dengan user yang sedang login
            return $query->whereHas('technician', function ($q) use ($user) {
                $q->where('nama_technician', 'LIKE', '%' . $user->name . '%');
            });
        }

        return $query;
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