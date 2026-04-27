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
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $lastLog = ServiceLog::where('machine_id', $state)->latest('tanggal')->first();
                                if ($lastLog) {
                                    $set('bw_lalu', $lastLog->counter_bw);
                                    $set('color_lalu', $lastLog->counter_color);
                                }
                            }),
                        
                        Forms\Components\Select::make('tipe_kunjungan')
                            ->options([
                                'RN' => 'RN (Routine) - Hijau',
                                'CM' => 'CM (Corrective) - Merah',
                                'RM' => 'RM (Repair) - Biru',
                                'RR' => 'RR (Return) - Coklat',
                                'JK' => 'JK (Jaga Kandang) - Ungu',
                                'L' => 'L (Lain-lain) - Abu',
                            ])->required(),

                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal Kunjungan')
                            ->default(now())
                            ->required(),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TimePicker::make('jam_mulai'),
                            Forms\Components\TimePicker::make('jam_selesai'),
                        ]),
                    ])->columns(2),

                Forms\Components\Section::make('Pencatatan Counter')
                    ->schema([
                        Forms\Components\TextInput::make('bw_lalu')->label('BW Lalu')->numeric()->readOnly(),
                        Forms\Components\TextInput::make('counter_bw')->label('BW Sekarang')->numeric()->required()->reactive()
                            ->afterStateUpdated(fn ($state, $get, $set) => $set('usage_bw', (int)$state - (int)$get('bw_lalu'))),
                        Forms\Components\TextInput::make('usage_bw')->label('Total Pakai BW')->numeric()->readOnly(),

                        Forms\Components\TextInput::make('color_lalu')->label('Color Lalu')->numeric()->readOnly(),
                        Forms\Components\TextInput::make('counter_color')->label('Color Sekarang')->numeric()->reactive()
                            ->afterStateUpdated(fn ($state, $get, $set) => $set('usage_color', (int)$state - (int)$get('color_lalu'))),
                        Forms\Components\TextInput::make('usage_color')->label('Total Pakai Color')->numeric()->readOnly(),
                    ])->columns(3),

                Forms\Components\Section::make('Detail Teknisi & Perbaikan')
                    ->schema([
                        Forms\Components\Textarea::make('kerusakan')->required(),
                        Forms\Components\Textarea::make('perbaikan')->required(),
                        
                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'nama_technician')
                            ->label('Teknisi 1 (Utama)')
                            ->required(),
                        
                        Forms\Components\TextInput::make('nama_teknisi_manual')
                            ->label('Teknisi 2 (Partner)')
                            ->placeholder('Ketik nama partner teknisi'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('machine.customer.nama_customer')->label('Customer')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('machine.model_mesin')->label('Type Mesin'),
                Tables\Columns\TextColumn::make('machine.serial_number')->label('SN Mesin')->searchable(),
                Tables\Columns\TextColumn::make('machine.deployment.tanggal_instal')->label('Tgl Pasang')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('tanggal')->label('Tgl Kunjungan')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('counter_bw')->label('Counter BW')->numeric(),
                Tables\Columns\TextColumn::make('usage_bw')->label('Total Pakai')->badge()->color('info'),
                Tables\Columns\TextColumn::make('tipe_kunjungan')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'RN' => 'success', 'CM' => 'danger', 'RM' => 'info',
                        'RR' => 'amber', 'JK' => 'primary', 'L'  => 'gray', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('perbaikan')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('technician.nama_technician')->label('Teknisi 1'),
                Tables\Columns\TextColumn::make('nama_teknisi_manual')->label('Teknisi 2'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('printBulanan')
                    ->label('Cetak Per Bulan')
                    ->color('success')
                    ->icon('heroicon-o-calendar')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Pilih Bulan')
                            ->options([
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ])->required()->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->label('Pilih Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()->default(date('Y')),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('service-log.monthly', [
                            'month' => $data['month'],
                            'year' => $data['year'],
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->color('success')
                    ->icon('heroicon-o-printer')
                    ->url(fn (ServiceLog $record) => route('service-log.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('tanggal', 'desc'); // Urutkan berdasarkan tanggal terbaru
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