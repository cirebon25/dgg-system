<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MachineReturnResource\Pages;
use App\Models\MachineReturn;
use App\Models\Machine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class MachineReturnResource extends Resource
{
    protected static ?string $model            = MachineReturn::class;
    protected static ?string $navigationLabel  = 'Retur ke Bandung';
    protected static ?string $navigationIcon   = 'heroicon-o-arrow-uturn-left';
    protected static ?string $navigationGroup  = 'Transaksi';
    protected static ?int    $navigationSort   = 3;
    protected static ?string $modelLabel       = 'Retur Mesin';
    protected static ?string $pluralModelLabel = 'Retur Mesin';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('📦 Form Retur Mesin ke Bandung')
                ->description('Catat pengiriman mesin dari Gudang Cirebon ke Bandung untuk servis / perbaikan.')
                ->schema([

                    Forms\Components\Repeater::make('machine_list')
                        ->label('Mesin yang Diretur')
                        ->schema([
                            Forms\Components\Select::make('machine_id')
                                ->label('Pilih SN Mesin')
                                ->options(function () {
                                    return Machine::query()
                                        ->whereIn('status', ['Ready', 'Refurbish'])
                                        ->get()
                                        ->mapWithKeys(fn(Machine $m) => [
                                            $m->id => "[{$m->serial_number} - {$m->tipe_model}] {$m->status}",
                                        ]);
                                })
                                ->searchable()
                                ->required()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->columnSpanFull(),
                        ])
                        ->addActionLabel('Tambah Mesin')
                        ->minItems(1)
                        ->maxItems(10)
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('dari_lokasi')
                            ->label('Dari')->default('Gudang Cirebon')->disabled()->dehydrated(),
                        Forms\Components\TextInput::make('ke_lokasi')
                            ->label('Ke')->default('Gudang Bandung')->disabled()->dehydrated(),
                    ]),

                    Forms\Components\DatePicker::make('tanggal_retur')
                        ->label('Tanggal Dikirim')->default(now())->required(),

                    Forms\Components\Select::make('kondisi_saat_retur')
                        ->label('Kondisi Mesin')
                        ->options([
                            'Rusak Ringan' => 'Rusak Ringan',
                            'Rusak Berat'  => 'Rusak Berat',
                            'Cek Rutin'    => 'Cek Rutin / PM',
                        ])->required(),

                    Forms\Components\TextInput::make('dikirim_oleh')
                        ->label('Dikirim Oleh')->nullable(),

                    Forms\Components\Textarea::make('keterangan_kerusakan')
                        ->label('Keterangan Kerusakan')->columnSpanFull()->nullable(),

                ])->columns(2),

            Forms\Components\Section::make('🔧 Update Progress Servis')
                ->description('Isi setelah ada kabar dari tim Bandung.')
                ->schema([

                    Forms\Components\Select::make('status_retur')
                        ->label('Status Retur')
                        ->options([
                            'Dikirim'            => '🚚 Dikirim (Di Bandung)',
                            'Selesai Servis'     => '✅ Selesai Servis',
                            'Kembali ke Cirebon' => '🏠 Kembali ke Cirebon (Ready)',
                        ])
                        ->default('Dikirim')->required()->live(),

                    Forms\Components\DatePicker::make('tanggal_selesai_servis')
                        ->label('Tgl Selesai Servis')->nullable()
                        ->visible(fn(Forms\Get $get) => in_array($get('status_retur'), ['Selesai Servis', 'Kembali ke Cirebon'])),

                    Forms\Components\DatePicker::make('tanggal_kembali')
                        ->label('Tgl Kembali ke Cirebon')->nullable()
                        ->visible(fn(Forms\Get $get) => $get('status_retur') === 'Kembali ke Cirebon'),

                    Forms\Components\Textarea::make('hasil_servis')
                        ->label('Laporan Hasil Servis')->columnSpanFull()->nullable()
                        ->visible(fn(Forms\Get $get) => in_array($get('status_retur'), ['Selesai Servis', 'Kembali ke Cirebon'])),

                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_retur')
                    ->label('Tgl Retur')->date('d/m/Y')->sortable(),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN Mesin')->searchable()->weight('bold'),

                Tables\Columns\TextColumn::make('machine.tipe_model')
                    ->label('Tipe')->searchable(),

                Tables\Columns\TextColumn::make('machine.status')
                    ->label('Status Mesin')->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'Ready'     => 'success',
                        'Rented'    => 'warning',
                        'Returned'  => 'info',
                        'Refurbish' => 'danger',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('kondisi_saat_retur')
                    ->label('Kondisi')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Rusak Berat'  => 'danger',
                        'Rusak Ringan' => 'warning',
                        'Cek Rutin'    => 'info',
                        default        => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status_retur')
                    ->label('Progress')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Dikirim'            => 'warning',
                        'Selesai Servis'     => 'info',
                        'Kembali ke Cirebon' => 'success',
                        default              => 'gray',
                    }),

                Tables\Columns\TextColumn::make('dikirim_oleh')->label('Oleh')->toggleable(),
                Tables\Columns\TextColumn::make('tanggal_kembali')->label('Tgl Kembali')
                    ->date('d/m/Y')->placeholder('-')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_retur')->label('Status')
                    ->options([
                        'Dikirim'            => 'Dikirim',
                        'Selesai Servis'     => 'Selesai Servis',
                        'Kembali ke Cirebon' => 'Kembali ke Cirebon',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Action::make('cetak_hari_ini')
                    ->label('🖨️ Cetak Hari Ini')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(): string => route('cetak.surat-retur-tanggal', now()->toDateString()))
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Action::make('selesai_servis')
                    ->label('Selesai Servis')->icon('heroicon-o-check-circle')->color('info')
                    ->visible(fn(MachineReturn $record) => $record->status_retur === 'Dikirim')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Selesai Servis?')
                    ->modalDescription('Status retur berubah jadi Selesai Servis. Mesin masih Returned sampai dikirim balik.')
                    ->action(function (MachineReturn $record) {
                        $record->update([
                            'status_retur'           => 'Selesai Servis',
                            'tanggal_selesai_servis' => now()->toDateString(),
                        ]);
                        Notification::make()->title('Status: Selesai Servis')->success()->send();
                    }),

                Action::make('kembali_cirebon')
                    ->label('Kembali ke Cirebon')->icon('heroicon-o-home')->color('success')
                    ->visible(fn(MachineReturn $record) => $record->status_retur === 'Selesai Servis')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi: Mesin Kembali ke Cirebon?')
                    ->modalDescription('Status mesin otomatis jadi READY. Pastikan mesin sudah tiba di gudang Cirebon.')
                    ->form([
                        Forms\Components\Textarea::make('hasil_servis')
                            ->label('Laporan Hasil Servis (opsional)'),
                    ])
                    ->action(function (MachineReturn $record, array $data) {
                        $record->update([
                            'status_retur'    => 'Kembali ke Cirebon',
                            'tanggal_kembali' => now()->toDateString(),
                            'hasil_servis'    => $data['hasil_servis'] ?? $record->hasil_servis,
                        ]);
                        Notification::make()
                            ->title("Mesin {$record->machine->serial_number} sekarang READY di Cirebon!")
                            ->success()->send();
                    }),

                Action::make('cetak_per_tanggal')
                    ->label('Cetak Tanggal Ini')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn(MachineReturn $record): string =>
                        route('cetak.surat-retur-tanggal',
                            \Carbon\Carbon::parse($record->tanggal_retur)->toDateString()
                        )
                    )
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMachineReturns::route('/'),
            'create' => Pages\CreateMachineReturn::route('/create'),
            'edit'   => Pages\EditMachineReturn::route('/{record}/edit'),
        ];
    }
}