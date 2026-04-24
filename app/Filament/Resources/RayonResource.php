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
    protected static ?string $model = Rayon::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Pengaturan Form Input (Saat Tambah Data)
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_rayon')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    // Pengaturan Tabel (Saat Lihat Daftar Data)
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_rayon')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Tanggal Dibuat'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListRayons::route('/'),
            'create' => Pages\CreateRayon::route('/create'),
            'edit' => Pages\EditRayon::route('/{record}/edit'),
        ];
    }
}