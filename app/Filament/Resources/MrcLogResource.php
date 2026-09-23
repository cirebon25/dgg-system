<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MrcLogResource\Pages;
use App\Models\ServiceLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HasRoleAccess;

class MrcLogResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];
    protected static ?string $model = ServiceLog::class;
    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Rekap MRC';
    protected static ?string $navigationGroup = 'MRC & Billing';
    protected static ?string $slug            = 'mrc-log';
    protected static ?int    $navigationSort  = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_mrc', true)
            ->with(['machine', 'customer', 'technician']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN Mesin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('machine.tipe_model')
                    ->label('Model Mesin')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('counter_bw_lalu')
                    ->label('Counter BW Lalu')
                    ->numeric()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('counter_bw')
                    ->label('Counter BW')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('usage_bw')
                    ->label('Usage BW')
                    ->numeric()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('counter_color_lalu')
                    ->label('Counter Color Lalu')
                    ->numeric()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('counter_color')
                    ->label('Counter Color')
                    ->numeric()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('usage_color')
                    ->label('Usage Color')
                    ->numeric()
                    ->badge()
                    ->color('warning')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Data MRC'),

                Tables\Actions\Action::make('cetakMrc')
                    ->label('Cetak Rekap MRC')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ])
                            ->required()
                            ->default(date('m')),

                        Forms\Components\Select::make('year')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()
                            ->default(date('Y')),

                        // Tambahan Input Counter Pembanding (Opsional jika ingin kalkulasi manual saat cetak)
                        Forms\Components\Section::make('Parameter Tambahan Cetak')
                            ->schema([
                                Forms\Components\TextInput::make('counter_bw_lalu')
                                    ->label('Default Counter BW Bulan Lalu')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('counter_color_lalu')
                                    ->label('Default Counter Color Bulan Lalu')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(2),
                    ])
                    ->action(fn(array $data) => redirect()->route('mrc.rekap', $data)),
            ])
            ->filters([
                Tables\Filters\Filter::make('bulan')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ])
                            ->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->default(date('Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['month'], fn($q) => $q->whereMonth('tanggal', $data['month']))
                            ->when($data['year'],  fn($q) => $q->whereYear('tanggal',  $data['year']));
                    }),
            ])
            ->defaultSort('tanggal', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('tanggal')
                ->label('Tanggal Input')
                ->required()
                ->default(now()),

            Forms\Components\Select::make('machine_id')
                ->relationship('machine', 'serial_number')
                ->label('Mesin')
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(function (callable $set, callable $get, $state) {
                    if (! $state) {
                        return;
                    }

                    $machine = \App\Models\Machine::find($state);

                    if ($machine && $machine->customer_id) {
                        $set('customer_id', $machine->customer_id);
                    }

                    $counterBwLalu    = $machine->counter_bw ?? 0;
                    $counterColorLalu = $machine->counter_color ?? 0;

                    $set('counter_bw_lalu', $counterBwLalu);
                    $set('counter_color_lalu', $counterColorLalu);
                    $set('usage_bw', max(0, (int) $get('counter_bw') - (int) $counterBwLalu));
                    $set('usage_color', max(0, (int) $get('counter_color') - (int) $counterColorLalu));
                }),

            Forms\Components\Select::make('customer_id')
                ->relationship('customer', 'nama_customer')
                ->label('Customer')
                ->searchable()
                ->preload(),

            Forms\Components\Select::make('technician_id')
                ->relationship('technician', 'nama_technician')
                ->label('Teknisi')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Fieldset::make('Counter BW')
                ->schema([
                    Forms\Components\TextInput::make('counter_bw_lalu')
                        ->label('Counter BW Lalu')
                        ->numeric()
                        ->default(0)
                        ->disabled()
                        ->dehydrated(false)
                        ->live()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $set('usage_bw', max(0, (int) $get('counter_bw') - (int) $get('counter_bw_lalu')));
                        }),

                    Forms\Components\TextInput::make('counter_bw')
                        ->label('Counter BW Sekarang')
                        ->numeric()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $set('usage_bw', max(0, (int) $get('counter_bw') - (int) $get('counter_bw_lalu')));
                        }),

                    Forms\Components\TextInput::make('usage_bw')
                        ->label('Selisih BW')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(),
                ])
                ->columns(3),

            Forms\Components\Fieldset::make('Counter Color')
                ->schema([
                    Forms\Components\TextInput::make('counter_color_lalu')
                        ->label('Counter Color Lalu')
                        ->numeric()
                        ->default(0)
                        ->disabled()
                        ->dehydrated(false)
                        ->live()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $set('usage_color', max(0, (int) $get('counter_color') - (int) $get('counter_color_lalu')));
                        }),

                    Forms\Components\TextInput::make('counter_color')
                        ->label('Counter Color Sekarang')
                        ->numeric()
                        ->live()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $set('usage_color', max(0, (int) $get('counter_color') - (int) $get('counter_color_lalu')));
                        }),

                    Forms\Components\TextInput::make('usage_color')
                        ->label('Selisih Color')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(),
                ])
                ->columns(3),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMrcLogs::route('/'),
            'create' => Pages\CreateMrcLog::route('/create'),
            'edit'   => Pages\EditMrcLog::route('/{record}/edit'),
        ];
    }
}
