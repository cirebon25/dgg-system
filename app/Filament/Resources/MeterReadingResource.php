<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeterReadingResource\Pages;
use App\Filament\Traits\HasRoleAccess;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\MeterReading;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Closure;
use Illuminate\Database\Eloquent\Model;

class MeterReadingResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin', 'admin_teknik'];

    protected static ?string $model           = MeterReading::class;
    protected static ?string $navigationIcon  = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Meter Reading';
    protected static ?string $navigationGroup = 'MRC & Billing';
    protected static ?string $slug            = 'meter-reading';
    protected static ?int    $navigationSort  = 3;

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

    /** Counter lalu: input terakhir sebelum bulan terpilih, kalau tidak ada pakai counter mesin. */
    public static function counterLalu($machineId, $tanggal = null): array
    {
        if (! $machineId) {
            return [0, 0];
        }

        $awalBulan = Carbon::parse($tanggal ?: now())->startOfMonth();

        $prev = MeterReading::where('machine_id', $machineId)
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
        return parent::getEloquentQuery()->with(['machine', 'customer', 'technician']);
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

        // Saat no seri / tanggal berubah: isi customer, teknisi, dan counter lalu
        $isiOtomatis = function (Set $set, Get $get) use ($hitungBw, $hitungColor) {
            $machineId = $get('machine_id');

            if (! $machineId) {
                $set('customer_id', null);

                return;
            }

            $machine = Machine::with('customer')->find($machineId);

            $set('customer_id', $machine?->customer_id);

            if ($machine?->customer?->technician_id) {
                $set('technician_id', $machine->customer->technician_id);
            }

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

            Forms\Components\Select::make('technician_id')
                ->relationship('technician', 'nama_technician')
                ->label('Teknisi')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Fieldset::make('Counter BW')
                ->schema([
                    Forms\Components\TextInput::make('counter_bw_lalu')
                        ->label('Counter Lalu')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated($hitungBw),

                    Forms\Components\TextInput::make('counter_bw')
                        ->label('Counter Sekarang')
                        ->numeric()
                        ->required()
                        ->gte('counter_bw_lalu')
                        ->live(onBlur: true)
                        ->afterStateUpdated($hitungBw)
                        ->rules([
                            fn(Get $get, ?Model $record): Closure => function (string $attribute, $value, Closure $fail) use ($get, $record) {
                                $machineId = $get('machine_id');
                                $tanggal   = $get('tanggal');

                                if (! $machineId || ! $tanggal) {
                                    return;
                                }

                                $tgl   = Carbon::parse($tanggal);
                                $color = $get('counter_color');

                                $duplikat = MeterReading::where('machine_id', $machineId)
                                    ->whereMonth('tanggal', $tgl->month)
                                    ->whereYear('tanggal', $tgl->year)
                                    ->where('counter_bw', (int) $value)
                                    ->when(
                                        $color === null || $color === '',
                                        fn($q) => $q->whereNull('counter_color'),
                                        fn($q) => $q->where('counter_color', (int) $color)
                                    )
                                    ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                    ->exists();

                                if ($duplikat) {
                                    $fail('Ditolak: no seri ini sudah punya input dengan counter yang sama di bulan ini.');
                                }
                            },
                        ]),

                    Forms\Components\TextInput::make('usage_bw')
                        ->label('Usage BW (Sekarang - Lalu)')
                        ->numeric()
                        ->default(0)
                        ->readOnly()
                        ->dehydrated(),
                ])
                ->columns(3),

            Forms\Components\Fieldset::make('Counter Color')
                ->schema([
                    Forms\Components\TextInput::make('counter_color_lalu')
                        ->label('Counter Lalu')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true)
                        ->afterStateUpdated($hitungColor),

                    Forms\Components\TextInput::make('counter_color')
                        ->label('Counter Sekarang')
                        ->numeric()
                        ->gte('counter_color_lalu')
                        ->live(onBlur: true)
                        ->afterStateUpdated($hitungColor),

                    Forms\Components\TextInput::make('usage_color')
                        ->label('Usage Color (Sekarang - Lalu)')
                        ->numeric()
                        ->default(0)
                        ->readOnly()
                        ->dehydrated(),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->label('Tanggal')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('machine.serial_number')->label('No Seri')->searchable(),
                Tables\Columns\TextColumn::make('customer.nama_customer')->label('Customer')->searchable(),
                Tables\Columns\TextColumn::make('technician.nama_technician')->label('Teknisi'),
                Tables\Columns\TextColumn::make('counter_bw_lalu')->label('BW Lalu')->numeric(),
                Tables\Columns\TextColumn::make('counter_bw')->label('BW Sekarang')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('usage_bw')->label('Usage BW')->numeric()->badge()->color('info'),
                Tables\Columns\TextColumn::make('counter_color_lalu')->label('CL Lalu')->numeric()->placeholder('-'),
                Tables\Columns\TextColumn::make('counter_color')->label('CL Sekarang')->numeric()->placeholder('-'),
                Tables\Columns\TextColumn::make('usage_color')->label('Usage CL')->numeric()->badge()->color('warning')->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\Filter::make('bulan')
                    ->form([
                        Forms\Components\Select::make('month')->label('Bulan')
                            ->options(static::bulanOptions())->default(date('m')),
                        Forms\Components\Select::make('year')->label('Tahun')
                            ->options(static::tahunOptions())->default(date('Y')),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query
                        ->when($data['month'] ?? null, fn($q, $v) => $q->whereMonth('tanggal', $v))
                        ->when($data['year'] ?? null, fn($q, $v) => $q->whereYear('tanggal', $v))),
            ])
            ->defaultSort('tanggal', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMeterReadings::route('/'),
            'create' => Pages\CreateMeterReading::route('/create'),
            'edit'   => Pages\EditMeterReading::route('/{record}/edit'),
        ];
    }
}
