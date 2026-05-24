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
                ->schema([
                    Select::make('deployment_id')
                        ->label('Pilih Pemasangan Aktif')
                        ->options(
                            Deployment::with(['customer', 'machine'])
                                ->get()
                                ->mapWithKeys(fn($dep) => [
                                    $dep->id => "{$dep->customer?->nama_customer} (SN: {$dep->machine?->serial_number})",
                                ])
                        )
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
                        ->options(Machine::where('status', 'Ready')->pluck('serial_number', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('technician_id')
                        ->label('Teknisi Pelaksana')
                        ->options(Technician::pluck('nama_technician', 'id'))
                        ->searchable()
                        ->required()
                        ->live(),
                    DatePicker::make('tanggal')
                        ->label('Tanggal Rolling')
                        ->default(now())
                        ->required(),
                    TextInput::make('keterangan')
                        ->label('Alasan Rolling')
                        ->required(),
                ])->columns(4),

            Section::make('3. Sparepart / Kelengkapan Unit Baru')
                ->schema([
                    Repeater::make('spareparts')
                        ->label('Daftar Sparepart')
                        ->schema([
                            Select::make('sparepart_id')
                                ->label('Item/Part')
                                ->options(
                                    Sparepart::all()
                                        ->mapWithKeys(fn($s) => [
                                            $s->id => $s->nama_sparepart . ($s->nama_alias ? " — {$s->nama_alias}" : '')
                                        ])
                                        ->toArray()
                                )
                                ->searchable()
                                ->required()
                                ->live(),
                            TextInput::make('jumlah')
                                ->label('Qty')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->live(onBlur: true)
                                ->rules([
                                    fn(Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
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
}
