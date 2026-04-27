<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeploymentResource\Pages;
use App\Models\Deployment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeploymentResource extends Resource
{
    protected static ?string $model = Deployment::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck'; // Icon truk biar pas buat pengiriman
    
    protected static ?string $navigationGroup = 'Transaksi'; // Biar rapi di menu samping

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pemasangan')
                    ->description('Detail customer dan mesin yang akan dipasang.')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'nama_customer')
                            ->label('Customer')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('machine_id')
                            ->relationship('machine', 'serial_number', function (Builder $query) {
                                // HANYA munculkan mesin yang statusnya 'Ready' di gudang
                                return $query->where('status', 'Ready');
                            })
                            ->label('SN Mesin')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->serial_number} - {$record->model_mesin}")
                            ->required()
                            ->searchable()
                            ->preload()
                            ->unique(ignoreRecord: true), // Keamanan ganda biar SN tidak duplikat

                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'nama_technician')
                            ->label('Teknisi Pasang')
                            ->required()
                            ->preload(),

                        Forms\Components\DatePicker::make('tanggal_instal')
                            ->label('Tanggal Pasang')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y'),
                            
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Catatan Tambahan')
                            ->placeholder('Contoh: Lantai 2, dekat meja admin')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN Mesin')
                    ->description(fn (Deployment $record): string => $record->machine->model_mesin ?? '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('tanggal_instal')
                    ->label('Tgl Pasang')
                    ->date('d M Y')
                    ->sortable(),

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
                Tables\Filters\SelectFilter::make('customer')
                    ->relationship('customer', 'nama_customer'),
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
        return [
            // Bisa ditambah RelationManager Customer nanti
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