<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceLogResource\Pages;
use App\Models\ServiceLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceLogResource extends Resource
{
    protected static ?string $model = ServiceLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Kunjungan')
                    ->schema([
                        Forms\Components\Select::make('machine_id')
                            ->relationship('machine', 'serial_number', fn (Builder $query) => $query->where('status', 'Rented'))
                            ->label('SN Mesin')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive() // Supaya counter lalu muncul otomatis saat SN dipilih
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // MENCARI COUNTER TERAKHIR
                                $lastLog = ServiceLog::where('machine_id', $state)
                                    ->latest('tanggal')
                                    ->first();

                                if ($lastLog) {
                                    $set('bw_lalu', $lastLog->counter_bw);
                                    $set('color_lalu', $lastLog->counter_color);
                                } else {
                                    $set('bw_lalu', 0);
                                    $set('color_lalu', 0);
                                }
                            }),
                        
                        Forms\Components\Select::make('tipe_kunjungan')
                            ->options([
                                'RN' => 'RN (Routine)', 'CM' => 'CM (Corrective)', 
                                'RM' => 'RM (Repair)', 'RR' => 'RR (Return)', 
                                'JK' => 'JK (Jaga Kandang)', 'L' => 'L (Lain-lain)',
                            ])->required(),

                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal Service')
                            ->default(now())
                            ->required(),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TimePicker::make('jam_mulai'),
                            Forms\Components\TimePicker::make('jam_selesai'),
                        ]),
                    ])->columns(2),

                Forms\Components\Section::make('Pencatatan Counter')
                    ->schema([
                        // BW Section
                        Forms\Components\TextInput::make('bw_lalu')
                            ->label('BW Bulan Lalu')->numeric()->readOnly()
                            ->helperText('Otomatis dari data servis terakhir'),
                        Forms\Components\TextInput::make('counter_bw')
                            ->label('BW Bulan Ini')->numeric()->required()->reactive()
                            ->afterStateUpdated(fn ($state, $get, $set) => $set('usage_bw', (int)$state - (int)$get('bw_lalu'))),
                        Forms\Components\TextInput::make('usage_bw')
                            ->label('Total Pemakaian BW')->numeric()->readOnly(),

                        // Color Section
                        Forms\Components\TextInput::make('color_lalu')
                            ->label('Color Bulan Lalu')->numeric()->readOnly(),
                        Forms\Components\TextInput::make('counter_color')
                            ->label('Color Bulan Ini')->numeric()->reactive()
                            ->afterStateUpdated(fn ($state, $get, $set) => $set('usage_color', (int)$state - (int)$get('color_lalu'))),
                        Forms\Components\TextInput::make('usage_color')
                            ->label('Total Pemakaian Color')->numeric()->readOnly(),
                    ])->columns(3),

                Forms\Components\Section::make('Detail Perbaikan & Teknisi')
                    ->schema([
                        Forms\Components\Textarea::make('kerusakan')->required(),
                        Forms\Components\Textarea::make('perbaikan')->required(),
                        
                        // DUA NAMA TEKNISI
                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'nama_technician')
                            ->label('Teknisi Utama (Wajib)')
                            ->required(),
                        
                        Forms\Components\TextInput::make('nama_teknisi_manual')
                            ->label('Teknisi Partner (Manual)')
                            ->placeholder('Ketik nama teknisi kedua'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Nama Customer (Lewat Machine)
                Tables\Columns\TextColumn::make('machine.customer.nama_customer')
                    ->label('Customer')
                    ->searchable(),

                // 2. Type Mesin
                Tables\Columns\TextColumn::make('machine.model_mesin')
                    ->label('Type Mesin'),

                // 3. No Seri
                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN Mesin')
                    ->searchable(),

                // 4. Tgl Instal Awal (Dari Deployment)
                Tables\Columns\TextColumn::make('machine.deployment.tanggal_instal')
                    ->label('Tgl Pasang')
                    ->date('d/m/Y'),

                // 5. Tgl Kunjungan
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tgl Kunjungan')
                    ->date('d/m/Y')
                    ->sortable(),

                // 6. Conter Akhir
                Tables\Columns\TextColumn::make('counter_bw')
                    ->label('Counter BW')
                    ->numeric(),

                // 7. Total Pemakaian
                Tables\Columns\TextColumn::make('usage_bw')
                    ->label('Total Pakai')
                    ->badge()
                    // ->color('info'),

                // 8. Tipe Kunjungan dengan Warna
                Tables\Columns\TextColumn::make('tipe_kunjungan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'RN' => 'success', // Hijau
                        'CM' => 'danger',  // Merah
                        'RM' => 'info',    // Biru
                        'RR' => 'warning', // Coklat
                        'JK' => 'primary', // Ungu
                        default => 'gray',
                    }),

                // 9. Nama Teknisi (Tampil 2 Orang)
                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi 1'),
                Tables\Columns\TextColumn::make('nama_teknisi_manual')
                    ->label('Teknisi 2'),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceLogs::route('/'),
            'create' => Pages\CreateServiceLog::route('/create'),
            'edit' => Pages\EditServiceLog::route('/{record}/edit'),
        ];
    }
}