<?php

namespace App\Filament\Resources;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\ServiceLogResource\Pages;
use App\Models\ServiceLog;
use App\Models\Machine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceLogResource extends Resource
{
    protected static ?string $model = ServiceLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Unit')
                    ->schema([
                        Forms\Components\Select::make('machine_id')
                            ->relationship('machine', 'serial_number')
                            ->label('Nomor Seri Mesin')
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $machine = Machine::find($state);
                                    $deployment = $machine->deployments()->latest()->first();
                                    $set('customer_name', $deployment?->customer?->nama_customer ?? 'Unit Standby');
                                    
                                    $lastLog = ServiceLog::where('machine_id', $state)->latest('tanggal')->first();
                                    $set('last_counter_color', $lastLog?->counter_color ?? 0);
                                    $set('last_counter_bw', $lastLog?->counter_bw ?? 0);
                                }
                            })->required(),
                        Forms\Components\TextInput::make('customer_name')->label('Nama Customer')->readOnly()->dehydrated(false),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Kunjungan')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')->default(now())->required(),
                        Forms\Components\Select::make('tipe_kunjungan')
                            ->options(['RN'=>'RN','RM'=>'RM','CM'=>'CM','TN'=>'TN','JK'=>'JK','RR'=>'RR','L'=>'L'])->required(),
                        Forms\Components\TimePicker::make('jam_mulai'), // HARUS ADA
                        Forms\Components\TimePicker::make('jam_selesai'), // HARUS ADA
                    ])->columns(2),

                Forms\Components\Section::make('Pekerjaan & Sparepart')
                    ->schema([
                        Forms\Components\Textarea::make('kerusakan')->columnSpanFull(),
                        Forms\Components\Textarea::make('perbaikan')->columnSpanFull(),
                        Forms\Components\Select::make('sparepart_id')->relationship('sparepart', 'nama_sparepart')->searchable(),
                        Forms\Components\TextInput::make('jumlah_sparepart')->numeric()->default(0),
                        Forms\Components\Select::make('technician_id')->relationship('technician', 'nama_technician')->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Data Counter & Pemakaian')
                    ->schema([
                        Forms\Components\TextInput::make('last_counter_color')->label('Counter Color Lalu')->readOnly()->dehydrated(false),
                        Forms\Components\TextInput::make('counter_color')->label('Counter Color Baru')->numeric()->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => $set('usage_color', (int)$get('counter_color') - (int)$get('last_counter_color'))),
                        Forms\Components\TextInput::make('usage_color')->label('Pemakaian Color')->readOnly(),

                        Forms\Components\TextInput::make('last_counter_bw')->label('Counter BW Lalu')->readOnly()->dehydrated(false),
                        Forms\Components\TextInput::make('counter_bw')->label('Counter BW Baru')->numeric()->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => $set('usage_bw', (int)$get('counter_bw') - (int)$get('last_counter_bw'))),
                        Forms\Components\TextInput::make('usage_bw')->label('Pemakaian BW')->readOnly(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('tanggal')
                ->date('d M Y')
                ->sortable(),

            // MENAMPILKAN NAMA CUSTOMER (Lewat relasi Mesin -> Penempatan)
            Tables\Columns\TextColumn::make('machine.deployments.customer.nama_customer')
                ->label('Customer')
                ->placeholder('Unit Gudang')
                ->searchable(),

            Tables\Columns\TextColumn::make('machine.serial_number')
                ->label('SN Mesin')
                ->searchable(),

            Tables\Columns\TextColumn::make('tipe_kunjungan')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'RN' => 'success', 'RM' => 'info', 'CM' => 'danger',
                    'TN' => 'warning', 'JK' => 'orange', 'RR' => 'gray', 'L' => 'slate',
                    default => 'gray'
                }),

            // MENAMPILKAN COUNTER AKHIR (Data yang diinput saat servis)
            Tables\Columns\TextColumn::make('counter_color')
                ->label('C-Color Akhir')
                ->numeric()
                ->toggleable(),

            Tables\Columns\TextColumn::make('counter_bw')
                ->label('C-BW Akhir')
                ->numeric()
                ->toggleable(),

            // MENAMPILKAN PEMAKAIAN (Selisih)
            Tables\Columns\TextColumn::make('usage_color')
                ->label('Pakai Color')
                ->description(fn ($record) => $record->usage_color . " lbr")
                ->label('Usage C')
                ->color('primary'),

            Tables\Columns\TextColumn::make('usage_bw')
                ->label('Pakai BW')
                ->description(fn ($record) => $record->usage_bw . " lbr")
                ->label('Usage BW'),

            // DETAIL LAINNYA (Sparepart & Perbaikan)
            Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                ->label('Sparepart')
                ->placeholder('-'),
            
            Tables\Columns\TextColumn::make('perbaikan')
                ->label('Tindakan')
                ->limit(20) // Supaya tabel tidak terlalu lebar
                ->tooltip(fn ($record) => $record->perbaikan),

            Tables\Columns\TextColumn::make('technician.nama_technician')
                ->label('Teknisi'),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('machine_id')
                ->relationship('machine', 'serial_number')
                ->label('Cek Per Mesin'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
            Tables\Actions\DeleteBulkAction::make(),
            
            // 1. Tombol Excel (Arsip Digital)
            ExportBulkAction::make()
                ->label('Download Excel')
                ->color('success'),

            // 2. Tombol Cetak Laporan (Arsip Fisik/Print)
            Tables\Actions\BulkAction::make('print_report')
                ->label('Cetak Laporan (Print)')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                    // Ambil ID semua baris yang dicentang
                    $ids = $records->pluck('id')->implode(',');
                    // Buka halaman cetak di tab baru
                    return redirect()->to(route('print.service.bulk', ['ids' => $ids]));
                })
                ->openUrlInNewTab(),
            ]),
        ]);
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