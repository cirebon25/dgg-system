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

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationLabel = 'Customer';

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

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
                Tables\Columns\TextColumn::make('rayon.nama_rayon')
                    ->label('Rayon')
                    ->badge()
                    ->color(fn (string $state): string => match (trim(strtolower($state))) {
                        'barat daya' => 'info',  // 🔴 Merah
                        'barat' => 'success', // 🟢 Hijau
                        'utara' => 'warning', // 🟡 Kuning
                        'timur' => 'danger',    // 🔵 Biru
                        default => 'gray',    // ⚪ Abu-abu jika tidak cocok
                    }) // ✨ SUDAH DITUTUP DI SINI BOSS
                    ->sortable()
                    ->searchable(), // Tambah koma di akhir jika di dalam array columns,

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
