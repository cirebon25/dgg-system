<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechnicianResource\Pages;
use App\Models\Technician;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TechnicianResource extends Resource
{
    protected static ?string $model = Technician::class;

    protected static ?string $navigationLabel = 'Teknisi';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Pilih Rayon dari data yang sudah diinput sebelumnya
                \Filament\Forms\Components\Select::make('rayon_id')
                    ->relationship('rayon', 'nama_rayon') // Menghubungkan ke tabel Rayon
                    ->required(),

                \Filament\Forms\Components\TextInput::make('nama_technician')
                    ->required()
                    ->maxLength(255),

                \Filament\Forms\Components\TextInput::make('nomor_hp')
                    ->tel(), // Format nomor telepon
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rayon.nama_rayon')
                    ->label('Rayon')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_technician')
                    ->label('Nama Teknisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nomor_hp')
                    ->label('No. HP'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Tombol Edit
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Tombol Hapus Massal
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
            'index' => Pages\ListTechnicians::route('/'),
            'create' => Pages\CreateTechnician::route('/create'),
            'edit' => Pages\EditTechnician::route('/{record}/edit'),
        ];
    }
}
