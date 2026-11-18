<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceLogResource\Pages;
use App\Filament\Traits\HasRoleAccess;
use App\Models\Machine;
use App\Models\ServiceLog;
use App\Models\Sparepart;
use App\Models\TechnicianStock;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ServiceLogResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin', 'admin_teknik'];

    protected static ?string $model = ServiceLog::class;
    protected static ?string $navigationLabel = 'Input Servis Teknisi';
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Transaksi';

    private const TAHUN_AWAL = 2024;

    private const TIPE_KUNJUNGAN = [
        'RN' => 'RN (Instal Baru)',
        'CM' => 'CM (Call Maintenance)',
        'RM' => 'RM (Kunjungan Rutin)',
        'RR' => 'RR (Ganti Mesin)',
        'JK' => 'JK (Jaringan komputer)',
        'L' => 'L (Lanjut)',
        'TN' => 'TN (Call Toner)',
        'MRC' => 'MRC (Pencatatan Counter Bulanan)',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Kunjungan')
                ->schema([
                    Forms\Components\Select::make('machine_id')
                        ->relationship('machine', 'serial_number')
                        ->label('SN Mesin')
                        ->required()
                        ->searchable()
                        ->live()
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('scanQR')
                                ->icon('heroicon-o-qr-code')
                                ->color('primary')
                                ->tooltip('Scan QR Mesin')
                                ->modalHeading('Scan QR Code Mesin')
                                ->modalSubmitAction(false)
                                ->modalCancelActionLabel('Tutup')
                                ->modalContent(view('filament.forms.components.qr-scanner-modal'))
                        )
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, ?ServiceLog $record) {
                            $machine = Machine::with('customer')->find($state);

                            $set('customer_id', $machine?->customer_id);

                            if ($machine?->customer?->technician_id) {
                                $set('technician_id', $machine->customer->technician_id);
                            }

                            self::syncCounterBaseline($get, $set, $record);
                        }),

                    Forms\Components\Select::make('customer_id')
                        ->relationship('customer', 'nama_customer')
                        ->label('Nama Customer / Instansi')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('tipe_kunjungan')
                        ->options(self::TIPE_KUNJUNGAN)
                        ->required()
                        ->default('RM'),

                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal Kunjungan')
                        ->default(now())
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, ?ServiceLog $record) {
                            self::syncCounterBaseline($get, $set, $record);
                        }),
                ])->columns(2),

            Forms\Components\Section::make('Detail Teknisi & Perbaikan')
                ->schema([
                    Forms\Components\Textarea::make('kerusakan')->required(),
                    Forms\Components\Textarea::make('perbaikan')->required(),

                    Forms\Components\Select::make('technician_id')
                        ->relationship('technician', 'nama_technician')
                        ->label('Teknisi Utama')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Forms\Set $set, string $operation) {
                            // Ganti teknisi = daftar sparepart lama tidak valid lagi
                            if ($operation === 'create') {
                                $set('serviceLogSpareparts', []);
                            }
                        }),

                    Forms\Components\TextInput::make('nama_teknisi_2')
                        ->label('Teknisi Pembantu (Ketik Manual)')
                        ->placeholder('Contoh: Rudi / Ahmad'),
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
                        ->rules([self::counterRule('bw', 'BW')])
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                            $sekarang = (int) $state;
                            $lalu = (int) $get('bw_lalu');

                            if ($sekarang < $lalu) {
                                Notification::make()
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
                        ->minValue(0)
                        ->live(onBlur: true)
                        ->rules([self::counterRule('color', 'Color')])
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                            $sekarang = (int) $state;
                            $lalu = (int) $get('color_lalu');

                            if ($sekarang < $lalu) {
                                Notification::make()
                                    ->title('Counter Color tidak valid!')
                                    ->body("Counter sekarang ({$sekarang}) tidak boleh kurang dari counter lalu ({$lalu}).")
                                    ->danger()
                                    ->send();
                            }

                            $set('usage_color', max(0, $sekarang - $lalu));
                        }),

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
                                ->label('Pilih Sparepart')
                                ->options(function (Forms\Get $get): array {
                                    $technicianId = $get('../../technician_id');

                                    if (! $technicianId) {
                                        return [];
                                    }

                                    return Sparepart::query()
                                        ->whereIn('id', TechnicianStock::query()
                                            ->where('technician_id', $technicianId)
                                            ->where('jumlah', '>', 0)
                                            ->select('sparepart_id'))
                                        ->orderBy('nama_sparepart')
                                        ->pluck('nama_sparepart', 'id')
                                        ->all();
                                })
                                ->getOptionLabelUsing(fn($value) => Sparepart::find($value)?->nama_sparepart)
                                ->searchable()
                                ->required()
                                ->placeholder('Pilih teknisi dulu')
                                ->disabled(fn(string $operation) => $operation === 'edit'),

                            Forms\Components\TextInput::make('jumlah')
                                ->label('Jumlah Pakai')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->live()
                                ->disabled(fn(string $operation) => $operation === 'edit')
                                ->rules([self::stokTeknisiRule()]),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Tambah Sparepart')
                        ->addable(fn(string $operation) => $operation === 'create')
                        ->deletable(fn(string $operation) => $operation === 'create')
                        ->reorderable(false),
                ]),
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
                    ->form(self::periodeForm())
                    ->action(fn(array $data) => redirect()->route('cetak.service-rayon', $data)),

                Tables\Actions\Action::make('cetak_kinerja_rayon')
                    ->label('Cetak Kinerja Rayon')
                    ->icon('heroicon-o-building-office')
                    ->color('violet')
                    ->form(self::periodeForm())
                    ->action(fn(array $data) => redirect()->route('print.performance-rayon', $data)),

                Tables\Actions\Action::make('cetakHorizontal')
                    ->label('Laporan Teknisi')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->form(self::periodeForm(singkat: true))
                    ->action(fn(array $data) => redirect()->route('rekap.horizontal', $data)),

                Tables\Actions\Action::make('cetak_kinerja')
                    ->label('Cetak Kinerja Teknisi')
                    ->icon('heroicon-o-chart-pie')
                    ->color('danger')
                    ->form(self::periodeForm())
                    ->action(fn(array $data) => redirect()->route('print.tech-performance', $data)),

                Tables\Actions\CreateAction::make(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('customer_display')
                    ->label('Customer / Model')
                    ->getStateUsing(fn(ServiceLog $record): string => self::namaCustomer($record))
                    ->description(fn(ServiceLog $record): string => 'Model: ' . ($record->machine?->tipe_model ?? '-'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $q) use ($search) {
                            $q->whereHas('customer', fn(Builder $c) => $c->where('nama_customer', 'like', "%{$search}%"))
                                ->orWhereHas('machine.customer', fn(Builder $c) => $c->where('nama_customer', 'like', "%{$search}%"))
                                ->orWhereHas('machine.deployment.customer', fn(Builder $c) => $c->where('nama_customer', 'like', "%{$search}%"));
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
                    ->label('Counter BW')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('counter_color')
                    ->label('Counter CL')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipe_kunjungan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'CM', 'RR' => 'danger',
                        'RN' => 'success',
                        'RM' => 'info',
                        'TN' => 'warning',
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
                    ->url(fn(ServiceLog $record) => route('service-log.surat-jalan', $record))
                    ->openUrlInNewTab()
                    ->visible(fn(ServiceLog $record) => $record->tipe_kunjungan === 'RR'),

                Tables\Actions\Action::make('print')
                    ->label('Nota')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(ServiceLog $record) => route('service-log.print', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    /**
     * Eager load seluruh relasi yang dipakai tabel.
     * Kolom customer_display mengakses tiga jalur: customer langsung,
     * machine->customer, dan machine->deployment->customer.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'customer',
                'technician',
                'machine.customer',
                'machine.deployment.customer',
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceLogs::route('/'),
            'create' => Pages\CreateServiceLog::route('/create'),
            'edit' => Pages\EditServiceLog::route('/{record}/edit'),
        ];
    }

    /**
     * Mengisi bw_lalu dan color_lalu, serta menghitung ulang usage
     * berdasarkan counter kunjungan terakhir sebelum tanggal kunjungan ini.
     */
    private static function syncCounterBaseline(Forms\Get $get, Forms\Set $set, ?ServiceLog $record): void
    {
        $baseline = self::lastCounter($get('machine_id'), $get('tanggal'), $record?->getKey());

        $set('bw_lalu', $baseline['bw']);
        $set('color_lalu', $baseline['color']);
        $set('usage_bw', max(0, (int) $get('counter_bw') - $baseline['bw']));
        $set('usage_color', max(0, (int) $get('counter_color') - $baseline['color']));
    }

    /**
     * Mengambil counter terakhir sebelum (atau pada) tanggal kunjungan,
     * dengan mengecualikan record yang sedang diedit.
     *
     * @return array{bw: int, color: int}
     */
    private static function lastCounter(mixed $machineId, mixed $tanggal, ?int $excludeId = null): array
    {
        if (! $machineId) {
            return ['bw' => 0, 'color' => 0];
        }

        $tanggalRef = $tanggal ? Carbon::parse($tanggal) : now();

        $log = ServiceLog::query()
            ->select(['id', 'counter_bw', 'counter_color'])
            ->where('machine_id', $machineId)
            ->whereDate('tanggal', '<=', $tanggalRef->toDateString())
            ->when($excludeId, fn(Builder $q) => $q->whereKeyNot($excludeId))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();

        return [
            'bw' => (int) ($log?->counter_bw ?? 0),
            'color' => (int) ($log?->counter_color ?? 0),
        ];
    }

    /**
     * Rule validasi: counter sekarang tidak boleh kurang dari counter baseline.
     */
    private static function counterRule(string $type, string $label): Closure
    {
        return fn(Forms\Get $get, ?ServiceLog $record): Closure => function (string $attribute, $value, Closure $fail) use ($get, $record, $type, $label) {
            $baseline = self::lastCounter($get('machine_id'), $get('tanggal'), $record?->getKey());
            $minimum = $baseline[$type];

            if ((int) $value < $minimum) {
                $fail("Counter {$label} ({$value}) tidak boleh kurang dari counter lalu ({$minimum}).");
            }
        };
    }

    /**
     * Rule validasi: jumlah pemakaian sparepart tidak boleh melebihi stok di tas teknisi.
     * Validasi ini adalah lapisan UX. Pengurangan stok yang sebenarnya harus dijaga di server.
     */
    private static function stokTeknisiRule(): Closure
    {
        return fn(Forms\Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
            $technicianId = $get('../../technician_id');
            $sparepartId = $get('sparepart_id');

            if (! $technicianId || ! $sparepartId) {
                return;
            }

            $currentStock = (int) TechnicianStock::query()
                ->where('technician_id', $technicianId)
                ->where('sparepart_id', $sparepartId)
                ->value('jumlah');

            if ((int) $value > $currentStock) {
                $fail("STOK TIDAK CUKUP! Saldo di tas teknisi hanya {$currentStock} pcs.");
            }
        };
    }

    /**
     * Form periode (bulan & tahun) yang dipakai bersama oleh seluruh action laporan.
     *
     * @return array<int, Forms\Components\Component>
     */
    private static function periodeForm(bool $singkat = false): array
    {
        return [
            Forms\Components\Select::make('month')
                ->label('Bulan')
                ->options(self::namaBulan($singkat))
                ->default(date('m'))
                ->required(),

            Forms\Components\Select::make('year')
                ->label('Tahun')
                ->options(self::opsiTahun())
                ->default((string) date('Y'))
                ->required(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function namaBulan(bool $singkat = false): array
    {
        $bulan = [
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
        ];

        return $singkat
            ? array_map(fn(string $nama) => substr($nama, 0, 3), $bulan)
            : $bulan;
    }

    /**
     * @return array<string, string>
     */
    private static function opsiTahun(): array
    {
        $tahun = array_map('strval', range(self::TAHUN_AWAL, (int) date('Y') + 1));

        return array_combine($tahun, $tahun);
    }

    private static function namaCustomer(ServiceLog $record): string
    {
        return $record->customer?->nama_customer
            ?? $record->machine?->customer?->nama_customer
            ?? $record->machine?->deployment?->customer?->nama_customer
            ?? 'Gudang DGG / Tanpa Customer';
    }
}