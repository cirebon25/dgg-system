<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypeModelResource\Pages;
use App\Models\TypeModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TypeModelResource extends Resource
{
    protected static ?string $model = TypeModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 5; // Urutan nomor 5


    protected static ?string $modelLabel = 'Tipe Model';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('nama_tipe')
                            ->label('Nama Tipe / Model Mesin')
                            ->placeholder('Contoh: iR 2525 atau M 2040 dn')
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_tipe')
                    ->label('Nama Tipe / Model')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
            'index' => Pages\ListTypeModels::route('/'),
        ];
    }
}
