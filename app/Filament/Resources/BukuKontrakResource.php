<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuKontrakResource\Pages;
use App\Models\BukuKontrak;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BukuKontrakResource extends Resource
{
    protected static ?string $model = BukuKontrak::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Buku Kontrak';
    protected static ?string $modelLabel = 'Buku Kontrak';
    protected static ?string $pluralModelLabel = 'Buku Kontrak';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Kontrak')
                ->schema([
                    Forms\Components\TextInput::make('no_kontrak')
                        ->label('No Kontrak')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\Select::make('customer_id')
                        ->label('Customer')
                        ->relationship('customer', 'nama_customer')
                        ->searchable()
                        ->preload(),
                    Forms\Components\DatePicker::make('tgl_dtg')
                        ->label('Tgl DTG')
                        ->required()
                        ->default(now()),
                ])->columns(3),

            Forms\Components\Section::make('Dibawa Oleh')
                ->schema([
                    Forms\Components\Select::make('technician_id')
                        ->label('Dibawa Oleh')
                        ->relationship('technician', 'nama_technician')
                        ->searchable()
                        ->preload(),
                    Forms\Components\DatePicker::make('tgl_dibawa')
                        ->label('Pada Tgl'),
                ])->columns(2),

            Forms\Components\Section::make('Status Sekarang')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status Sekarang')
                        ->options(BukuKontrak::statusLabels())
                        ->default('depo_cirebon')
                        ->required(),
                    Forms\Components\DatePicker::make('tgl_status')
                        ->label('Pada Tanggal')
                        ->required()
                        ->default(now()),
                    Forms\Components\Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_kontrak')
                    ->label('No Kontrak')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_dtg')
                    ->label('Tgl DTG')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Dibawa Oleh'),
                Tables\Columns\TextColumn::make('tgl_dibawa')
                    ->label('Pada Tgl')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Sekarang')
                    ->badge()
                    ->formatStateUsing(fn($state) => BukuKontrak::statusLabels()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'depo_cirebon'   => 'gray',
                        'dibawa_teknisi' => 'warning',
                        'di_customer'    => 'info',
                        'selesai'        => 'success',
                        default          => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tgl_status')
                    ->label('Pada Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('tgl_dtg', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(BukuKontrak::statusLabels()),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBukuKontraks::route('/'),
            'create' => Pages\CreateBukuKontrak::route('/create'),
            'edit'   => Pages\EditBukuKontrak::route('/{record}/edit'),
        ];
    }
}
