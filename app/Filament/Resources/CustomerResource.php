<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder; // <--- TAMBAHKAN INI
use Illuminate\Database\Eloquent\SoftDeletingScope; // <--- TAMBAHKAN INI

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationLabel = 'Customer';
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        // ... (Tetap seperti kodingan Akang) ...
        return $form->schema([
            Forms\Components\Select::make('rayon_id')
                ->relationship('rayon', 'nama_rayon')
                ->required()
                ->preload(),
            Forms\Components\TextInput::make('nama_customer')
                ->label('Nama Instansi / Perorangan')
                ->required(),
            Forms\Components\TextInput::make('kota')
                ->label('Kota / Kabupaten')
                ->required(),
            Forms\Components\Textarea::make('alamat')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rayon.nama_rayon')->label('Rayon')->badge()->sortable()->searchable(),
                TextColumn::make('nama_customer')->label('Pelanggan / Alamat')->searchable()
                    ->description(fn (Customer $record): string => $record->alamat ?? '-')
                    ->summarize(Count::make()->label('Total Pelanggan')),
                TextColumn::make('kota')->label('Kota')->searchable(),
                TextColumn::make('deployments_count')->label('Unit Terpasang')->counts('deployments')->suffix(' Unit')->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rayon_id')
                    ->relationship('rayon', 'nama_rayon')->label('Filter Rayon'),
                
                // 1. TAMBAHKAN FILTER ARSIP DI SINI
                Tables\Filters\TrashedFilter::make(), 
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // 2. TAMBAHKAN AKSI RESTORE (BALIKIN DARI ARSIP)
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(), // <--- BISA RESTORE BANYAK
                ]),
            ]);
    }

    // 3. WAJIB TAMBAHKAN INI AGAR DATA TERHAPUS BISA MUNCUL SAAT DIFILTER
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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