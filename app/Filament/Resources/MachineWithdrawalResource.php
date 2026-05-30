<?php

// app/Filament/Resources/MachineWithdrawalResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\MachineWithdrawalResource\Pages;
use App\Models\MachineWithdrawal;
use App\Models\Machine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class MachineWithdrawalResource extends Resource
{
    protected static ?string $model = MachineWithdrawal::class;
    protected static ?string $navigationLabel = 'Penarikan Mesin';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Penarikan Unit Mesin')
                    ->description('Pilihan SN Mesin di bawah ini dibatasi otomatis hanya untuk unit yang berstatus Rented / Aktif di lapangan.')
                    ->schema([

                        Forms\Components\Select::make('machine_id')
                            ->label('Pilih SN Mesin')
                            ->options(function () {
                                return Machine::query()
                                    ->where('status', 'Rented')
                                    ->with('customer')
                                    ->get()
                                    ->mapWithKeys(function (Machine $machine) {
                                        $sn       = $machine->serial_number;
                                        $model    = $machine->tipe_model ?? '-';
                                        $customer = $machine->customer?->nama_customer ?? 'Belum Terikat Customer';

                                        return [
                                            $machine->id => "[{$sn} - {$model}] 👤 {$customer}",
                                        ];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (! $state) return;
                                $machine = Machine::find($state);
                                if ($machine) {
                                    $set('customer_id', $machine->customer_id);
                                }
                            }),

                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'nama_customer')
                            ->label('Nama Customer / Instansi')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\DatePicker::make('tanggal_tarik')
                            ->label('Tanggal Penarikan')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('kondisi_akhir')
                            ->label('Kondisi Akhir Unit')
                            ->options([
                                'Baik'         => 'Baik / Ready Gudang',
                                'Rusak Ringan' => 'Rusak Ringan (Butuh Servis Gudang)',
                                'Rusak Berat'  => 'Rusak Berat (Butuh Ganti Part Total)',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('alasan_penarikan')
                            ->label('Alasan Penarikan Unit')
                            ->placeholder('Contoh: Kontrak sewa di instansi terkait telah habis.')
                            ->required()
                            ->columnSpanFull(),

                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_tarik')
                    ->label('Tgl Tarik')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN Mesin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Ex-Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('kondisi_akhir')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Baik'         => 'success',
                        'Rusak Ringan' => 'warning',
                        'Rusak Berat'  => 'danger',
                        default        => 'gray',
                    }),

                Tables\Columns\TextColumn::make('alasan_penarikan')
                    ->label('Alasan')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->alasan_penarikan),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            // ->headerActions([
            //     Action::make('rekap_bulanan')
            //         ->label('Rekap Bulanan')
            //         ->icon('heroicon-o-document-chart-bar')
            //         ->color('success')
            //         ->form([
            //             Forms\Components\Select::make('month')
            //                 ->label('Bulan')
            //                 ->options([
            //                     '01' => 'Januari',
            //                     '02' => 'Februari',
            //                     '03' => 'Maret',
            //                     '04' => 'April',
            //                     '05' => 'Mei',
            //                     '06' => 'Juni',
            //                     '07' => 'Juli',
            //                     '08' => 'Agustus',
            //                     '09' => 'September',
            //                     '10' => 'Oktober',
            //                     '11' => 'November',
            //                     '12' => 'Desember',
            //                 ])
            //                 ->default(now()->format('m'))
            //                 ->required(),
            //             Forms\Components\Select::make('year')
            //                 ->label('Tahun')
            //                 ->options(
            //                     collect(range(now()->year, 2024))
            //                         ->mapWithKeys(fn($y) => [$y => $y])
            //                 )
            //                 ->default((string) now()->year)
            //                 ->required(),
            //         ])
            //         ->action(function (array $data) {
            //             $url = route('withdrawal.rekap', [
            //                 'month' => $data['month'],
            //                 'year'  => $data['year'],
            //             ]);
            //             // Buka di tab baru via JS
            //             $escaped = e($url);
            //             return response("<script>window.open('{$escaped}','_blank');</script>");
            //         })
            //         ->modalSubmitActionLabel('Cetak')
            //         ->modalHeading('Rekap Bulanan Penarikan'),
            // ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('cetak')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn(MachineWithdrawal $record): string => route('withdrawal.rekap', $record->id))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMachineWithdrawals::route('/'),
            'create' => Pages\CreateMachineWithdrawal::route('/create'),
            'edit'   => Pages\EditMachineWithdrawal::route('/{record}/edit'),
        ];
    }
}
