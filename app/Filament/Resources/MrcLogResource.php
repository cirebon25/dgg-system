<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MrcLogResource\Pages;
use App\Filament\Traits\HasRoleAccess;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\ServiceLog;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MrcLogResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];
    protected static ?string $model = ServiceLog::class;
    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Input MRC';
    protected static ?string $navigationGroup = 'MRC & Billing';
    protected static ?string $slug            = 'mrc-log';
    protected static ?int    $navigationSort  = 2;

    public static function bulanOptions(): array
    {
        return [
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
        ];
    }

    public static function tahunOptions(): array
    {
        $range = range((int) date('Y'), 2024);

        return array_combine($range, $range);
    }

    /**
     * Counter bulan lalu otomatis:
     * 1) input MRC terakhir sebelum bulan yang dipilih,
     * 2) kalau belum ada, counter yang tersimpan di mesin.
     *
     * @return array{0:int,1:int} [bw, color]
     */
    public static function counterLalu($machineId, $tanggal = null): array
    {
        if (! $machineId) {
            return [0, 0];
        }

        $awalBulan = Carbon::parse($tanggal ?: now())->startOfMonth();

        $prev = ServiceLog::where('machine_id', $machineId)
            ->where('is_mrc', true)
            ->where('tanggal', '<', $awalBulan)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();

        if ($prev) {
            return [(int) $prev->counter_bw, (int) ($prev->counter_color ?? 0)];
        }

        $machine = Machine::find($machineId);

        return [(int) ($machine?->counter_bw ?? 0), (int) ($machine?->counter_color ?? 0)];
    }

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
                    ->label('No Seri')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('machine.tipe_model')
                    ->label('Model Mesin')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('counter_bw_lalu')
                    ->label('BW Lalu')
                    ->numeric()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('counter_bw')
                    ->label('BW Sekarang')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('usage_bw')
                    ->label('Usage BW')
                    ->numeric()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('counter_color_lalu')
                    ->label('Color Lalu')
                    ->numeric()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('counter_color')
                    ->label('Color Sekarang')
                    ->numeric()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('usage_color')
                    ->label('Usage Color')
                    ->numeric()
                    ->badge()
                    ->color('warning')
                    ->placeholder('-'),
            ])
            // Tombol Cetak dan Tambah ada di ListMrcLogs::getHeaderActions()
            ->filters([
                Tables\Filters\Filter::make('bulan')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options(static::bulanOptions())
                            ->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->label('Tahun')
                            ->options(static::tahunOptions())
                            ->default(date('Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['month'] ?? null, fn($q, $v) => $q->whereMonth('tanggal', $v))
                            ->when($data['year'] ?? null, fn($q, $v) => $q->whereYear('tanggal', $v));
                    }),
            ])
            ->defaultSort('tanggal', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function form(Form $form): Form
    {
        $hitungBw = fn(Set $set, Get $get) => $set(
            'usage_bw',
            max(0, (int) $get('counter_bw') - (int) $get('counter_bw_lalu'))
        );

        $hitungColor = fn(Set $set, Get $get) => $set(
            'usage_color',
            max(0, (int) $get('counter_color') - (int) $get('counter_color_lalu'))
        );

        // Isi ulang customer + counter lalu otomatis
        $isiOtomatis = function (Set $set, Get $get) use ($hitungBw, $hitungColor) {
            $machineId = $get('machine_id');

            if (! $machineId) {
                $set('customer_id', null);

                return;
            }

            $set('customer_id', Machine::find($machineId)?->customer_id);

            [$bwLalu, $colorLalu] = static::counterLalu($machineId, $get('tanggal'));

            $set('counter_bw_lalu', $bwLalu);
            $set('counter_color_lalu', $colorLalu);

            $hitungBw($set, $get);
            $hitungColor($set, $get);
        };

        return $form->schema([
            Forms\Components\DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required()
                ->default(now())
                ->live()
                ->afterStateUpdated($isiOtomatis),

            Forms\Components\Select::make('machine_id')
                ->relationship('machine', 'serial_number')
                ->label('No Seri')
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated($isiOtomatis),

            Forms\Components\Hidden::make('customer_id'),

            Forms\Components\Placeholder::make('nama_customer')
                ->label('Customer')
                ->content(fn(Get $get) => $get('customer_id')
                    ? (Customer::find($get('customer_id'))?->nama_customer ?? '-')
                    : '-'),

            Forms\Components\Fieldset::make('Counter BW')
                ->schema([
                    Forms\Components\TextInput::make('counter_bw_lalu')
                        ->label('Counter Lalu (otomatis)')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated($hitungBw),

                    Forms\Components\TextInput::make('counter_bw')
                        ->label('Counter Sekarang')
                        ->numeric()
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated($hitungBw),

                    Forms\Components\Hidden::make('usage_bw')
                        ->default(0)
                        ->dehydrated(),
                ])
                ->columns(2),

            Forms\Components\Fieldset::make('Counter Color')
                ->schema([
                    Forms\Components\TextInput::make('counter_color_lalu')
                        ->label('Counter Lalu (otomatis)')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true)
                        ->afterStateUpdated($hitungColor),

                    Forms\Components\TextInput::make('counter_color')
                        ->label('Counter Sekarang')
                        ->numeric()
                        ->live(onBlur: true)
                        ->afterStateUpdated($hitungColor),

                    Forms\Components\Hidden::make('usage_color')
                        ->default(0)
                        ->dehydrated(),
                ])
                ->columns(2),
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
