<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MachineEventResource\Pages;
use App\Filament\Traits\HasRoleAccess;
use App\Models\Machine;
use App\Models\MachineEvent;
use App\Models\Technician;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MachineEventResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];

    protected static ?string $model = MachineEvent::class;

    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Event Mesin';
    protected static ?string $modelLabel      = 'Event Mesin';
    protected static ?string $pluralModelLabel = 'Event Mesin';
    protected static ?int $navigationSort     = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Event Mesin')
                ->schema([
                    Forms\Components\Select::make('machine_id')
                        ->label('No Seri')
                        ->relationship('machine', 'serial_number')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->default(now())
                        ->required(),

                    Forms\Components\Select::make('tipe')
                        ->label('Jenis Event')
                        ->options(MachineEvent::TIPE)
                        ->required()
                        ->live(),

                    Forms\Components\Select::make('customer_id')
                        ->label('Customer')
                        ->relationship('customer', 'nama_customer')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\Select::make('technician_id')
                        ->label('Teknisi')
                        ->options(fn() => Technician::all()->mapWithKeys(
                            fn($t) => [$t->id => $t->nama ?? $t->name ?? ('#' . $t->id)]
                        ))
                        ->searchable()
                        ->nullable(),

                    Forms\Components\TextInput::make('counter')
                        ->label('Counter Mesin')
                        ->numeric()
                        ->nullable(),

                    Forms\Components\Select::make('sparepart_id')
                        ->label('Part')
                        ->relationship('sparepart', 'nama_sparepart')
                        ->searchable()
                        ->nullable()
                        ->visible(fn(Get $get) => in_array($get('tipe'), ['ganti_part', 'perbaikan_ws'])),

                    Forms\Components\TextInput::make('jumlah')
                        ->label('Jumlah Part')
                        ->numeric()
                        ->nullable()
                        ->visible(fn(Get $get) => in_array($get('tipe'), ['ganti_part', 'perbaikan_ws'])),

                    Forms\Components\Textarea::make('keterangan')
                        ->label('Keterangan / Kondisi / Kerusakan')
                        ->nullable()
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('machine.serial_number')
                    ->label('No Seri')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipe')
                    ->label('Event')
                    ->badge()
                    ->formatStateUsing(fn($state) => MachineEvent::TIPE[$state] ?? $state)
                    ->color(fn($state) => MachineEvent::WARNA[$state] ?? 'gray'),

                TextColumn::make('customer.nama_customer')
                    ->label('Customer')
                    ->searchable()
                    ->placeholder('-'),

                // TextColumn::make('sparepart.nama_sparepart')
                //     ->label('Part')
                //     ->placeholder('-'),

                // TextColumn::make('jumlah')
                //     ->label('Qty')
                //     ->placeholder('-'),

                // TextColumn::make('keterangan')
                //     ->label('Keterangan')
                //     ->limit(50)
                //     ->tooltip(fn($record) => $record->keterangan)
                //     ->placeholder('-'),

                TextColumn::make('sumber')
                    ->label('Sumber')
                    ->badge()
                    ->state(fn($record) => ($record->service_log_id || $record->service_log_sparepart_id) ? 'Otomatis' : 'Manual')
                    ->color(fn($state) => $state === 'Otomatis' ? 'gray' : 'primary')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe')
                    ->label('Jenis Event')
                    ->options(MachineEvent::TIPE),

                Tables\Filters\SelectFilter::make('machine_id')
                    ->label('No Seri')
                    ->relationship('machine', 'serial_number')
                    ->searchable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetakRiwayat')
                    ->label('Cetak Riwayat No Seri')
                    ->color('success')
                    ->icon('heroicon-o-printer')
                    ->form([
                        Forms\Components\Select::make('machine_id')
                            ->label('No Seri')
                            ->options(fn() => Machine::orderBy('serial_number')->pluck('serial_number', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(fn(array $data) => redirect()->route('machine.riwayat', $data['machine_id'])),

                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('riwayat')
                    ->label('Riwayat')
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->url(fn($record) => route('machine.riwayat', $record->machine_id))
                    ->openUrlInNewTab(),

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
            'index'  => Pages\ListMachineEvents::route('/'),
            'create' => Pages\CreateMachineEvent::route('/create'),
            'edit'   => Pages\EditMachineEvent::route('/{record}/edit'),
        ];
    }
}