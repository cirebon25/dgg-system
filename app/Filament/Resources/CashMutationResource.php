<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashMutationResource\Pages;
use App\Models\CashMutation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;
use Filament\Tables;
use Filament\Tables\Table;

class CashMutationResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin', 'keuangan', 'manager', 'teknisi'];

    protected static ?string $model           = CashMutation::class;
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-up-circle';
    protected static ?string $navigationLabel = 'Form SPM (Kas Keluar)';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?int    $navigationSort  = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Utama')
                ->columns(2)
                ->schema([
                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal Pengeluaran')
                        ->default(now())
                        ->required(),
                    Forms\Components\Hidden::make('jenis_pembayaran')->default('Tunai'),
                ]),

            Forms\Components\Section::make('Daftar Uraian Pengeluaran')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship('items')
                        ->schema([
                            Forms\Components\TextInput::make('uraian')
                                ->label('Uraian / Keperluan Pembayaran')
                                ->required()
                                ->columnSpan(2),

                            // Forms\Components\TextInput::make('jumlah')
                            //     ->label('Nominal (Rp)')
                            //     ->numeric()
                            //     ->required()
                            //     ->live(onBlur: true)
                            //     ->afterStateUpdated(function (callable $set, callable $get) {
                            //         self::recalculateTotal($set, $get);
                            //     }),

                            Forms\Components\TextInput::make('jumlah')
                                ->label('Nominal (Rp)')
                                ->numeric()
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (callable $set, callable $get) {
                                    self::recalculateTotal($set, $get);
                                })
                                ->rules([
                                    function ($get) {
                                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                                            // Ambil tanggal transaksi atau gunakan tanggal hari ini
                                            $tanggal = $get('../../tanggal') ?? now();

                                            // Hitung saldo berjalan saat ini
                                            $tahun = \Carbon\Carbon::parse($tanggal)->year;
                                            $bulan = \Carbon\Carbon::parse($tanggal)->month;

                                            $saldoAwal = \App\Models\CashLedger::saldoAwalBulan($tahun, $bulan);

                                            // Hitung total masuk dan keluar sampai tanggal ini
                                            $totalMasuk = \App\Models\CashLedger::whereYear('tanggal', $tahun)
                                                ->whereMonth('tanggal', $bulan)
                                                ->sum('uang_masuk');

                                            $totalKeluar = \App\Models\CashLedger::whereYear('tanggal', $tahun)
                                                ->whereMonth('tanggal', $bulan)
                                                ->sum('uang_keluar');

                                            $sisaSaldo = $saldoAwal + $totalMasuk - $totalKeluar;

                                            // Jika ini form Edit, tambahkan kembali nilai lama transaksi ini ke saldo agar tidak salah hitung
                                            // (Opsional, tergantung kebutuhan edit data)

                                            // Hitung total keseluruhan dari repeater saat ini
                                            $items = $get('../../items') ?? [];
                                            $totalPengeluaranBaru = collect($items)->sum(fn($item) => (float) ($item['jumlah'] ?? 0));

                                            if ($totalPengeluaranBaru > $sisaSaldo) {
                                                $fail("Pengeluaran melebihi sisa saldo kas yang tersedia! Sisa saldo saat ini: Rp " . number_format($sisaSaldo, 0, ',', '.'));
                                            }
                                        };
                                    },
                                ]),

                            Forms\Components\TextInput::make('plat_nomor')
                                ->label('Nomor Polisi Kendaraan')
                                ->placeholder('E 1234 AB'),

                            Forms\Components\TextInput::make('km_awal')
                                ->label('Kilometer Awal')
                                ->numeric(),

                            Forms\Components\TextInput::make('km_akhir')
                                ->label('Kilometer Akhir')
                                ->numeric(),

                            // Forms\Components\TextInput::make('kode_perkiraan')
                            //     ->label('Kode Akun / Perkiraan'),
                        ])
                        ->columns(4)
                        ->addActionLabel('Tambah Baris Uraian')
                        ->defaultItems(1)
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            self::recalculateTotal($set, $get);
                        }),
                ]),

            Forms\Components\Section::make('Total & Otorisasi')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('total_jumlah')
                        ->label('Total Keseluruhan (Rp)')
                        ->numeric()
                        ->readOnly()
                        ->prefix('Rp.'),

                    Forms\Components\TextInput::make('terbilang')
                        ->label('Jumlah Terbilang')
                        ->readOnly()
                        ->placeholder('Akan terisi otomatis...'),

                    Forms\Components\TextInput::make('pembuat')
                        ->label('Dibuat Oleh')
                        ->default('RUDI'),

                    Forms\Components\TextInput::make('pemeriksa')
                        ->label('Diketahui / Diperiksa Oleh')
                        ->default('RIZEN'),

                    Forms\Components\TextInput::make('penerima')
                        ->label('Nama Penerima Dana')
                        ->default(''),
                ]),
        ]);
    }

    protected static function recalculateTotal(callable $set, callable $get): void
    {
        $items = $get('items') ?? [];
        $total = collect($items)->sum(fn($item) => (float) ($item['jumlah'] ?? 0));
        $set('total_jumlah', $total);
        if (class_exists(CashMutation::class)) {
            $set('terbilang', CashMutation::konversiTerbilang($total) . ' rupiah');
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_voucher')
                    ->label('No. Voucher')
                    // ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal Pengeluaran')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_jumlah')
                    ->label('Total Keseluruhan')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('danger')
                    ->weight('bold')
                    ->default(0),

                Tables\Columns\TextColumn::make('pembuat')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('sudah_dicatat_kas')
                    ->label('Tercatat di Kas')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('cash-mutation.print', $record->id))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCashMutations::route('/'),
            'create' => Pages\CreateCashMutation::route('/create'),
            'edit'   => Pages\EditCashMutation::route('/{record}/edit'),
        ];
    }
}
