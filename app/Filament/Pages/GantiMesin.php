<?php

namespace App\Filament\Pages;

use App\Models\Deployment;
use App\Models\Machine;
use App\Models\ServiceLog;
use App\Models\ServiceLogSparepart;
use App\Models\Sparepart;
use App\Models\Technician;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class GantiMesin extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Ganti Mesin (Rolling)';

    protected static ?string $title = 'Proses Rolling Unit DGG';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static string $view = 'filament.pages.ganti-mesin';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('1. Data Penarikan & Customer')
                ->description('Pilih unit yang akan ditarik dari customer.')
                ->schema([
                    Select::make('deployment_id')
                        ->label('Pilih Pemasangan Aktif')
                        ->options(
                            Deployment::with(['customer', 'machine'])
                                ->get()
                                ->mapWithKeys(fn ($dep) => [
                                    $dep->id => "{$dep->customer->nama_customer} (SN: {$dep->machine->serial_number})",
                                ])
                        )
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            $dep = Deployment::with(['customer', 'machine'])->find($state);
                            if ($dep) {
                                $set('customer_name', $dep->customer->nama_customer);
                                $set('old_machine_id', $dep->machine->id);
                                $set('old_machine_sn', $dep->machine->serial_number);
                            }
                        }),
                    TextInput::make('customer_name')
                        ->label('Nama Customer')
                        ->readOnly()
                        ->extraAttributes(['class' => 'bg-gray-100 font-bold']),
                    TextInput::make('old_machine_sn')
                        ->label('SN Mesin LAMA')
                        ->readOnly()
                        ->extraAttributes(['class' => 'bg-gray-100']),

                    TextInput::make('counter_bw_final')
                        ->label('Counter BW Akhir (Unit Lama)')
                        ->numeric()
                        ->required(),
                    TextInput::make('counter_color_final')
                        ->label('Counter Color Akhir (Unit Lama)')
                        ->numeric()
                        ->required(),

                    Hidden::make('old_machine_id'),
                ])->columns(3),

            Section::make('2. Unit Pengganti & Pelaksana')
                ->schema([
                    Select::make('new_machine_id')
                        ->label('Pilih SN Mesin BARU')
                        ->options(Machine::where('status', 'Ready')->pluck('serial_number', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('technician_id')
                        ->label('Teknisi Pelaksana')
                        ->options(Technician::pluck('nama_technician', 'id'))
                        ->searchable()
                        ->required(),
                    DatePicker::make('tanggal')
                        ->label('Tanggal Rolling')
                        ->default(now())
                        ->required(),
                    TextInput::make('keterangan')
                        ->label('Alasan Rolling')
                        ->placeholder('Contoh: Unit sering SC542')
                        ->required(),
                ])->columns(4),

            Section::make('3. Sparepart / Kelengkapan Unit Baru')
                ->description('Input sparepart atau toner yang disertakan pada mesin baru.')
                ->schema([
                    Repeater::make('spareparts')
                        ->label('Daftar Sparepart')
                        ->schema([
                            Select::make('sparepart_id')
                                ->label('Item/Part')
                                ->options(Sparepart::pluck('nama_sparepart', 'id'))
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, $set) => $set('nama_part', Sparepart::find($state)?->nama_sparepart)
                                ),
                            TextInput::make('jumlah')
                                ->label('Qty')
                                ->numeric()
                                ->default(1)
                                ->required(),
                            TextInput::make('ket_part')
                                ->label('Keterangan'),
                            Hidden::make('nama_part'),
                        ])
                        ->columns(3)
                        ->createItemButtonLabel('Tambah Sparepart +')
                        ->defaultItems(0),
                ]),
        ])->statePath('data');
    }

    public function submit()
    {
        $input = $this->form->getState();

        DB::transaction(function () use ($input) {
            $deployment = Deployment::with('customer')->find($input['deployment_id']);
            $oldMachine = Machine::find($input['old_machine_id']);
            $newMachine = Machine::find($input['new_machine_id']);

            // 1. Log Mesin Lama (ROLLING OUT)
            ServiceLog::create([
                'machine_id' => $oldMachine->id,
                'technician_id' => $input['technician_id'],
                'tanggal' => $input['tanggal'],
                'tipe_kunjungan' => 'RR',
                'counter_bw' => $input['counter_bw_final'],
                'counter_color' => $input['counter_color_final'],
                'kerusakan' => 'ROLLING OUT',
                'perbaikan' => "Unit ditarik. Pengganti SN: {$newMachine->serial_number}. Alasan: {$input['keterangan']}",
            ]);

            // 2. Log Mesin Baru (ROLLING IN)
            $logBaru = ServiceLog::create([
                'machine_id' => $newMachine->id,
                'technician_id' => $input['technician_id'],
                'tanggal' => $input['tanggal'],
                'tipe_kunjungan' => 'RR',
                'counter_bw' => 0,
                'counter_color' => 0,
                'kerusakan' => 'ROLLING IN',
                'perbaikan' => "Unit masuk menggantikan SN: {$oldMachine->serial_number}",
            ]);

            // 3. Simpan Sparepart ke Database
            if (! empty($input['spareparts'])) {
                foreach ($input['spareparts'] as $item) {
                    ServiceLogSparepart::create([
                        'service_log_id' => $logBaru->id,
                        'sparepart_id' => $item['sparepart_id'],
                        'jumlah' => $item['jumlah'],
                        'keterangan' => $item['ket_part'] ?? '-',
                    ]);
                }
            }

            // 4. Update Status Mesin & Pemasangan
            $oldMachine->update(['status' => 'Refurbish']);
            $deployment->update(['machine_id' => $newMachine->id]);
            $newMachine->update(['status' => 'Rented']);

            // 5. Masukkan ke Session untuk Keperluan Cetak
            session()->put('sj_data', [
                'old_sn' => $oldMachine->serial_number,
                'old_model' => $oldMachine->tipe_model, // Tipe Mesin Lama
                'new_sn' => $newMachine->serial_number,
                'new_model' => $newMachine->tipe_model, // Tipe Mesin Baru
                'cust' => $deployment->customer->nama_customer,
                'alamat' => $deployment->customer->alamat ?? '-',
                'bw' => $input['counter_bw_final'],
                'cl' => $input['counter_color_final'],
                'parts' => $input['spareparts'] ?? [],
                'keterangan' => $input['keterangan'],
            ]);
        });

        $this->form->fill();

        // NOTIFIKASI DENGAN TOMBOL CETAK
        Notification::make()
            ->title('Rolling Berhasil!')
            ->success()
            ->persistent()
            ->body('Data sinkron. Mesin lama otomatis berstatus PERBAIKAN.')
            ->actions([
                Action::make('print_sj')
                    ->label('🖨️ CETAK SURAT JALAN')
                    ->button()
                    ->url(route('cetak.sj-rolling'))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }
}
