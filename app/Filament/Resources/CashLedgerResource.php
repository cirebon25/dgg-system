<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashLedgerResource\Pages;
use App\Filament\Traits\HasRoleAccess;
use App\Models\CashLedger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CashLedgerResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model            = CashLedger::class;
    protected static ?string $navigationIcon   = 'heroicon-o-book-open';
    protected static ?string $navigationLabel  = 'Buku Kas Umum';
    protected static ?string $navigationGroup  = 'Keuangan';
    protected static ?int    $navigationSort   = 10;
    protected static ?string $modelLabel       = 'Buku Kas';
    protected static ?string $pluralModelLabel = 'Buku Kas Umum';
    protected static array   $allowedRoles     = ['admin', 'keuangan', 'manager'];

    protected static function bulanOptions(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    protected static function tahunOptions(): array
    {
        return array_combine(
            range(now()->year, now()->year - 3),
            range(now()->year, now()->year - 3)
        );
    }

    public static function canCreate(): bool
    {
        // Buku Kas adalah satu-satunya tempat pencatatan saldo resmi,
        // diisi manual oleh admin (kas masuk & keluar).
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Transaksi Kas')
                ->columns(2)
                ->schema([
                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal Transaksi')
                        ->required()
                        ->default(now())
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get, $state, string $operation) {
                            // Saat buat baru & kas keluar, usulan nomor mengikuti bulan tanggal
                            if ($operation === 'create' && (float) $get('uang_masuk') <= 0 && $state) {
                                $set('no_surat', CashLedger::nomorSuratBerikutnya($state));
                            }
                        }),

                    Forms\Components\TextInput::make('no_surat')
                        ->label('No. Surat')
                        ->placeholder('Otomatis, bisa diubah manual')
                        ->helperText('Kosongkan untuk diisi otomatis. Kas masuk tidak memakai no. surat.')
                        ->default(fn() => CashLedger::nomorSuratBerikutnya(now()))
                        ->hidden(fn(Get $get) => (float) $get('uang_masuk') > 0)
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('isiOtomatis')
                                ->icon('heroicon-o-arrow-path')
                                ->tooltip('Isi nomor otomatis')
                                ->action(function (Set $set, Get $get) {
                                    $set('no_surat', CashLedger::nomorSuratBerikutnya($get('tanggal') ?: now()));
                                })
                        ),

                    Forms\Components\Textarea::make('keterangan')
                        ->label('Uraian Transaksi')
                        ->required()
                        ->rows(2)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('uang_masuk')
                        ->label('Pemasukan Kas (Rp)')
                        ->numeric()
                        ->default(0)
                        ->prefix('Rp')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            if ((float) $state > 0) {
                                $set('no_surat', null);
                            } elseif (blank($get('no_surat'))) {
                                $set('no_surat', CashLedger::nomorSuratBerikutnya($get('tanggal') ?: now()));
                            }
                        }),

                    Forms\Components\TextInput::make('uang_keluar')
                        ->label('Pengeluaran Kas (Rp)')
                        ->numeric()
                        ->default(0)
                        ->prefix('Rp'),

                    Forms\Components\TextInput::make('dibuat_oleh')
                        ->label('Nama Pembuat')
                        ->default(fn() => auth()->user()?->name ?? ''),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_urut')
                    ->label('No.')
                    ->sortable()
                    ->width(50),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('no_surat')
                    ->label('No. Surat')
                    ->formatStateUsing(
                        fn($state, CashLedger $record) => $record->uang_masuk > 0 ? '-' : ($state ?: '-')
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('uraian_transaksi')
                    ->label('Uraian Transaksi')
                    ->state(function (CashLedger $record) {
                        // 1. Data historis dari Mutasi Kas lama
                        if ($record->cashMutation && $record->cashMutation->items->isNotEmpty()) {
                            return $record->cashMutation->items->pluck('uraian')->filter()->implode(', ');
                        }

                        // 2. Data historis dari Kas Masuk lama
                        if ($record->cashReceipt) {
                            return $record->cashReceipt->uraian ?? $record->cashReceipt->keterangan ?? '-';
                        }

                        // 3. Entry manual
                        return $record->keterangan ?? '-';
                    })
                    ->searchable(query: function (Builder $query, string $search) {
                        return $query->where('keterangan', 'like', "%{$search}%")
                            ->orWhereHas('cashMutation.items', function ($q) use ($search) {
                                $q->where('uraian', 'like', "%{$search}%");
                            });
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('uang_masuk')
                    ->label('Pemasukan Kas')
                    ->formatStateUsing(
                        fn($state) => $state > 0
                            ? 'Rp ' . number_format($state, 0, ',', '.')
                            : '-'
                    )
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->summarize([
                        Sum::make()
                            ->label('Total Masuk')
                            ->using(fn($query) => $query->sum('uang_masuk'))
                            ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                    ]),

                Tables\Columns\TextColumn::make('uang_keluar')
                    ->label('Pengeluaran Kas')
                    ->formatStateUsing(
                        fn($state) => $state > 0
                            ? 'Rp ' . number_format($state, 0, ',', '.')
                            : '-'
                    )
                    ->color(fn($state) => $state > 0 ? 'danger' : 'gray')
                    ->summarize(Sum::make()->money('IDR')->label('Total Keluar')),

                Tables\Columns\TextColumn::make('sisa_saldo')
                    ->label('Sisa Saldo')
                    ->getStateUsing(function (CashLedger $record) {
                        $saldoAwal = CashLedger::saldoAwalBulan(
                            $record->tanggal->year,
                            $record->tanggal->month
                        );

                        $sampaiBarisIni = function ($q) use ($record) {
                            $q->where('tanggal', '<', $record->tanggal)
                                ->orWhere(function ($q) use ($record) {
                                    $q->whereDate('tanggal', $record->tanggal)
                                        ->where('id', '<=', $record->id);
                                });
                        };

                        $totalMasuk = CashLedger::whereYear('tanggal', $record->tanggal->year)
                            ->whereMonth('tanggal', $record->tanggal->month)
                            ->where($sampaiBarisIni)
                            ->sum('uang_masuk');

                        $totalKeluar = CashLedger::whereYear('tanggal', $record->tanggal->year)
                            ->whereMonth('tanggal', $record->tanggal->month)
                            ->where($sampaiBarisIni)
                            ->sum('uang_keluar');

                        return $saldoAwal + $totalMasuk - $totalKeluar;
                    })
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->weight('bold')
                    ->summarize(
                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Saldo Akhir')
                            ->using(function ($query) {
                                $first = $query->first();

                                if (! $first) {
                                    return 0;
                                }

                                $tanggal = \Carbon\Carbon::parse($first->tanggal);

                                $saldoAwal = CashLedger::saldoAwalBulan(
                                    $tanggal->year,
                                    $tanggal->month
                                );

                                return $saldoAwal
                                    + $query->sum('uang_masuk')
                                    - $query->sum('uang_keluar');
                            })
                            ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ),

                Tables\Columns\TextColumn::make('dibuat_oleh')
                    ->label('Nama Pembuat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal', 'asc')
            ->filters([
                Tables\Filters\Filter::make('bulan')
                    ->form([
                        Forms\Components\Select::make('bulan')
                            ->label('Bulan')
                            ->options(static::bulanOptions())
                            ->default(now()->month),
                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(static::tahunOptions())
                            ->default(now()->year),
                    ])
                    ->query(function ($query, array $data) {
                        if (filled($data['bulan']) && filled($data['tahun'])) {
                            $query->whereYear('tanggal', $data['tahun'])
                                ->whereMonth('tanggal', $data['bulan']);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (filled($data['bulan']) && filled($data['tahun'])) {
                            return 'Periode: ' . static::bulanOptions()[$data['bulan']] . ' ' . $data['tahun'];
                        }
                        return null;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning'),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),

                Tables\Actions\Action::make('cetak')
                    ->label('Cetak Laporan')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(function (CashLedger $record, Tables\Contracts\HasTable $livewire) {
                        $filters = $livewire->tableFilters;
                        $bulan = $filters['bulan']['bulan'] ?? $record->tanggal->month;
                        $tahun = $filters['bulan']['tahun'] ?? $record->tanggal->year;

                        return route('cetak.kas-bulanan', [
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                        ]);
                    })
                    ->openUrlInNewTab(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetak_laporan')
                    ->label('Cetak Laporan Periode Ini')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('bulan')
                            ->label('Pilih Bulan')
                            ->options(static::bulanOptions())
                            ->default(now()->month)
                            ->required(),

                        Forms\Components\Select::make('tahun')
                            ->label('Pilih Tahun')
                            ->options(static::tahunOptions())
                            ->default(now()->year)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $url = route('cetak.kas-bulanan', [
                            'bulan' => $data['bulan'],
                            'tahun' => $data['tahun'],
                        ]);

                        return redirect()->away($url);
                    })
                    ->modalHeading('Cetak Laporan Buku Kas')
                    ->modalDescription('Silakan pilih bulan dan tahun laporan yang ingin dicetak.')
                    ->modalSubmitActionLabel('Cetak Sekarang'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCashLedgers::route('/'),
            'create' => Pages\CreateCashLedger::route('/create'),
            'edit'   => Pages\EditCashLedger::route('/{record}/edit'),
        ];
    }
}
