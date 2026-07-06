<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartReplacementResource\Pages;
use App\Models\PartReplacement;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;

class PartReplacementResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin', 'manager'];

    protected static ?string $model          = PartReplacement::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Riwayat Ganti Part Setiap Mesin';
    protected static ?string $navigationGroup = 'Gudang & Stok';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                // CETAK PER MESIN
                Tables\Actions\Action::make('cetak_per_mesin')
                    ->label('Cetak Per Mesin')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('machine_id')
                            ->label('Pilih No Seri Mesin')
                            ->options(\App\Models\Machine::pluck('serial_number', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(fn(array $data) => redirect()->route('cetak.part.mesin', $data))
                    ->openUrlInNewTab(),

                // CETAK PER BULAN
                Tables\Actions\Action::make('cetak_per_bulan')
                    ->label('Cetak Per Bulan')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ])
                            ->required()
                            ->default(date('m')),
                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()
                            ->default(date('Y')),
                    ])
                    ->action(fn(array $data) => redirect()->route('cetak.part.bulan', $data))
                    ->openUrlInNewTab(),
            ])

            ->columns([
                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('machine.tipe_model')
                    ->label('Tipe Mesin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('machine.customer.nama_customer')
                    ->label('Lokasi')
                    ->placeholder('Gudang DGG'),

                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Sparepart')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal Ganti')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('counter_sebelumnya')
                    ->label('Counter Sebelumnya')
                    ->numeric()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('counter_saat_ganti')
                    ->label('Counter Saat Ganti')
                    ->numeric()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('selisih')
                    ->label('Selisih (Pemakaian)')
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(fn($state) => number_format($state) . ' Lbr')
                    ->color(fn(int $state): string => match (true) {
                        $state >= 80000 => 'danger',
                        $state >= 50000 => 'warning',
                        default         => 'success',
                    }),
            ])
            ->defaultGroup(
                Group::make('machine.serial_number')
                    ->label('Mesin')
                    ->collapsible()
            )
            ->defaultSort('tanggal', 'desc')
            ->striped()
            ->filters([
                Tables\Filters\SelectFilter::make('sparepart_id')
                    ->relationship('sparepart', 'nama_sparepart')
                    ->label('Filter Sparepart'),

                Tables\Filters\SelectFilter::make('machine_id')
                    ->relationship('machine', 'serial_number')
                    ->label('Filter Mesin'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->label('')->tooltip('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartReplacements::route('/'),
        ];
    }
}