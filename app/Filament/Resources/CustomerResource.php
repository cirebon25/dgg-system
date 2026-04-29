<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Count;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('rayon_id')
                    ->relationship('rayon', 'nama_rayon')
                    ->required()
                    ->preload(),
                    
                Forms\Components\TextInput::make('nama_customer')
                    ->label('Nama Instansi / Perorangan')
                    ->required(),

                Forms\Components\TextInput::make('kota')
                    ->label('Kota / Kabupaten')
                    ->placeholder('Contoh: Indramayu')
                    ->required(),

                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom Rayon
                TextColumn::make('rayon.nama_rayon')
                    ->label('Rayon')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                // Kolom Nama & Alamat (Stack)
                TextColumn::make('nama_customer')
                    ->label('Pelanggan / Alamat')
                    ->searchable()
                    ->description(fn (Customer $record): string => $record->alamat ?? '-')
                    ->sortable()
                    ->summarize(Count::make()->label('Total Pelanggan')),

                // Kolom Kota
                TextColumn::make('kota')
                    ->label('Kota')
                    ->searchable(),

                // MENAMPILKAN JUMLAH MESIN YANG DISEWA
                TextColumn::make('deployments_count')
                    ->label('Unit Terpasang')
                    ->counts('deployments') 
                    ->suffix(' Unit')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray'),

                // Tanggal Input
                TextColumn::make('created_at')
                    ->label('Tgl Input')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rayon_id')
                    ->relationship('rayon', 'nama_rayon')
                    ->label('Filter Rayon'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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