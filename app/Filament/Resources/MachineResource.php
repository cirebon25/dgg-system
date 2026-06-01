<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MachineResource\Pages;
use App\Models\Machine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MachineResource extends Resource
{
    protected static ?string $model = Machine::class;
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Data Mesin';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 2;
    protected static bool $globallySearchable = true;

    public static function getGloballySearchableAttributes(): array
    {
        return ['serial_number', 'tipe_model', 'status'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record->serial_number;
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Tipe'   => $record->tipe_model,
            'Status' => $record->status,
            'Lokasi' => $record->customer?->nama_customer ?? 'Gudang DGG',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Unit Mesin')
                    ->description('Masukkan detail mesin fotokopi sesuai label SN di bodi mesin.')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'nama_customer')
                            ->label('Lokasi / Pelanggan')
                            ->placeholder('Pilih Customer (Kosongkan jika masih di Gudang)')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('serial_number')
                            ->label('Serial Number (SN)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: XVR005848'),

                        Forms\Components\Select::make('tipe_model')
                            ->label('Tipe / Model Mesin')
                            ->options(\App\Models\TypeModel::pluck('nama_tipe', 'nama_tipe'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama_tipe')
                                    ->label('Tipe Model Baru')
                                    ->required()
                                    ->unique('type_models', 'nama_tipe'),
                            ])
                            ->createOptionUsing(function (array $data): string {
                                $tipe = \App\Models\TypeModel::create($data);
                                return $tipe->nama_tipe;
                            }),

                        Forms\Components\Select::make('status')
                            ->label('Status Mesin')
                            ->options([
                                'Ready' => 'Ready (Siap Pakai)',
                                'Rented' => 'Rented (Sedang Disewa)',
                                'Refurbish' => 'Refurbish (Dalam Perbaikan)',
                            ])
                            ->default('Ready')
                            ->required(),

                        Forms\Components\Textarea::make('keterangan_awal')
                            ->label('Keterangan Mesin')
                            ->placeholder('Misal: EX LUAR / EX RENTAL')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Teknis Mesin')
                    ->description('Informasi tambahan untuk spesifikasi teknis unit')
                    ->schema([
                        Forms\Components\TextInput::make('volt')
                            ->label('Voltase')
                            ->placeholder('Contoh: 110V / 220V'),

                        Forms\Components\TextInput::make('finisher')
                            ->label('Finisher')
                            ->placeholder('Contoh: Internal Finisher / Booklet'),

                        Forms\Components\TextInput::make('cover')
                            ->label('Cover')
                            ->placeholder('Contoh: Platen Cover / DADF'),

                        Forms\Components\TextInput::make('kaset')
                            ->label('Jumlah Kaset')
                            ->placeholder('Contoh: 2 Tray / 4 Tray'),

                        Forms\Components\TextInput::make('double_scan')
                            ->label('Double Scan (Qty)')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->orderByRaw("CASE WHEN status = 'Ready' THEN 0 ELSE 1 END")
                    ->orderBy('created_at', 'desc');
            })
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Input Mesin Baru')
                    ->icon('heroicon-o-plus'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipe_model')
                    ->label('Tipe Mesin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Ready' => 'success',
                        'Rented' => 'warning',
                        'Refurbish' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Lokasi / Pelanggan')
                    ->placeholder('Gudang DGG'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('monitor')
                    ->label('Monitor Part')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('warning')
                    ->url(fn($record) => route('sparepart.monitor', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ← OPTIMASI: Eager load customer agar kolom 'Lokasi / Pelanggan'
    // tidak trigger N+1 query (1 query untuk semua, bukan 1 per baris)
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMachines::route('/'),
            'create' => Pages\CreateMachine::route('/create'),
            'edit' => Pages\EditMachine::route('/{record}/edit'),
        ];
    }
}
