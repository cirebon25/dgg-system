<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartResource\Pages;
use App\Models\Sparepart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class SparepartResource extends Resource
{
    protected static ?string $model = Sparepart::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;
    // ===== GLOBAL SEARCH =====
    protected static bool $globallySearchable = true;

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_sparepart', 'nama_alias', 'no_part', 'code_part'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record->nama_sparepart;
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'No Part' => $record->no_part ?? '-',
            'Alias'   => $record->nama_alias ?? '-',
            'Stok'    => $record->stok . ' pcs',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Barang')
                    ->schema([
                        Forms\Components\TextInput::make('nama_sparepart')
                            ->label('Nama Sparepart')
                            ->placeholder('Contoh: Toner Super Silver Yellow Polos 500gr')
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('nama_alias')
                            ->label('Nama Alias / Nama Teknisi')
                            ->placeholder('Contoh: toner blue, toner kuning')
                            ->helperText('Nama yang dikenal teknisi. Pisahkan koma jika lebih dari satu.')
                            ->nullable()
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('code_part')
                            ->label('Code Part')
                            ->nullable()
                            ->required(false),

                        Forms\Components\TextInput::make('no_part')
                            ->label('No Part')
                            ->required(),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn(Sparepart $record): string => $record->nama_alias
                            ? ' Alias: ' . $record->nama_alias
                            : ''
                    ),

                TextColumn::make('code_part')
                    ->label('Kode Part')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('no_part')
                    ->label('No Part')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('harga_beli')
                    ->label('Harga Beli')
                    ->money('IDR')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('stok')
                    ->label('Stok Gudang')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state <= 2 => 'danger',
                        $state <= 5 => 'warning',
                        default     => 'success',
                    })
                    ->weight('bold')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import_sparepart')
                    ->label('Import CSV')
                    ->icon('heroicon-m-arrow-up-tray')
                    ->color('info')
                    ->form([
                        Forms\Components\FileUpload::make('file_csv')
                            ->label('Pilih File CSV/Excel')
                            ->disk('public')
                            ->directory('imports')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $filePath = storage_path('app/public/' . $data['file_csv']);
                        $rows = Excel::toArray([], $filePath)[0];
                        array_shift($rows);

                        foreach ($rows as $row) {
                            $sp = Sparepart::updateOrCreate(
                                ['no_part' => $row[1]],
                                [
                                    'nama_sparepart' => $row[0],
                                    'code_part'      => $row[3] ?? null,
                                    'nama_alias'     => $row[4] ?? null,
                                ]
                            );

                            $jumlahMasuk = (int) ($row[2] ?? 0);
                            if ($jumlahMasuk > 0) {
                                \App\Models\SparepartEntry::create([
                                    'sparepart_id' => $sp->id,
                                    'jumlah'       => $jumlahMasuk,
                                    'supplier'     => 'Import Awal CSV',
                                    'keterangan'   => 'Inisialisasi stok awal via file CSV',
                                ]);
                                $sp->increment('stok', $jumlahMasuk);
                            }
                        }

                        if (file_exists($filePath)) unlink($filePath);

                        \Filament\Notifications\Notification::make()
                            ->title('Import Berhasil!')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('rekapKeluar')
                    ->label('Cetak Rekap Part Terpakai   Keluar')
                    ->color('danger')
                    ->icon('heroicon-o-printer')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Pilih Bulan')
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
                                '12' => 'Desember',
                            ])
                            ->required()
                            ->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->label('Pilih Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()
                            ->default(date('Y')),
                    ])
                    ->action(fn(array $data) => redirect()->route('sparepart.report.outflow', $data)),

                Tables\Actions\Action::make('cetakRealtime')
                    ->label('Cetak Rekap Saldo Realtime')
                    ->color('warning')
                    ->icon('heroicon-o-printer')
                    ->url(fn() => route('saldo-sparepart'))
                    ->openUrlInNewTab(),

                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListSpareparts::route('/'),
            'create' => Pages\CreateSparepart::route('/create'),
            'edit'   => Pages\EditSparepart::route('/{record}/edit'),
        ];
    }

    // Helper static untuk dipakai di semua resource lain
    // Contoh penggunaan: Sparepart::getOptionsWithAlias()
    public static function getOptionsWithAlias(): array
    {
        return Sparepart::all()
            ->mapWithKeys(fn($s) => [
                $s->id => $s->nama_sparepart . ($s->nama_alias ? " — {$s->nama_alias}" : '')
            ])
            ->toArray();
    }
}
