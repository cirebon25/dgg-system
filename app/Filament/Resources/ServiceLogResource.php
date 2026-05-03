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
use Carbon\Carbon;

class ServiceLogResource extends Resource
{
    protected static ?string $model = ServiceLog::class;
    protected static ?string $navigationLabel = 'Input Servis Teknisi';
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Kunjungan')
                    ->schema([
                        Forms\Components\Select::make('machine_id')
                            ->relationship('machine', 'serial_number')
                            ->label('SN Mesin')
                            ->required()
                            ->searchable()
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
                                'RN' => 'RN (Intal Baru)', 'CM' => 'CM (Call Maintenance)', 
                                'RM' => 'RM (Kunjungan Rutin)', 'RR' => 'RR (Ganti Mesin)', 
                                'JK' => 'JK (Jaringan komputer)', 'L' => 'L (Lanjut)',
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
                        Forms\Components\TextInput::make('usage_bw')->label('Usage BW')->numeric()->readOnly(),

                        Forms\Components\TextInput::make('color_lalu')->label('Color Lalu')->numeric()->readOnly(),
                        Forms\Components\TextInput::make('counter_color')->label('Color Sekarang')->numeric()->reactive()
                            ->afterStateUpdated(fn ($state, $get, $set) => $set('usage_color', (int)$state - (int)$get('color_lalu'))),
                        Forms\Components\TextInput::make('usage_color')->label('Usage Color')->numeric()->readOnly(),
                    ])->columns(3),

                // --- BAGIAN SPAREPART (REPEATER) SUDAH KEMBALI ---
                Forms\Components\Section::make('Sparepart yang Diganti')
                    ->description('Kosongkan jika tidak ada pergantian sparepart.')
                    ->schema([
                        Forms\Components\Repeater::make('serviceLogSpareparts')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('sparepart_id')
                                    ->relationship('sparepart', 'nama_sparepart')
                                    ->label('Pilih Sparepart')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('jumlah')
                                    ->numeric()
                                    ->default(1),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Sparepart'),
                    ]),

                Forms\Components\Section::make('Detail Teknisi & Perbaikan')
                    ->schema([
                        Forms\Components\Textarea::make('kerusakan')->required(),
                        Forms\Components\Textarea::make('perbaikan')->required(),
                        
                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'nama_technician')
                            ->label('Teknisi Utama')->required(),
                        
                        Forms\Components\TextInput::make('nama_teknisi_manual')
                            ->label('Teknisi Partner (Manual)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Nama Customer & Model
                Tables\Columns\TextColumn::make('machine.deployment.customer.nama_customer')
                    ->label('Customer / Model')
                    ->placeholder('Data Kosong')
                    ->description(fn ($record): string => "Model: " . ($record->machine?->model_mesin ?? '-'))
                    ->searchable()
                    ->sortable(),

                // 2. SN & Tgl Pasang
                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN / Tgl Pasang')
                    ->description(function ($record) {
                        $tgl = $record->machine?->deployment?->tanggal_instal;
                        return "Instal: " . ($tgl ? \Carbon\Carbon::parse($tgl)->format('d/m/Y') : '-');
                    })
                    ->searchable(),

                // 3. Tgl Kunjungan
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tgl Servis')
                    ->date('d/m/Y')
                    ->sortable(),

                // 4. Counter BW & Color (MENGGUNAKAN KOLOM ASLI AGAR TIDAK KOSONG)
                Tables\Columns\TextColumn::make('counter_bw')
                    ->label('Counter (BW/CL)')
                    ->html()
                    ->formatStateUsing(fn ($record) => 
                        "BW: " . number_format($record->counter_bw) . "<br>CL: " . number_format($record->counter_color)
                    ),

                // 5. Usage BW & Color (MENGGUNAKAN KOLOM ASLI AGAR TIDAK KOSONG)
                Tables\Columns\TextColumn::make('usage_bw')
                    ->label('Usage (BW/CL)')
                    ->html()
                    ->formatStateUsing(fn ($record) => 
                        "<span style='color:#3b82f6; font-weight:bold;'>BW: " . number_format($record->usage_bw) . "</span><br>" .
                        "<span style='color:#ef4444; font-weight:bold;'>CL: " . number_format($record->usage_color) . "</span>"
                    ),

                // 6. Tipe Kunjungan
                Tables\Columns\TextColumn::make('tipe_kunjungan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'RN' => 'success', 'CM' => 'danger', 'RM' => 'info', 
                        'RR' => 'amber', 'JK' => 'primary', default => 'gray',
                    }),
                
                // 7. Perbaikan
                Tables\Columns\TextColumn::make('perbaikan')
                    ->label('Tindakan')
                    ->limit(20)
                    ->toggleable(),
                    
                // 8. Teknisi
                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->description(fn ($record) => $record->nama_teknisi_manual ? "Partner: " . $record->nama_teknisi_manual : ''),
            ])
            ->groups([
                Tables\Grouping\Group::make('tanggal')
                    ->label('Bulan Kunjungan')
                    ->date()
                    ->collapsible(),
            ])
            ->headerActions([
                // TOMBOL CETAK PER BULAN KEMBALI
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
                // TOMBOL EDIT DAN PRINT PER BARIS KEMBALI
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (ServiceLog $record) => route('service-log.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
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