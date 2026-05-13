<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MachineResource\Pages;
use App\Models\Machine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MachineResource extends Resource
{
    protected static ?string $model = Machine::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'Data Mesin';

   public static function form(Form $form): Form
    {
    return $form
        ->schema([
            Forms\Components\Section::make('Informasi Unit Mesin')
                ->description('Masukkan detail mesin fotokopi sesuai label SN di bodi mesin.')
                ->schema([
                    // 1. TAMBAHKAN PEMILIH CUSTOMER DI SINI BOSS!
                    Forms\Components\Select::make('customer_id')
                        ->relationship('customer', 'nama_customer')
                        ->label('Lokasi / Pelanggan')
                        ->placeholder('Pilih Customer (Kosongkan jika masih di Gudang)')
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(), // Kita buat lebar biar jelas

                    Forms\Components\TextInput::make('serial_number')
                        ->label('Serial Number (SN)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('Contoh: WEP12345'),

                    Forms\Components\Select::make('tipe_model')
                        ->label('Tipe / Model Mesin')
                        ->options(\App\Models\TypeModel::pluck('nama_tipe', 'nama_tipe'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('nama_tipe')
                                ->label('Tipe Model Baru')
                                ->required()
                                ->unique('type_models', 'nama_tipe'),
                        ])
                        ->createOptionUsing(function (array $data): string {
                            $tipe = \App\Models\TypeModel::create($data);
                            return $tipe->nama_tipe;
                        }),

                    Forms\Components\Select::make('status')
                        ->label('Status Mesin')
                        ->options([
                            'Ready' => 'Ready (Siap Pakai)',
                            'Rented' => 'Rented (Sedang Disewa)',
                            'Refurbish' => 'Refurbish (Dalam Perbaikan)',
                        ])
                        ->default('Ready')
                        ->required(),

                    Forms\Components\Textarea::make('keterangan_awal')
                        ->label('Keterangan Mesin')
                        ->placeholder('Misal: EX LUAR / EX RENTAL')
                        ->columnSpanFull(),
                ])->columns(2),

            // ... (Section Detail Teknis Mesin tetap sama) ...
            Forms\Components\Section::make('Detail Teknis Mesin')
                ->description('Informasi tambahan untuk stok gudang')
                ->schema([
                    // ... isi tetap sama ...
                    Forms\Components\TextInput::make('volt')->label('Voltase'),
                    Forms\Components\TextInput::make('finisher')->label('Finisher'),
                    Forms\Components\TextInput::make('cover')->label('Cover'),
                    Forms\Components\TextInput::make('kaset')->label('Jumlah Kaset'),
                    Forms\Components\TextInput::make('double_scan')->label('Double Scan (Qty)')->numeric()->default(0),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('cetak_rekap_rayon')
                    ->label('Rekap Per Rayon')
                    ->icon('heroicon-o-map')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ])->required()->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()->default(date('Y')),
                    ])
                    ->action(fn (array $data) => redirect()->route('cetak.rekap-rayon', $data)),
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->orderByRaw("CASE WHEN status = 'Ready' THEN 0 ELSE 1 END")
                    ->orderBy('created_at', 'desc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipe_model')
                    ->label('Tipe Mesin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ready' => 'success',
                        'Rented' => 'warning',
                        'Refurbish' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Lokasi / Pelanggan')
                    ->placeholder('Gudang DGG'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('monitor')
                    ->label('Monitor Part')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('warning')
                    ->url(fn ($record) => route('sparepart.monitor', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('cetak_qr')
                    ->label('Cetak QR')
                    ->icon('heroicon-m-qr-code')
                    ->color('warning')
                    ->url(fn (Machine $record): string => route('mesin.cetak-qr', ['id' => $record->id]))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListMachines::route('/'),
            'create' => Pages\CreateMachine::route('/create'),
            'edit' => Pages\EditMachine::route('/{record}/edit'),
        ];
    }
}
