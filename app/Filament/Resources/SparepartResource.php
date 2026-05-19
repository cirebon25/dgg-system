<?php

namespace App\Playground; // Sesuaikan namespace aplikasi Akang jika bukan Playground (biasanya App\Filament\Resources)
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
    protected static ?string $navigationGroup = 'MASTER DATA';
    protected static ?int $navigationSort = 3; // Urutan nomor 3

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Barang')
                    ->schema([
                        Forms\Components\TextInput::make('nama_sparepart')
                            ->label('Nama Sparepart')
                            ->required(),
                        Forms\Components\TextInput::make('code_part')
                            ->label('Code Part'),
                        Forms\Components\TextInput::make('no_part')
                            ->label('No Part')
                            ->required(),
                    ])->columns(3),

                // Bagian Manajemen Stok manual kita buang dari form Create/Edit 
                // Karena sekarang input barang masuk sudah pakai menu "Input Stok Masuk" tersendiri!
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code_part')
                    ->label('Kode Part')
                    ->searchable(),

                TextColumn::make('no_part')
                    ->label('No Part')
                    ->searchable(),

                // 🌟 TAMPILKAN TOTAL MASUK REAL-TIME DARI SUPPLIER
                TextColumn::make('calculated_saldo_masuk')
                    ->label('Total Masuk')
                    ->badge()
                    ->color('info'),

                // 🌟 TAMPILKAN TOTAL KELUAR REAL-TIME (DIPINJAM TEKNISI)
                TextColumn::make('calculated_saldo_keluar')
                    ->label('Total Keluar')
                    ->badge()
                    ->color('warning'),

                // 🌟 TAMPILKAN SISA STOK FISIK DI GUDANG PUSAT (REAL-TIME)
                TextColumn::make('calculated_stok')
                    ->label('Stok Gudang')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state <= 2 => 'danger',   // Merah kalau kritis
                        $state <= 5 => 'warning',  // Kuning kalau menipis
                        default => 'success',      // Hijau kalau aman
                    })
                    ->weight('bold')
                    ->sortable(),
            ])
            ->headerActions([
                // 🟢 1. TOMBOL IMPORT CSV
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
                        array_shift($rows); // Buang header

                        foreach ($rows as $row) {
                            Sparepart::updateOrCreate(
                                ['no_part' => $row[1]], // Kunci: No Part
                                [
                                    'nama_sparepart' => $row[0],
                                    'code_part' => $row[3] ?? null,
                                ]
                            );

                            // Agar stok masuk dari hasil import CSV juga tercatat resmi di riwayat, 
                            // Kita buatkan langsung record transaksinya di tabel Entries jika jumlahnya > 0
                            if ((int)$row[2] > 0) {
                                $sp = Sparepart::where('no_part', $row[1])->first();
                                if ($sp) {
                                    \App\Models\SparepartEntry::create([
                                        'sparepart_id' => $sp->id,
                                        'jumlah' => (int)$row[2],
                                        'supplier' => 'Import Awal CSV',
                                        'keterangan' => 'Inisialisasi stok awal via file CSV',
                                    ]);
                                }
                            }
                        }
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Import Berhasil!')
                            ->success()
                            ->send();
                    }),

                // 🔵 2. TOMBOL REKAP KELUAR BULANAN
                Tables\Actions\Action::make('rekapKeluar')
                    ->label('Cetak Rekap Keluar')
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
                            ])->required()->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->label('Pilih Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()->default(date('Y')),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('sparepart.report.outflow', $data);
                    }),

                Tables\Actions\Action::make('cetakRealtime')
                    ->label('Cetak Rekap Realtime')
                    ->color('warning') // Warna kuning oranye biar mencolok dan beda sendiri
                    ->icon('heroicon-o-printer')
                    ->url(fn() => route('cetak.rekap-sparepart')) // Mengarah ke jalur cetak langsung
                    ->openUrlInNewTab(), // Buka di tab baru biar halaman inputan gak hilang


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
            'index' => Pages\ListSpareparts::route('/'),
            'create' => Pages\CreateSparepart::route('/create'),
            'edit' => Pages\EditSparepart::route('/{record}/edit'),
        ];
    }
}
