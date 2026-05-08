<?php

namespace App\Filament\Pages;

use App\Models\Machine;
use App\Models\Deployment;
use App\Models\ServiceLog;
use App\Models\Technician;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class GantiMesin extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Ganti Mesin (Rolling)';
    protected static ?string $title = 'Proses Ganti Mesin (RR)';
    protected static ?string $navigationGroup = 'Transaksi';

    // Menghubungkan ke file blade
    protected static string $view = 'filament.pages.ganti-mesin'; 

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pilih Unit yang Akan Diganti')
                    ->description('Cari berdasarkan Nama Customer atau SN Mesin yang terpasang.')
                    ->schema([
                        Select::make('deployment_id')
                            ->label('Pilih Pemasangan Aktif')
                            ->options(
                                Deployment::with(['customer', 'machine'])
                                    ->get()
                                    ->mapWithKeys(function ($dep) {
                                        return [$dep->id => "{$dep->customer->nama_customer} - (SN: {$dep->machine->serial_number})"];
                                    })
                            )
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                // INI BAGIAN PENTING: Mengisi data otomatis saat dipilih
                                $dep = Deployment::with(['customer', 'machine'])->find($state);
                                if ($dep) {
                                    $set('customer_name', $dep->customer->nama_customer); // Isi Nama Customer
                                    $set('old_machine_sn', $dep->machine->serial_number); // Isi SN Lama
                                    $set('no_kontrak', $dep->no_kontrak); // Isi No Kontrak
                                }
                            }),
                        
                        // Field yang terisi otomatis
                        TextInput::make('customer_name')
                            ->label('Nama Customer')
                            ->readOnly()
                            ->extraAttributes(['class' => 'bg-gray-100 font-bold']),
                        
                        TextInput::make('old_machine_sn')
                            ->label('SN Mesin Lama')
                            ->readOnly()
                            ->extraAttributes(['class' => 'bg-gray-100']),
                        
                        TextInput::make('no_kontrak')
                            ->label('No. Kontrak')
                            ->readOnly()
                            ->extraAttributes(['class' => 'bg-gray-100']),
                    ])->columns(2),

                Section::make('Informasi Penarikan & Counter Terakhir')
                    ->schema([
                        TextInput::make('counter_bw_final')->label('Counter BW Terakhir (Unit Lama)')->numeric()->required(),
                        TextInput::make('counter_color_final')->label('Counter Color Terakhir (Unit Lama)')->numeric()->required(),
                        DatePicker::make('tanggal')->label('Tanggal Tukar Guling')->default(now())->required(),
                        Select::make('technician_id')
                            ->label('Teknisi Pelaksana')
                            ->options(Technician::pluck('nama_technician', 'id'))
                            ->searchable()
                            ->required(),
                    ])->columns(4),

                Section::make('Pilih Mesin Pengganti')
                    ->description('Hanya mesin STATUS READY yang muncul.')
                    ->schema([
                        Select::make('new_machine_id')
                            ->label('Pilih Mesin Baru')
                            ->options(Machine::where('status', 'Ready')->pluck('serial_number', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('keterangan')
                            ->label('Alasan Ganti Mesin')
                            ->placeholder('Contoh: Unit lama sering SC542 atau Rolling Unit'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $input = $this->form->getState();

        DB::transaction(function () use ($input) {
            $deployment = Deployment::find($input['deployment_id']);
            $oldMachine = $deployment->machine;
            $newMachine = Machine::find($input['new_machine_id']);

            // 1. Buat Service Log RR otomatis
            ServiceLog::create([
                'machine_id' => $oldMachine->id,
                'technician_id' => $input['technician_id'],
                'tanggal' => $input['tanggal'],
                'tipe_kunjungan' => 'RR',
                'counter_bw' => $input['counter_bw_final'],
                'counter_color' => $input['counter_color_final'],
                'kerusakan' => 'Mesin ditarik (Rolling)',
                'perbaikan' => 'Ganti unit ke SN: ' . $newMachine->serial_number . '. Ket: ' . $input['keterangan'],
            ]);

            // 2. OTOMATIS JADI PERBAIKAN (Refurbish) untuk mesin lama
            $oldMachine->update(['status' => 'Refurbish']);

            // 3. Tukar Mesin di Pemasangan
            $deployment->update(['machine_id' => $newMachine->id]);

            // 4. Update Mesin Baru jadi Rented
            $newMachine->update(['status' => 'Rented']);
        });

        $this->form->fill();

        Notification::make()
            ->title('Berhasil Ganti Mesin!')
            ->success()
            ->body('Mesin lama ditarik (Status: Perbaikan), mesin baru sudah aktif.')
            ->send();
    }
}