<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartUsageHeaderResource\Pages;
use App\Models\PartUsageHeader;
use App\Models\TechnicianStock;
use App\Models\Sparepart;
use App\Models\Machine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartUsageHeaderResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];

    protected static ?string $model = PartUsageHeader::class;
    protected static ?string $navigationLabel = 'Pemakaian Part Ws';
    protected static ?string $pluralModelLabel = 'Pemakaian Part Teknisi';
    protected static ?string $modelLabel = 'Pemakaian Part';
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $isTechnician = $user?->hasRole('teknisi');

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Sumber & Kunjungan')
                    ->description('Pilih sumber stok (Gudang atau Tas Teknisi), teknisi, tanggal, serta daftar part yang digunakan beserta No Seri Mesin.')
                    ->schema([
                        // Pilih Sumber Pengambilan Stok
                        Forms\Components\Select::make('sumber_stok')
                            ->label('Sumber Pengambilan Stok')
                            ->options([
                                'tas' => 'Tas Teknisi',
                                'gudang' => 'Gudang Pusat',
                            ])
                            ->default('tas')
                            ->required()
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'nama_technician')
                            ->label('Nama Teknisi')
                            ->required()
                            ->searchable()
                            ->default(fn() => $user?->technician_id)
                            ->disabled($isTechnician)
                            ->dehydrated(),

                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal Pemakaian')
                            ->default(now())
                            ->required(),

                        Forms\Components\Repeater::make('items')
                            ->label('Detail Sparepart & No Seri Mesin (Unit)')
                            ->relationship('items')
                            ->schema([
                                // Pilih No Seri Mesin
                                Forms\Components\Select::make('machine_id')
                                    ->relationship('machine', 'serial_number')
                                    ->getOptionLabelFromRecordUsing(fn(Machine $record) => "SN: {$record->serial_number} — " . ($record->customer?->nama_customer ?? 'Tanpa Customer'))
                                    ->label('No Seri Mesin')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(2),

                                // Pilih Sparepart (Otomatis menyesuaikan apakah sumbernya Gudang atau Tas)
                                Forms\Components\Select::make('sparepart_id')
                                    ->label('Sparepart')
                                    ->options(function (Forms\Get $get) use ($user, $isTechnician) {
                                        $sumber = $get('../../sumber_stok');
                                        $techId = $isTechnician ? $user->technician_id : $get('../../technician_id');

                                        if ($sumber === 'tas') {
                                            if (!$techId) return [];
                                            // Ambil dari tas teknisi yang stoknya > 0
                                            return TechnicianStock::where('technician_id', $techId)
                                                ->where('jumlah', '>', 0)
                                                ->with('sparepart')
                                                ->get()
                                                ->mapWithKeys(fn($ts) => [
                                                    $ts->sparepart_id => $ts->sparepart->nama_sparepart . " (Sisa di Tas: {$ts->jumlah})"
                                                ]);
                                        } else {
                                            // Ambil dari Gudang Pusat yang stoknya > 0
                                            return Sparepart::where('stok', '>', 0)
                                                ->orderBy('nama_sparepart')
                                                ->get()
                                                ->mapWithKeys(fn($s) => [
                                                    $s->id => $s->nama_sparepart . " (Sisa Gudang: {$s->stok})"
                                                ]);
                                        }
                                    })
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn(Forms\Set $set) => $set('jumlah', 1))
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->rules([
                                        fn(Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $user, $isTechnician) {
                                            $sumber = $get('../../sumber_stok');
                                            $techId = $isTechnician ? $user->technician_id : $get('../../technician_id');
                                            $sparepartId = $get('sparepart_id');

                                            if (!$sparepartId) return;

                                            if ($sumber === 'tas') {
                                                if (!$techId) return;
                                                $techStock = TechnicianStock::where('technician_id', $techId)
                                                    ->where('sparepart_id', $sparepartId)
                                                    ->first();
                                                $max = $techStock ? $techStock->jumlah : 0;
                                                if ((int)$value > $max) {
                                                    $fail("Melebihi tas! Sisa: {$max} pcs.");
                                                }
                                            } else {
                                                $sparepart = Sparepart::find($sparepartId);
                                                $max = $sparepart ? $sparepart->stok : 0;
                                                if ((int)$value > $max) {
                                                    $fail("Melebihi gudang! Sisa: {$max} pcs.");
                                                }
                                            }
                                        },
                                    ])
                                    ->columnSpan(1),
                            ])
                            ->columns(5)
                            ->minItems(1)
                            ->addActionLabel('+ Tambah Item Sparepart')
                            ->reorderable(false)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Catatan / Keluhan / Tiket (Opsional)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sumber_stok')
                    ->label('Sumber')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'gudang' => 'success',
                        'tas' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => strtoupper($state)),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->searchable()
                    ->hidden(fn() => auth()->user()?->hasRole('teknisi')),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Jumlah Item')
                    ->counts('items')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('sumber_stok')
                    ->options([
                        'tas' => 'Tas Teknisi',
                        'gudang' => 'Gudang Pusat',
                    ])
                    ->label('Filter Sumber'),

                Tables\Filters\SelectFilter::make('technician_id')
                    ->relationship('technician', 'nama_technician')
                    ->label('Filter Teknisi')
                    ->hidden(fn() => auth()->user()?->hasRole('teknisi')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => !auth()->user()?->hasRole('teknisi')),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery()->with(['technician', 'items.sparepart', 'items.machine.customer']);

        if ($user && $user->hasRole('teknisi')) {
            if (!empty($user->technician_id)) {
                return $query->where('technician_id', $user->technician_id);
            }
            return $query->whereHas('technician', function ($q) use ($user) {
                $q->where('nama_technician', 'LIKE', '%' . $user->name . '%');
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartUsageHeaders::route('/'),
            'create' => Pages\CreatePartUsageHeader::route('/create'),
        ];
    }
}
