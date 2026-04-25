<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeploymentResource\Pages;
use App\Filament\Resources\DeploymentResource\RelationManagers;
use App\Models\Deployment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeploymentResource extends Resource
{
    protected static ?string $model = Deployment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

   public static function form(Form $form): Form
    {
    return $form
        ->schema([
            \Filament\Forms\Components\Select::make('customer_id')
                ->relationship('customer', 'nama_customer')
                ->required()
                ->searchable()
                ->preload(),

            \Filament\Forms\Components\Select::make('machine_id')
                ->relationship('machine', 'serial_number') // Pilih berdasarkan No Seri
                ->required()
                ->searchable()
                ->preload(),

            \Filament\Forms\Components\Select::make('technician_id')
                ->relationship('technician', 'nama_technician')
                ->required()
                ->preload(),

            \Filament\Forms\Components\DatePicker::make('tanggal_instal')
                ->label('Tanggal Pasang')
                ->required()
                ->default(now()),
                
            \Filament\Forms\Components\Textarea::make('keterangan')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
    return $table
        ->columns([
            // Menampilkan Nama Customer
            Tables\Columns\TextColumn::make('customer.nama_customer')
                ->label('Customer')
                ->searchable()
                ->sortable(),

            // Menampilkan Serial Number Mesin
            Tables\Columns\TextColumn::make('machine.serial_number')
                ->label('SN Mesin')
                ->searchable()
                ->sortable(),

            // Menampilkan Nama Teknisi
            Tables\Columns\TextColumn::make('technician.nama_technician')
                ->label('Teknisi Pasang')
                ->sortable(),

            // Menampilkan Tanggal Instal
            Tables\Columns\TextColumn::make('tanggal_instal')
                ->label('Tgl Pasang')
                ->date('d M Y')
                ->sortable(),

            // Status Mesin (Bisa diambil dari tabel machine)
            Tables\Columns\TextColumn::make('machine.status')
                ->label('Status Unit')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Ready' => 'success',
                    'Rented' => 'warning',
                    'Refurbish' => 'danger',
                    default => 'gray',
                }),
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
            'index' => Pages\ListDeployments::route('/'),
            'create' => Pages\CreateDeployment::route('/create'),
            'edit' => Pages\EditDeployment::route('/{record}/edit'),
        ];
    }
}
