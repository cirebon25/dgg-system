<?php

namespace App\Filament\Pages;

use App\Models\Deployment;
use App\Models\Machine;
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
use Filament\Notifications\Actions\Action as NotifAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GantiMesin extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Ganti Mesin (Rolling)';
    protected static ?string $title           = 'Proses Rolling Unit DGG';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static string  $view            = 'filament.pages.ganti-mesin';

    public ?array $data = [];
    public array $riwayat = [];

    public function mount(): void
    {
        $this->form->fill();
        $this->loadRiwayat();
    }

    public function loadRiwayat(): void
    {
        $this->riwayat = DB::table('machine_replacements')
            ->leftJoin('customers', 'machine_replacements.customer_id', '=', 'customers.id')
            ->leftJoin('machines as m_old', 'machine_replacements.old_machine_id', '=', 'm_old.id')
            ->leftJoin('machines as m_new', 'machine_replacements.new_machine_id', '=', 'm_new.id')
            ->leftJoin('technicians', 'machine_replacements.technician_id', '=', 'technicians.id')
            ->select([
                'machine_replacements.id',
                'machine_replacements.tanggal',
                'machine_replacements.keterangan',
                'machine_replacements.counter_bw_final',
                'machine_replacements.counter_color_final',
                'customers.nama_customer',
                'm_old.serial_number as sn_lama',
                'm_new.serial_number as sn_baru',
                'technicians.nama_technician',
            ])
            ->orderBy('machine_replacements.created_at', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            Section::make('1. Data Penarikan & Customer')
                ->schema([
                    Select::make('deployment_id')
                        ->label('Pilih Pemasangan Aktif')
                        ->options(function () {
                            return Cache::remember('deployment_options', 60, function () {
                                return Deployment::with(['customer', 'machine'])
                                    ->get()
                                    ->mapWithKeys(fn($dep) => [
                                        $dep->id => "{$dep->customer?->nama_customer} (SN: {$dep->machine?->serial_number})",
                                    ]);
                            });
                        })
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            $dep = Deployment::with(['customer', 'machine'])->find($state);
                            if ($dep) {
                                $set('customer_name', $dep->customer?->nama_customer);
                                $set('old_machine_id', $dep->machine?->id);
                                $set('old_machine_sn', $dep->machine?->serial_number);
                            }
                        }),

                    TextInput::make('customer_name')
                        ->label('Nama Customer')
                        ->readOnly()
                        ->extraAttributes(['class' => 'bg-gray-100']),

                    TextInput::make('old_machine_sn')
                        ->label('SN Mesin LAMA')
                        ->readOnly()
                        ->extraAttributes(['class' => 'bg-gray-100']),

                    TextInput::make('counter_bw_final')
                        ->label('Counter BW Akhir')
                        ->numeric()
                        ->required(),

                    TextInput::make('counter_color_final')
                        ->label('Counter Color Akhir')
                        ->numeric()
                        ->required(),

                    Hidden::make('old_machine_id'),
                ])->columns(3),

            Section::make('2. Unit Pengganti & Pelaksana')
                ->schema([
                    Select::make('new_machine_id')
                        ->label('Pilih SN Mesin BARU')
                        ->options(function () {
                            return Cache::remember('ready_machine_options', 60, function () {
                                return Machine::where('status', 'Ready')->pluck('serial_number', 'id');
                            });
                        })
                        ->searchable()
                        ->required(),

                    Select::make('technician_id')
                        ->label('Teknisi Pelaksana')
                        ->options(
                            Cache::remember('technician_options', 300, function () {
                                return Technician::pluck('nama_technician', 'id');
                            })
                        )
                        ->searchable()
                        ->required(),

                    DatePicker::make('tanggal')
                        ->label('Tanggal Rolling')
                        ->default(now())
                        ->required(),

                    TextInput::make('keterangan')
                        ->label('Alasan Rolling')
                        ->required(),

                    TextInput::make('counter_bw_awal')
                        ->label('Counter BW Awal (Mesin Baru)')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    TextInput::make('counter_color_awal')
                        ->label('Counter Color Awal (Mesin Baru)')
                        ->numeric()
                        ->default(0)
                        ->required(),
                ])->columns(4),

            Section::make('3. Sparepart / Kelengkapan (Potong Stok Gudang)')
                ->schema([
                    Repeater::make('spareparts')
                        ->label('Daftar Sparepart')
                        ->schema([
                            Select::make('sparepart_id')
                                ->label('Item/Part')
                                ->options(function () {
                                    return Cache::remember('sparepart_options', 120, function () {
                                        return Sparepart::all()->mapWithKeys(fn($s) => [
                                            $s->id => $s->nama_sparepart . ($s->nama_alias ? " — {$s->nama_alias}" : '') . " [Stok: {$s->stok}]",
                                        ])->toArray();
                                    });
                                })
                                ->searchable()
                                ->required(),

                            TextInput::make('jumlah')
                                ->label('Qty')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->live(onBlur: true)
                                ->rules([
                                    fn(Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $partId = $get('sparepart_id');
                                        if (! $partId) return;

                                        $sparepart = Sparepart::find($partId);
                                        $stok = $sparepart?->stok ?? 0;

                                        if ((int) $value > (int) $stok) {
                                            $fail("❌ STOK GUDANG TIDAK CUKUP! Sisa: {$stok}");
                                        }
                                    },
                                ]),

                            TextInput::make('ket_part')->label('Keterangan'),
                        ])
                        ->columns(3)
                        ->createItemButtonLabel('+ Tambah Sparepart'),
                ]),

        ])->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Simpan id-id penting di luar transaction agar bisa diakses setelahnya
        $newDeploymentId = null;
        $replacementId    = null;

        DB::transaction(function () use ($data, &$newDeploymentId, &$replacementId) {
            $dep        = Deployment::with(['customer', 'machine'])->findOrFail($data['deployment_id']);
            $oldMachine = Machine::findOrFail($data['old_machine_id']);
            $newMachine = Machine::findOrFail($data['new_machine_id']);
            $customer   = $dep->customer;

            // 1. Catat ke tabel machine_replacements (insertGetId supaya id-nya bisa dipakai untuk link cetak)
            $replacementId = DB::table('machine_replacements')->insertGetId([
                'customer_id'         => $customer->id,
                'old_machine_id'      => $oldMachine->id,
                'new_machine_id'      => $newMachine->id,
                'technician_id'       => $data['technician_id'],
                'tanggal'             => $data['tanggal'],
                'keterangan'          => $data['keterangan'],
                'counter_bw_final'    => $data['counter_bw_final'],
                'counter_color_final' => $data['counter_color_final'],
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            // 2. Mesin LAMA → Ready, lepas customer
            $oldMachine->update([
                'status'      => 'Ready',
                'customer_id' => null,
            ]);

            // 3. Mesin BARU → Rented, pasang customer
            $newMachine->update([
                'status'      => 'Rented',
                'customer_id' => $customer->id,
            ]);

            // 4. Soft-delete deployment lama, buat deployment baru
            $dep->delete();

            $newDeploymentId = DB::table('deployments')->insertGetId([
                'machine_id'     => $newMachine->id,
                'customer_id'    => $customer->id,
                'technician_id'  => $data['technician_id'],
                'tanggal_instal' => $data['tanggal'],
                'counter_bw'     => $data['counter_bw_awal'] ?? 0,
                'counter_color'  => $data['counter_color_awal'] ?? 0,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // 4b. Simpan deployment_id baru ke record machine_replacements, supaya sparepart yang dipakai bisa ditarik saat cetak Surat Jalan
            DB::table('machine_replacements')
                ->where('id', $replacementId)
                ->update(['deployment_id' => $newDeploymentId]);

            // 5. Potong stok GUDANG & catat ke deployment_sparepart
            foreach (($data['spareparts'] ?? []) as $part) {
                if (empty($part['sparepart_id'])) continue;

                // Potong stok gudang
                Sparepart::where('id', $part['sparepart_id'])
                    ->decrement('stok', (int) $part['jumlah']);

                // Catat ke deployment_sparepart
                DB::table('deployment_sparepart')->insert([
                    'deployment_id' => $newDeploymentId,
                    'sparepart_id'  => $part['sparepart_id'],
                    'jumlah'        => $part['jumlah'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        });

        // 6. Ambil data ringkas untuk isi notifikasi (link cetak sekarang cukup pakai $replacementId)
        $dep = DB::table('deployments')
            ->where('deployments.id', $newDeploymentId)
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->join('machines', 'deployments.machine_id', '=', 'machines.id')
            ->first(['customers.nama_customer', 'customers.alamat', 'machines.serial_number as new_sn']);

        $rep = DB::table('machine_replacements')
            ->where('machine_replacements.id', $replacementId)
            ->join('machines', 'machine_replacements.old_machine_id', '=', 'machines.id')
            ->first(['machines.serial_number as old_sn', 'machine_replacements.counter_bw_final as bw', 'machine_replacements.counter_color_final as cl']);

        // Link cetak sekarang cukup pakai id record MachineReplacement, data lengkap diambil live dari relasi saat dicetak
        $urlSj = route('cetak.sj-rolling', $replacementId);

        // 7. Notifikasi sukses + tombol cetak SJ
        Notification::make()
            ->title('✅ Rolling Berhasil!')
            ->body("Mesin {$rep?->old_sn} → {$dep?->new_sn} untuk {$dep?->nama_customer}")
            ->success()
            ->duration(10000)
            ->actions([
                NotifAction::make('cetak_sj')
                    ->label('🖨️ Cetak Surat Jalan')
                    ->url($urlSj, shouldOpenInNewTab: true)
                    ->button(),
            ])
            ->send();

        // 8. Clear cache & reset form
        Cache::forget('deployment_options');
        Cache::forget('ready_machine_options');
        Cache::forget('sparepart_options');

        $this->form->fill();
        $this->loadRiwayat();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(['admin', 'manager']) ?? false;
    }
}
