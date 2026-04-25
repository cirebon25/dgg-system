<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            \Filament\Forms\Components\Select::make('rayon_id')
                ->relationship('rayon', 'nama_rayon')
                ->required()
                ->preload(),
                
            \Filament\Forms\Components\TextInput::make('nama_customer')
                ->label('Nama Instansi / Perorangan')
                ->required(),

            \Filament\Forms\Components\TextInput::make('kota')
                ->label('Kota / Kabupaten')
                ->placeholder('Contoh: Indramayu')
                ->required(),

            \Filament\Forms\Components\Textarea::make('alamat')
                ->columnSpanFull(),
        ]);
}

   public static function table(Table $table): Table
    {
    return $table
        ->columns([
            // Menampilkan Nama Rayon (mengambil dari tabel Rayon)
            Tables\Columns\TextColumn::make('rayon.nama_rayon')
                ->label('Rayon')
                ->sortable()
                ->searchable(),

            // Menampilkan Nama Customer
            Tables\Columns\TextColumn::make('nama_customer')
                ->label('Nama Customer')
                ->searchable()
                ->sortable(),

            // Menampilkan Kota
            Tables\Columns\TextColumn::make('kota')
                ->label('Kota')
                ->searchable(),

            // Menampilkan Tanggal Dibuat
            Tables\Columns\TextColumn::make('created_at')
                ->label('Tgl Input')
                ->dateTime('d/m/Y')
                ->sortable(),
        ])
        ->filters([
            // Anda bisa menambah filter di sini nanti
        ])
        ->actions([
            Tables\Actions\EditAction::make(), // Agar tombol Edit muncul
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
