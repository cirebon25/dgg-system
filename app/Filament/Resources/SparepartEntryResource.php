<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartEntryResource\Pages;
use App\Models\SparepartEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SparepartEntryResource extends Resource
{
    protected static ?string $model = SparepartEntry::class;

    protected static ?string $navigationLabel = 'Input Stok Masuk';
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $navigationGroup = 'Gudang & Stok';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Penerimaan Barang Baru')
                    ->description('Input barang yang baru datang untuk menambah stok gudang.')
                    ->schema([
                        Forms\Components\Select::make('sparepart_id')
                            ->relationship('sparepart', 'nama_sparepart')
                            ->label('Pilih Sparepart')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('jumlah')
                            ->label('Jumlah Masuk')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Forms\Components\TextInput::make('supplier')
                            ->label('Nama Supplier / Toko'),
                        Forms\Components\Textarea::make('keterangan')
                            ->placeholder('Contoh: Barang datang dari Jakarta'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Tgl Masuk')->dateTime('d M Y'),
                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')->label('Nama Part'),
                Tables\Columns\TextColumn::make('jumlah')->label('Jumlah')->badge()->color('success'),
                Tables\Columns\TextColumn::make('supplier')->label('Supplier'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSparepartEntries::route('/'),
            'create' => Pages\CreateSparepartEntry::route('/create'),
        ];
    }
}