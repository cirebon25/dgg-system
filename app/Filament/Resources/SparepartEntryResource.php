<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartEntryResource\Pages;
use App\Models\Sparepart;
use App\Models\SparepartEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;

use Filament\Tables;
use Filament\Tables\Table;

class SparepartEntryResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];

    protected static ?string $model = SparepartEntry::class;

    protected static ?string $navigationLabel = 'Input Stok Masuk';
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $navigationGroup = 'Gudang & Stok';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengiriman / Supplier')
                    ->description('Detail nota utama dari pihak supplier.')
                    ->schema([
                        Forms\Components\TextInput::make('supplier')
                            ->label('Nama Supplier / Toko')
                            ->default('DGG Bandung')
                            ->placeholder('Contoh: CV. Jaya Bersama Jakarta'),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Catatan Tambahan Nota')
                            ->default('Datang Barang')
                            ->placeholder('Contoh: Kiriman Paket Cargo Gelombang 2')
                            ->rows(2),
                    ])->columns(2),

                Forms\Components\Section::make('Daftar Suku Cadang Masuk')
                    ->description('Masukkan semua jenis sparepart yang datang di nota ini sekaligus.')
                    ->schema([
                        Forms\Components\Repeater::make('items_masuk')
                            ->label('Item Barang')
                            ->schema([
                                Forms\Components\Select::make('sparepart_id')
                                    ->label('Pilih Sparepart')
                                    ->options(
                                        Sparepart::orderBy('nama_sparepart')
                                            ->get()
                                            ->mapWithKeys(fn($s) => [
                                                $s->id => $s->nama_sparepart . ($s->nama_alias ? " — {$s->nama_alias}" : '')
                                            ])
                                            ->toArray()
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah Masuk')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->createItemButtonLabel('Tambah Baris Barang Baru')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Masuk')
                    ->dateTime('d M Y'),
                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Part')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('supplier')
                    ->label('Supplier')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSparepartEntries::route('/'),
            'create' => Pages\CreateSparepartEntry::route('/create'),
        ];
    }
}
