<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashLedgerResource\Pages;
use App\Models\CashLedger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Traits\HasRoleAccess;

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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Transaksi Kas')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('tanggal')
                        ->label('Tanggal Transaksi')
                        ->disabled(),

                    Forms\Components\TextInput::make('no_surat')
                        ->label('No. Bukti / Referensi')
                        ->disabled(),

                    Forms\Components\TextInput::make('keterangan')
                        ->label('Uraian Transaksi')
                        ->disabled()
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('uang_masuk')
                        ->label('Pemasukan Kas (Rp)')
                        ->prefix('Rp')
                        ->disabled(),

                    Forms\Components\TextInput::make('uang_keluar')
                        ->label('Pengeluaran Kas (Rp)')
                        ->prefix('Rp')
                        ->disabled(),

                    Forms\Components\TextInput::make('dibuat_oleh')
                        ->label('Nama Pembuat')
                        ->disabled(),
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
                    ->label('No. Bukti')
                    ->searchable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Uraian Transaksi')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('uang_masuk')
                    ->label('Pemasukan Kas')
                    ->formatStateUsing(
                        fn($state) => $state > 0
                            ? 'Rp ' . number_format($state, 0, ',', '.')
                            : '-'
                    )
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    // ->summarize(Sum::make()->money('IDR')->label('Total Masuk')),
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
                        $totalMasuk = CashLedger::whereYear('tanggal', $record->tanggal->year)
                            ->whereMonth('tanggal', $record->tanggal->month)
                            ->where(function ($q) use ($record) {
                                $q->where('tanggal', '<', $record->tanggal)
                                    ->orWhere(function ($q) use ($record) {
                                        $q->whereDate('tanggal', $record->tanggal)
                                            ->where('id', '<=', $record->id);
                                    });
                            })
                            ->sum('uang_masuk');

                        $totalKeluar = CashLedger::whereYear('tanggal', $record->tanggal->year)
                            ->whereMonth('tanggal', $record->tanggal->month)
                            ->where(function ($q) use ($record) {
                                $q->where('tanggal', '<', $record->tanggal)
                                    ->orWhere(function ($q) use ($record) {
                                        $q->whereDate('tanggal', $record->tanggal)
                                            ->where('id', '<=', $record->id);
                                    });
                            })
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
                            ->options([
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
                            ])
                            ->default(now()->month),
                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(array_combine(
                                range(now()->year, now()->year - 3),
                                range(now()->year, now()->year - 3)
                            ))
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
                            $namaBulan = [
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
                            return 'Periode: ' . $namaBulan[$data['bulan']] . ' ' . $data['tahun'];
                        }
                        return null;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning'),
                Tables\Actions\Action::make('cetak')
                    ->label('Cetak Laporan')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn(CashLedger $record) => route('cetak.kas-bulanan', [
                        'bulan' => $record->tanggal->month,
                        'tahun' => $record->tanggal->year,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetak_laporan')
                    ->label('Cetak Laporan Bulan Ini')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn() => route('cetak.kas-bulanan', [
                        'bulan' => now()->month,
                        'tahun' => now()->year,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashLedgers::route('/'),
            'edit'  => Pages\EditCashLedger::route('/{record}/edit'),
        ];
    }
}
