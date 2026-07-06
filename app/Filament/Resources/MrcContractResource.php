<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MrcContractResource\Pages;
use App\Models\MrcContract;
use App\Models\Machine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\RawJs;
use App\Filament\Traits\HasRoleAccess;

class MrcContractResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];

    protected static ?string $model          = MrcContract::class;
    protected static ?string $navigationIcon  = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationLabel = 'Pemakaian MRC';
    protected static ?string $navigationGroup = 'MRC & Billing';
    protected static ?int    $navigationSort  = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Mesin & Customer')
                    ->schema([
                        Forms\Components\Select::make('machine_id')
                            ->label('Mesin (SN)')
                            ->options(
                                Machine::with('customer')
                                    ->where('status', 'Rented')
                                    ->get()
                                    ->mapWithKeys(fn($m) => [
                                        $m->id => $m->serial_number . ' — ' . ($m->customer?->nama_customer ?? '-') . ' (' . $m->tipe_model . ')'
                                    ])
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $machine = Machine::with('customer')->find($state);
                                if ($machine) {
                                    $set('customer_id', $machine->customer_id);
                                }
                            }),

                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'nama_customer')
                            ->label('Customer')
                            ->searchable()
                            ->required(),

                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai Kontrak')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai Kontrak')
                            ->nullable(),

                        Forms\Components\Toggle::make('aktif')
                            ->label('Kontrak Aktif')
                            ->default(true),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Harga & Kuota')
                    ->schema([
                        Forms\Components\TextInput::make('harga_sewa')
                            ->label('Harga Sewa / Bulan (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.'),

                        Forms\Components\TextInput::make('free_bw')
                            ->label('Free Kuota BW (lembar)')
                            ->numeric()
                            ->suffix('lembar')
                            ->required(),

                        Forms\Components\TextInput::make('harga_bw')
                            ->label('Harga per Lembar BW (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Forms\Components\TextInput::make('free_color')
                            ->label('Free Kuota Color (lembar)')
                            ->numeric()
                            ->suffix('lembar')
                            ->default(0),

                        Forms\Components\TextInput::make('harga_color')
                            ->label('Harga per Lembar Color (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('machine.serial_number')
                    ->label('SN Mesin')
                    ->searchable()
                    ->description(fn($record) => $record->machine?->tipe_model),

                TextColumn::make('customer.nama_customer')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('harga_sewa')
                    ->label('Harga Sewa')
                    ->money('IDR'),

                TextColumn::make('free_bw')
                    ->label('Free BW')
                    ->suffix(' lbr'),

                TextColumn::make('harga_bw')
                    ->label('Harga/Lbr BW')
                    ->money('IDR'),

                TextColumn::make('free_color')
                    ->label('Free Color')
                    ->suffix(' lbr'),

                TextColumn::make('harga_color')
                    ->label('Harga/Lbr Color')
                    ->money('IDR'),

                Tables\Columns\IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date('d/m/Y'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetakTagihan')
                    ->label('Cetak Tagihan MRC')
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
                    ])
                    ->action(fn(array $data) => redirect()->route('mrc.tagihan', $data)),

                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMrcContracts::route('/'),
            'create' => Pages\CreateMrcContract::route('/create'),
            'edit'   => Pages\EditMrcContract::route('/{record}/edit'),
        ];
    }
}