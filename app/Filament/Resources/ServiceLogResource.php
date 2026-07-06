<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceLogResource\Pages;
use App\Models\ServiceLog;
use App\Models\Machine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;
use Filament\Forms\Get;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceLogResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin', 'admin_teknik'];

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
                        // Forms\Components\Select::make('machine_id')
                        //     ->relationship('machine', 'serial_number')
                        //     ->label('SN Mesin')
                        //     ->required()
                        //     ->searchable()
                        //     ->reactive()
                        //     ->afterStateUpdated(function ($state, Forms\Set $set) {
                        //         $lastLog = ServiceLog::where('machine_id', $state)
                        //             ->latest('id')
                        //             ->first();

                        //         if ($lastLog) {
                        //             $set('bw_lalu', $lastLog->counter_bw);
                        //             $set('color_lalu', $lastLog->counter_color);
                        //         } else {
                        //             $set('bw_lalu', 0);
                        //             $set('color_lalu', 0);
                        //         }

                        //         $machine = Machine::find($state);
                        //         if ($machine) {
                        //             $set('customer_id', $machine->customer_id);

                        //             if ($machine->customer?->technician_id) {
                        //                 $set('technician_id', $machine->customer->technician_id);
                        //             }
                        //         }
                        //     }),

                        Forms\Components\Select::make('machine_id')
                            ->relationship('machine', 'serial_number')
                            ->label('SN Mesin')
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $bulanLalu = now()->subMonth();

                                // Ambil log terakhir di bulan lalu
                                $lastLog = ServiceLog::where('machine_id', $state)
                                    ->whereYear('tanggal',  $bulanLalu->year)
                                    ->whereMonth('tanggal', $bulanLalu->month)
                                    ->orderBy('tanggal', 'desc')
                                    ->orderBy('id', 'desc')
                                    ->first();

                                // Kalau bulan lalu tidak ada, ambil log terakhir sebelum bulan ini
                                if (!$lastLog) {
                                    $lastLog = ServiceLog::where('machine_id', $state)
                                        ->where('tanggal', '<', now()->startOfMonth())
                                        ->orderBy('tanggal', 'desc')
                                        ->orderBy('id', 'desc')
                                        ->first();
                                }

                                if ($lastLog) {
                                    $set('bw_lalu',    $lastLog->counter_bw);
                                    $set('color_lalu', $lastLog->counter_color);
                                } else {
                                    $set('bw_lalu',    0);
                                    $set('color_lalu', 0);
                                }

                                $machine = Machine::find($state);
                                if ($machine) {
                                    $set('customer_id', $machine->customer_id);

                                    if ($machine->customer?->technician_id) {
                                        $set('technician_id', $machine->customer->technician_id);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'nama_customer')
                            ->label('Nama Customer / Instansi')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('tipe_kunjungan')
                            ->options([
                                'RN' => 'RN (Instal Baru)',
                                'CM' => 'CM (Call Maintenance)',
                                'RM' => 'RM (Kunjungan Rutin)',
                                'RR' => 'RR (Ganti Mesin)',
                                'JK' => 'JK (Jaringan komputer)',
                                'L' => 'L (Lanjut)',
                                'TN' => 'TN (Call Toner)',
                                'MRC' => 'MRC (Pencatatan Counter Bulanan)'
                            ])
                            ->required()
                            ->default('RM'),

                        Forms\Components\Toggle::make('is_mrc')
                            ->label('Catat sebagai MRC (Pencatatan Counter Bulanan)')
                            ->default(false)
                            ->helperText('Centang jika kunjungan ini adalah pencatatan counter bulanan untuk tagihan.')
                            ->columnSpanFull(),

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
                        Forms\Components\TextInput::make('bw_lalu')
                            ->label('BW Lalu')
                            ->numeric()
                            ->readOnly(),

                        Forms\Components\TextInput::make('counter_bw')
                            ->label('BW Sekarang')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $get, $set) {
                                $sekarang = (int) $state;
                                $lalu     = (int) $get('bw_lalu');

                                if ($sekarang < $lalu) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Counter BW tidak valid!')
                                        ->body("Counter sekarang ({$sekarang}) tidak boleh kurang dari counter lalu ({$lalu}).")
                                        ->danger()
                                        ->send();
                                }

                                $set('usage_bw', max(0, $sekarang - $lalu));
                            }),
                        Forms\Components\TextInput::make('usage_bw')
                            ->label('Usage BW')
                            ->numeric()
                            ->readOnly(),

                        Forms\Components\TextInput::make('color_lalu')
                            ->label('Color Lalu')
                            ->numeric()
                            ->readOnly(),

                        Forms\Components\TextInput::make('counter_color')
                            ->label('Color Sekarang')
                            ->numeric()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $get, $set) => $set('usage_color', (int) $state - (int) $get('color_lalu'))),

                        Forms\Components\TextInput::make('usage_color')
                            ->label('Usage Color')
                            ->numeric()
                            ->readOnly(),
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
                                    ->preload()
                                    ->required()
                                    ->disabled(fn(string $operation) => $operation === 'edit'),

                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah Pakai')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->disabled(fn(string $operation) => $operation === 'edit')
                                    ->rules([
                                        fn(Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $technicianId = $get('../../technician_id');
                                            $sparepartId = $get('sparepart_id');

                                            if (!$technicianId || !$sparepartId) return;

                                            $stock = \App\Models\TechnicianStock::where('technician_id', $technicianId)
                                                ->where('sparepart_id', $sparepartId)
                                                ->first();

                                            $currentStock = $stock ? $stock->jumlah : 0;

                                            if ((int)$value > (int)$currentStock) {
                                                $fail("❌ STOK TIDAK CUKUP! Saldo di tas teknisi hanya ada {$currentStock} pcs.");
                                            }

                                            if ((int)$value <= 0) {
                                                $fail("❌ Minimal pemakaian adalah 1 pcs.");
                                            }
                                        },
                                    ]),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Sparepart')
                            ->addable(fn(string $operation) => $operation === 'create')
                            ->deletable(fn(string $operation) => $operation === 'create')
                            ->reorderable(false),
                    ]),

                Forms\Components\Section::make('Detail Teknisi & Perbaikan')
                    ->schema([
                        Forms\Components\Textarea::make('kerusakan')->required(),
                        Forms\Components\Textarea::make('perbaikan')->required(),

                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'nama_technician')
                            ->label('Teknisi Utama')
                            ->required(),

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
                Tables\Actions\Action::make('cetak_rekap_rayon')
                    ->label('Rekap Rayon')
                    ->icon('heroicon-o-map')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->options(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'])
                            ->required()->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()->default(date('Y')),
                    ])
                    ->action(fn(array $data) => redirect()->route('cetak.service-rayon', $data)),

                Tables\Actions\Action::make('cetak_kinerja_rayon')
                    ->label('Cetak Kinerja Rayon')
                    ->icon('heroicon-o-building-office')
                    ->color('violet')
                    ->action(fn(array $data) => redirect()->route('print.performance-rayon', $data)),

                Tables\Actions\Action::make('cetakHorizontal')
                    ->label('Laporan Teknisi')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->options(['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu', '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'])
                            ->required()->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()->default(date('Y')),
                    ])
                    ->action(fn(array $data) => redirect()->route('rekap.horizontal', $data)),

                Tables\Actions\Action::make('cetak_kinerja')
                    ->label('Cetak Kinerja Teknisi')
                    ->icon('heroicon-o-chart-pie')
                    ->color('danger')
                    ->form([
                        Forms\Components\Select::make('month')
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
                                '12' => 'Desember'
                            ])->default(date('m'))->required(),
                        Forms\Components\Select::make('year')
                            ->options(array_combine(range(2024, 2030), range(2024, 2030)))
                            ->default(date('Y'))->required(),
                    ])
                    ->action(fn(array $data) => redirect()->route('print.tech-performance', $data)),

                Tables\Actions\CreateAction::make(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('machine_id')
                    ->label('Customer / Model')
                    ->getStateUsing(function ($record) {
                        if ($record->customer?->nama_customer) {
                            return $record->customer->nama_customer;
                        }
                        if ($record->machine?->customer?->nama_customer) {
                            return $record->machine->customer->nama_customer;
                        }
                        if ($record->machine?->deployment?->customer?->nama_customer) {
                            return $record->machine->deployment->customer->nama_customer;
                        }
                        return 'Gudang DGG / Tanpa Customer';
                    })
                    ->description(fn($record): string => 'Model: ' . ($record->machine?->tipe_model ?? '-'))
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('customer', function ($q) use ($search) {
                            $q->where('nama_customer', 'like', "%{$search}%");
                        })->orWhereHas('machine.customer', function ($q) use ($search) {
                            $q->where('nama_customer', 'like', "%{$search}%");
                        });
                    }),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN Mesin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tgl Servis')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('counter_bw')
                    ->label('Counter (BW/CL)')
                    ->html()
                    ->formatStateUsing(fn($record) => 'BW: ' . number_format($record->counter_bw) . '<br>CL: ' . number_format($record->counter_color)),

                Tables\Columns\TextColumn::make('tipe_kunjungan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'CM' => 'danger',
                        'RN' => 'success',
                        'RM' => 'info',
                        'TN' => 'warning',
                        'RR' => 'danger',
                        'MRC' => 'indigo',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('printSuratJalan')
                    ->label('SJ')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn($record) => route('service-log.surat-jalan', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->tipe_kunjungan === 'RR'),

                Tables\Actions\Action::make('print')
                    ->label('Nota')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('service-log.print', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    // ← OPTIMASI: Eager load semua relasi yang dipakai di tabel
    // Kolom machine_id->getStateUsing mengakses 3 jalur berbeda:
    // customer langsung, machine->customer, machine->deployment->customer
    // Semua di-load sekaligus agar tidak ada N+1 query
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer', 'machine.customer', 'machine.deployment.customer', 'technician']);
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