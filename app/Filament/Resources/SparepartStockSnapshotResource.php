<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartStockSnapshotResource\Pages;
use App\Models\SparepartStockSnapshot;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;
use App\Filament\Traits\HasRoleAccess;
use Illuminate\Database\Eloquent\Builder;

class SparepartStockSnapshotResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];
    protected static ?string $model           = SparepartStockSnapshot::class;
    protected static ?string $navigationIcon  = 'heroicon-o-camera';
    protected static ?string $navigationLabel = 'Snapshot Stok Bulanan';
    protected static ?string $navigationGroup = 'Gudang & Stok';
    protected static ?bool   $canCreate       = false;
    protected static ?int $navigationSort = 11;

    public static function table(Table $table): Table
    {
        return $table
            // Default filter query agar hanya menampilkan bulan & tahun saat ini jika belum difilter
            ->modifyQueryUsing(function (Builder $query) {
                return $query->where('bulan', date('n'))
                    ->where('tahun', date('Y'))
                    ->join('spareparts', 'sparepart_stock_snapshots.sparepart_id', '=', 'spareparts.id')
                    ->orderBy('spareparts.no_part', 'asc')
                    ->select('sparepart_stock_snapshots.*');
            })
            ->columns([
                Tables\Columns\TextColumn::make('sparepart.no_part')
                    ->label('No Part')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('sparepart.code_part')
                    ->label('Kode Part')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bulan')
                    ->label('Bulan')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::create()->month($state)->translatedFormat('F')),

                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun'),

                Tables\Columns\TextColumn::make('stok_akhir')
                    ->label('Stok Akhir')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('snapshot_at')
                    ->label('Diambil Pada')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bulan')
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
                    ->default(date('n')), // Default filter UI ke bulan berjalan

                Tables\Filters\SelectFilter::make('tahun')
                    ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                    ->default(date('Y')), // Default filter UI ke tahun berjalan
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetakPemakaian')
                    ->label('Cetak Laporan Pemakaian')
                    ->icon('heroicon-o-printer')
                    ->color('success')
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
                            ->required()
                            ->default(date('n')),
                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()
                            ->default(date('Y')),
                    ])
                    ->action(fn(array $data) => redirect()->route('sparepart.pemakaian-bulanan', $data)),

                Tables\Actions\Action::make('ambilSnapshotManual')
                    ->label('Ambil Snapshot & Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('bulan')
                            ->label('Pilih Bulan Snapshot')
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
                            ->required()
                            ->default(date('n')),
                        Forms\Components\Select::make('tahun')
                            ->label('Pilih Tahun Snapshot')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()
                            ->default(date('Y')),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Ambil Snapshot & Download PDF')
                    ->modalDescription('Sistem akan memperbarui data snapshot stok sesuai bulan & tahun yang dipilih, kemudian otomatis membuka file PDF laporan pemakaian.')
                    ->action(function (array $data) {
                        Artisan::call('sparepart:snapshot', [
                            '--bulan' => $data['bulan'],
                            '--tahun' => $data['tahun'],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Snapshot berhasil diambil!')
                            ->success()
                            ->send();

                        return redirect()->route('sparepart.pemakaian-bulanan', [
                            'bulan' => $data['bulan'],
                            'tahun' => $data['tahun']
                        ]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSparepartStockSnapshots::route('/'),
        ];
    }
}
