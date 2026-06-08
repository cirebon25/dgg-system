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
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Traits\HasRoleAccess;

class CashLedgerResource extends Resource
{
    use HasRoleAccess;
    protected static ?string $model           = CashLedger::class;
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Buku Kas Umum';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $modelLabel      = 'Transaksi Kas';
    protected static ?string $pluralModelLabel = 'Buku Kas Umum';
    protected static array $allowedRoles = ['admin', 'keuangan', 'manager'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Transaksi')->schema([

                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now())
                    ->native(false),

                Forms\Components\TextInput::make('no_surat')
                    ->label('No. Surat')
                    ->placeholder('Otomatis jika kosong')
                    ->helperText('Format: 01/VI/26 — dibuat otomatis jika tidak diisi')
                    ->maxLength(30),

                Forms\Components\TextInput::make('keterangan')
                    ->label('Keterangan / Pembelian')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('uang_masuk')
                    ->label('Uang Masuk (Rp)')
                    ->prefix('Rp')
                    ->placeholder('0')
                    ->integer()                          // pakai integer, bukan numeric()
                    ->minValue(0)
                    ->rules(['integer', 'min:0'])        // validasi server-side
                    ->live(onBlur: true),               // update hanya saat blur, bukan tiap keystroke

                Forms\Components\TextInput::make('uang_keluar')
                    ->label('Uang Keluar (Rp)')
                    ->prefix('Rp')
                    ->placeholder('0')
                    ->integer()
                    ->minValue(0)
                    ->rules(['integer', 'min:0'])
                    ->live(onBlur: true),

                Forms\Components\TextInput::make('dibuat_oleh')
                    ->label('Dibuat Oleh')
                    ->default(fn() => auth()->user()?->name ?? '')
                    ->maxLength(100),

            ])->columns(2),
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
                    ->searchable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('uang_masuk')
                    ->label('Uang Masuk')
                    ->formatStateUsing(
                        fn($state) => $state > 0
                            ? 'Rp ' . number_format($state, 0, ',', '.')
                            : '-'
                    )
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->summarize(Sum::make()->money('IDR')->label('Total Masuk')),

                Tables\Columns\TextColumn::make('uang_keluar')
                    ->label('Uang Keluar')
                    ->formatStateUsing(
                        fn($state) => $state > 0
                            ? 'Rp ' . number_format($state, 0, ',', '.')
                            : '-'
                    )
                    ->color(fn($state) => $state > 0 ? 'danger' : 'gray')
                    ->summarize(Sum::make()->money('IDR')->label('Total Keluar')),

                // ── KOLOM SALDO AKHIR (running balance) ──────────────────
                Tables\Columns\TextColumn::make('sisa_saldo')
                    ->label('Sisa Saldo')
                    ->getStateUsing(function (CashLedger $record) {
                        $saldoAwal = CashLedger::saldoAwalBulan(
                            $record->tanggal->year,
                            $record->tanggal->month
                        );

                        // BENAR: pakai scope langsung tanpa wrap Builder manual
                        $totalMasuk = CashLedger::whereYear('tanggal', $record->tanggal->year)
                            ->whereMonth('tanggal', $record->tanggal->month)
                            ->where('id', '<=', $record->id)
                            ->sum('uang_masuk');

                        $totalKeluar = CashLedger::whereYear('tanggal', $record->tanggal->year)
                            ->whereMonth('tanggal', $record->tanggal->month)
                            ->where('id', '<=', $record->id)
                            ->sum('uang_keluar');

                        return $saldoAwal + $totalMasuk - $totalKeluar;
                    })
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('dibuat_oleh')
                    ->label('Dibuat Oleh')
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
                Tables\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn(CashLedger $record) => route('cetak.kas-bulanan', [
                        'bulan' => $record->tanggal->month,
                        'tahun' => $record->tanggal->year,
                    ]))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
