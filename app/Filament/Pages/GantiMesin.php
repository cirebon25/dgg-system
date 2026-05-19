<?php

namespace App\Filament\Pages;

use App\Models\Deployment;
use App\Models\Machine;
use App\Models\ServiceLog;
use App\Models\ServiceLogSparepart;
use App\Models\Sparepart;
use App\Models\Technician;
use App\Models\TechnicianStock;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Actions\Action; // 🌟 PENTING: Tombol Cetak Notifikasi
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
                    TextInput::make('customer_name')->label('Nama Customer')->readOnly()->extraAttributes(['class' => 'bg-gray-100']),
                    TextInput::make('old_machine_sn')->label('SN Mesin LAMA')->readOnly()->extraAttributes(['class' => 'bg-gray-100']),
                    TextInput::make('counter_bw_final')->label('Counter BW Akhir')->numeric()->required(),
                    TextInput::make('counter_color_final')->label('Counter Color Akhir')->numeric()->required(),
                    Hidden::make('old_machine_id'),
                ])->columns(3),

            Section::make('2. Unit Pengganti & Pelaksana')
                ->schema([
                    Select::make('new_machine_id')
                        ->label('Pilih SN Mesin BARU')
                        ->options(Machine::where('status', 'Ready')->pluck('serial_number', 'id'))
                        ->searchable()->required(),
                    Select::make('technician_id')
                        ->label('Teknisi Pelaksana')
                        ->options(Technician::pluck('nama_technician', 'id'))
                        ->searchable()->required()->live(),
                    DatePicker::make('tanggal')->label('Tanggal Rolling')->default(now())->required(),
                    TextInput::make('keterangan')->label('Alasan Rolling')->required(),
                ])->columns(4),

            Section::make('3. Sparepart / Kelengkapan Unit Baru')
                ->schema([
                    Repeater::make('spareparts')
                        ->label('Daftar Sparepart')
                        ->schema([
                            Select::make('sparepart_id')
                                ->label('Item/Part')
                                ->options(Sparepart::pluck('nama_sparepart', 'id'))
                                ->searchable()->required()->live(),
                            TextInput::make('jumlah')
                                ->label('Qty')
                                ->numeric()->default(1)->required()
                                ->live(onBlur: true)
                                ->rules([
                                    fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $techId = $get('../../technician_id');
                                        $partId = $get('sparepart_id');
                                        if (!$techId || !$partId) return;

                                        $stock = TechnicianStock::where('technician_id', $techId)
                                            ->where('sparepart_id', $partId)->first();
                                        $sisa = $stock ? $stock->jumlah : 0;

                                        if ((int)$value > (int)$sisa) {
                                            $fail("❌ STOK TAS TIDAK CUKUP! Sisa: {$sisa}");
                                        }
                                    },
                                ]),
                            TextInput::make('ket_part')->label('Keterangan'),
                        ])->columns(3)->createItemButtonLabel('Tambah Sparepart +'),
                ]),
        ])->statePath('data');
    }

    public function submit()
    {
        $input = $this->form->getState();

        DB::transaction(function () use ($input) {
            $deployment = Deployment::find($input['deployment_id']);
            $oldMachine = Machine::find($input['old_machine_id']);
            $newMachine = Machine::find($input['new_machine_id']);

            // --- 1. UPDATE STATUS MESIN ---
            $oldMachine->update(['status' => 'Refurbish', 'customer_id' => null, 'technician_id' => null]);
            $newMachine->update(['status' => 'Rented', 'customer_id' => $deployment->customer_id, 'technician_id' => $input['technician_id']]);
            $deployment->update(['machine_id' => $newMachine->id]);

            // --- 2. LOG REKAP PUSAT SWAP ---
            DB::table('machine_replacements')->insert([
                'customer_id'    => $deployment->customer_id,
                'old_machine_id' => $oldMachine->id,
                'new_machine_id' => $newMachine->id,
                'technician_id'  => $input['technician_id'],
                'tanggal'        => $input['tanggal'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // --- 3. LOG PENARIKAN (MESIN LAMA) ---
            ServiceLog::create([
                'machine_id' => $oldMachine->id,
                'customer_id' => $deployment->customer_id,
                'technician_id' => $input['technician_id'],
                'tanggal' => $input['tanggal'],
                'tipe_kunjungan' => 'RR',
                'counter_bw' => $input['counter_bw_final'],
                'counter_color' => $input['counter_color_final'],
                'kerusakan' => 'ROLLING OUT',
                'perbaikan' => $input['keterangan'], // Kunci Alasan Ganti
            ]);

            // --- 4. LOG PEMASANGAN (MESIN BARU) ---
            $logBaru = ServiceLog::create([
                'machine_id' => $newMachine->id,
                'customer_id' => $deployment->customer_id,
                'technician_id' => $input['technician_id'],
                'tanggal' => $input['tanggal'],
                'tipe_kunjungan' => 'RR',
                'counter_bw' => 0, 
                'counter_color' => 0,
                'kerusakan' => 'ROLLING IN',
                'perbaikan' => "Unit Pengganti dari SN: {$oldMachine->serial_number}",
            ]);

            // --- 5. SIMPAN SPAREPART KELENGKAPAN ---
            if (!empty($input['spareparts'])) {
                foreach ($input['spareparts'] as $item) {
                    ServiceLogSparepart::create([
                        'service_log_id' => $logBaru->id,
                        'sparepart_id' => $item['sparepart_id'],
                        'jumlah' => $item['jumlah'],
                        'keterangan' => $item['ket_part'] ?? 'Kelengkapan RR',
                    ]);
                }
            }
        });

        $this->form->fill();

        // 🌟 NOTIFIKASI SAKTI: TOMBOL PRINT LANGSUNG AKTIF KEMBALI 🌟
        Notification::make()
            ->title('Rolling Berhasil!')
            ->success()
            ->persistent()
            ->body('Data sinkron. Silakan langsung cetak Berita Acara melalui tombol di bawah ini:')
            ->actions([
                Action::make('print')
                    ->label('🖨️ Langsung Cetak Swap')
                    ->button()
                    ->color('warning')
                    ->url(route('cetak.swap'), shouldOpenInNewTab: true),
            ])
            ->send();
    }
}