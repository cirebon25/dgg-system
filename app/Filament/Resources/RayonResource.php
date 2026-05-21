<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RayonResource\Pages;
use App\Models\Rayon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RayonResource extends Resource
{
    protected static ?string $navigationLabel = 'Rayon';
    protected static ?string $model = Rayon::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 4; // Urutan nomor 4

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Setting Wilayah')
                    ->description('Tentukan nama wilayah dan tim teknisi yang bertugas.')
                    ->schema([
                        Forms\Components\TextInput::make('nama_rayon')
                            ->label('Nama Rayon')
                            ->required()
                            ->maxLength(255),

                        // Fokus ke relasi JAMAK (technicians)
                        Forms\Components\Select::make('technicians')
                            ->label('Tim Teknisi Penanggung Jawab')
                            ->relationship('technicians', 'nama_technician')
                            ->multiple() // Wajib untuk Many-to-Many
                            ->preload()
                            ->searchable()
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_rayon')
                    ->label('Wilayah Rayon')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // Menampilkan daftar nama teknisi dengan badge biru
                Tables\Columns\TextColumn::make('technicians.nama_technician')
                    ->label('Tim Teknisi')
                    ->badge()
                    ->color('info')
                    ->separator(','),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->label('Dibuat Pada')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListRayons::route('/'),
            'create' => Pages\CreateRayon::route('/create'),
            'edit' => Pages\EditRayon::route('/{record}/edit'),
        ];
    }
}
