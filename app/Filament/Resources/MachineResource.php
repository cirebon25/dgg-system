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

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2; // Urutan nomor 2

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- SECTION 1: INFORMASI UMUM ---
                Forms\Components\Section::make('Informasi Unit Mesin')
                    ->description('Masukkan detail mesin fotokopi sesuai label SN di bodi mesin.')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'nama_customer')
                            ->label('Lokasi / Pelanggan')
                            ->placeholder('Pilih Customer (Kosongkan jika masih di Gudang)')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),

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

                // --- SECTION 2: DETAIL TEKNIS (VOLT, DLL) ---
                Forms\Components\Section::make('Detail Teknis Mesin')
                    ->description('Informasi tambahan untuk spesifikasi teknis unit')
                    ->schema([
                        Forms\Components\TextInput::make('volt')
                            ->label('Voltase')
                            ->placeholder('Contoh: 110V / 220V'),

                        Forms\Components\TextInput::make('finisher')
                            ->label('Finisher')
                            ->placeholder('Contoh: Internal Finisher / Booklet'),

                        Forms\Components\TextInput::make('cover')
                            ->label('Cover')
                            ->placeholder('Contoh: Platen Cover / DADF'),

                        Forms\Components\TextInput::make('kaset')
                            ->label('Jumlah Kaset')
                            ->placeholder('Contoh: 2 Tray / 4 Tray'),

                        Forms\Components\TextInput::make('double_scan')
                            ->label('Double Scan (Qty)')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    // 1. Urutkan Status: 'Ready' jadi nomor 0 (paling atas), selain itu nomor 1
                    ->orderByRaw("CASE WHEN status = 'Ready' THEN 0 ELSE 1 END")
                    // 2. Urutkan berdasarkan tanggal buat terbaru
                    ->orderBy('created_at', 'desc');
            })
            ->headerActions([
                // 🌟 TOMBOL INPUT BARU
                Tables\Actions\CreateAction::make()
                    ->label('Input Mesin Baru')
                    ->icon('heroicon-o-plus'),

                // 🌟 TOMBOL REKAP RAYON
                // Tables\Actions\Action::make('cetak_rekap_rayon')
                //     ->label('Rekap Per Rayon')
                //     ->icon('heroicon-o-map')
                //     ->color('warning')
                //     ->form([
                //         Forms\Components\Select::make('month')
                //             ->label('Bulan')
                //             ->options([
                //                 '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                //                 '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                //                 '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                //                 '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                //             ])->required()->default(date('m')),
                //         Forms\Components\Select::make('year')
                //             ->label('Tahun')
                //             ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                //             ->required()->default(date('Y')),
                //     ])
                //     ->action(fn (array $data) => redirect()->route('cetak.rekap-rayon', $data)),
            ])
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
                    ->color(fn(string $state): string => match ($state) {
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
                    ->url(fn($record) => route('sparepart.monitor', $record->id))
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
