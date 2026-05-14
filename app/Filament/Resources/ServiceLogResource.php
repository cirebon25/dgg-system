<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceLogResource\Pages;
use App\Models\ServiceLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceLogResource extends Resource
{
    protected static ?string $model = ServiceLog::class;

    protected static ?string $navigationLabel = 'Input Servis Teknisi';

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Kunjungan')
                    ->schema([
                        Forms\Components\Select::make('machine_id')
                            ->relationship('machine', 'serial_number')
                            ->label('SN Mesin')
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $lastLog = ServiceLog::where('machine_id', $state)->latest('tanggal')->first();
                                if ($lastLog) {
                                    $set('bw_lalu', $lastLog->counter_bw);
                                    $set('color_lalu', $lastLog->counter_color);
                                }
                            }),

                        Forms\Components\Select::make('tipe_kunjungan')
                            ->options([
                                'RN' => 'RN (Intal Baru)', 'CM' => 'CM (Call Maintenance)',
                                'RM' => 'RM (Kunjungan Rutin)', 'RR' => 'RR (Ganti Mesin)',
                                'JK' => 'JK (Jaringan komputer)', 'L' => 'L (Lanjut)',
                                'TN' => 'TN (Call Toner )',
                            ])->required(),

                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal Kunjungan')
                            ->default(now())
                            ->required(),

                        Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TimePicker::make('jam_mulai'),
                        Forms\Components\TimePicker::make('jam_selesai'),
                              ]),
                             ])->columns(2),

                        Forms\Components\Section::make('Pencatatan Counter')
                            ->schema([
                        Forms\Components\TextInput::make('bw_lalu')->label('BW Lalu')->numeric()->readOnly(),
                        Forms\Components\TextInput::make('counter_bw')->label('BW Sekarang')->numeric()->required()->reactive()
                            ->afterStateUpdated(fn ($state, $get, $set) => $set('usage_bw', (int) $state - (int) $get('bw_lalu'))),
                        Forms\Components\TextInput::make('usage_bw')->label('Usage BW')->numeric()->readOnly(),

                        Forms\Components\TextInput::make('color_lalu')->label('Color Lalu')->numeric()->readOnly(),
                        Forms\Components\TextInput::make('counter_color')->label('Color Sekarang')->numeric()->reactive()
                            ->afterStateUpdated(fn ($state, $get, $set) => $set('usage_color', (int) $state - (int) $get('color_lalu'))),
                        Forms\Components\TextInput::make('usage_color')->label('Usage Color')->numeric()->readOnly(),
                              ])->columns(3),

                         Forms\Components\Section::make('Sparepart yang Diganti')
                          ->schema([
                        Forms\Components\Repeater::make('serviceLogSpareparts')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('sparepart_id')
                                    ->relationship('sparepart', 'nama_sparepart')
                                    ->label('Pilih Sparepart')
                                    ->searchable()
                                    ->preload(),
                                // Di dalam Schema Repeater, cari TextInput::make('jumlah')

                        Forms\Components\TextInput::make('jumlah')
    ->label('Jumlah Pakai')
    ->numeric()
    ->required()
    ->minValue(1) // <--- GEMBOK 1: Browser langsung nolak kalau angka 0 atau minus!
    ->reactive()
    ->rules([
        fn (Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
            // Jalur aman deteksi ID Teknisi
            $techId = $get('../../technician_id') ?? $get('../../../technician_id'); 
            $partId = $get('sparepart_id');

            if (!$partId) return;

            if (!$techId) {
                $fail("Pilih 'Teknisi Utama' terlebih dahulu di bagian bawah form!");
                return;
            }

            // GEMBOK 2: Validasi back-end kalau admin maksa ngetik angka 0 / kosong
            if ((int)$value <= 0) {
                $fail("Jumlah pakai tidak boleh 0 atau minus Boss! Minimal harus 1.");
                return;
            }

            // Ambil data stok di tas teknisi
            $stock = \App\Models\TechnicianStock::where('technician_id', $techId)
                ->where('sparepart_id', $partId)
                ->first();

            $currentStock = $stock ? $stock->jumlah : 0;

            // GEMBOK 3: Jika teknisi sama sekali gak punya barang itu di tas (stoknya 0)
            if ($currentStock <= 0) {
                $fail("Gagal! Teknisi tidak memiliki stok part ini di dalam tas (Stok: 0). Wajib Pinjam Part dulu!");
                return;
            }

            // GEMBOK 4: Jika stok ada tapi kurang (misal stok 3 tapi dipaksa input 5)
            if ((int)$value > $currentStock) {
                $fail("Stok di tas tidak cukup! Teknisi cuma bawa {$currentStock} pcs.");
                return;
            }
        },
    ]),
                            ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('Tambah Sparepart'),
                    ]),

                        Forms\Components\Section::make('Detail Teknisi & Perbaikan')
                          ->schema([
                        Forms\Components\Textarea::make('kerusakan')->required(),
                        Forms\Components\Textarea::make('perbaikan')->required(),

                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'nama_technician')
                            ->label('Teknisi Utama')->required(),

                       Forms\Components\TextInput::make('nama_teknisi_2')
                            ->label('Teknisi Pembantu (Ketik Manual)')
                            ->placeholder('Contoh: Rudi / Ahmad'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                // 1. Tombol Rekap Rayon
                Tables\Actions\Action::make('cetak_rekap_rayon')
                    ->label('Cetak Laporan  Perayon')
                    ->icon('heroicon-o-map')
                    ->color('info')
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
                    ->action(fn (array $data) => redirect()->route('cetak.service-rayon', $data)),

                // 2. Tombol Cetak Per Bulan
                Tables\Actions\Action::make('printBulanan')
                    ->label('Cetak Per Bulan')
                    ->color('success')
                    ->icon('heroicon-o-calendar')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->options([
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ])->required()->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()->default(date('Y')),
                    ])
                    ->action(fn (array $data) => redirect()->route('service-log.monthly', $data)),

                Tables\Actions\CreateAction::make(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('machine.deployment.customer.nama_customer')
                    ->label('Customer / Model')
                    ->description(fn ($record): string => 'Model: '.($record->machine?->tipe_model ?? '-'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN / Tgl Pasang')
                    ->description(fn ($record) => 'Instal: '.($record->machine?->deployment?->tanggal_instal?->format('d/m/Y') ?? '-'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tgl Servis')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('counter_bw')
                    ->label('Counter (BW/CL)')
                    ->html()
                    ->formatStateUsing(fn ($record) => 'BW: '.number_format($record->counter_bw).'<br>CL: '.number_format($record->counter_color)
                    ),

                Tables\Columns\TextColumn::make('usage_bw')
                    ->label('Usage (BW/CL)')
                    ->html()
                    ->formatStateUsing(fn ($record) => "<span style='color:#3b82f6; font-weight:bold;'>BW: ".number_format($record->usage_bw).'</span><br>'.
                        "<span style='color:#ef4444; font-weight:bold;'>CL: ".number_format($record->usage_color).'</span>'
                    ),

                Tables\Columns\TextColumn::make('tipe_kunjungan')
                    ->badge()
                   ->color(fn (string $state): string => match ($state) {
                        'CM' => 'danger',   // Merah Tua
                        'RN' => 'success',  // Hijau
                        'RM' => 'info',     // Biru
                        'TN' => 'warning',  // Kuning
                        'JK' => 'primary',  // Ungu (Warna Utama Filament)
                        'L'  => 'warning',  // Oren (Di Filament warning itu antara Kuning/Oren)
                        'RR' => 'rose',     // Merah Muda (Tersedia di Filament v3)
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (ServiceLog $record) => route('service-log.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceLogs::route('/'),
            'create' => Pages\CreateServiceLog::route('/create'),
            'edit' => Pages\EditServiceLog::route('/{record}/edit'),
        ];
    }
}
