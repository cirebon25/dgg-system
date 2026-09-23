<?php

namespace App\Filament\Pages;

use App\Models\MachineReplacement;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class EditMachineReplacement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Edit Rolling';
    protected static ?string $title           = 'Edit Riwayat Rolling Unit';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static string $view             = 'filament.pages.edit-machine-replacement';

    public ?int $id = null;
    public ?MachineReplacement $replacement = null;
    public ?array $data = [];

    public function mount(): void
    {
        $id = request()->query('id') ?? $this->id;

        if (!$id) {
            Notification::make()
                ->title('❌ Error')
                ->body('ID Rolling tidak ditemukan pada URL.')
                ->danger()
                ->send();

            $this->redirect(route('filament.admin.pages.ganti-mesin'), navigate: true);
            return;
        }

        try {
            $this->replacement = MachineReplacement::with([
                'customer',
                'oldMachine',
                'newMachine',
                'technician',
            ])->findOrFail($id);

            $this->id = $this->replacement->id;

            $this->form->fill([
                'tanggal'             => $this->replacement->tanggal,
                'keterangan'          => $this->replacement->keterangan,
                'counter_bw_final'    => $this->replacement->counter_bw_final,
                'counter_color_final' => $this->replacement->counter_color_final,
                'counter_bw_awal'     => $this->replacement->newMachine?->counter_bw ?? 0,
                'counter_color_awal'  => $this->replacement->newMachine?->counter_color ?? 0,
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ Error: ' . $e->getMessage())
                ->danger()
                ->send();

            $this->redirect(route('filament.admin.pages.ganti-mesin'));
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('📋 MESIN YANG DITARIK (LAMA)')
                ->description('Data mesin yang ditarik dari customer')
                ->collapsible(false)
                ->icon('heroicon-o-arrow-uturn-left')
                ->schema([
                    TextInput::make('old_machine_sn')
                        ->label('Serial Number Mesin Lama')
                        ->default($this->replacement?->oldMachine?->serial_number ?? '-')
                        ->readOnly()
                        ->disabled()
                        ->helperText('Mesin ini sudah ditarik dari customer'),

                    TextInput::make('customer_name')
                        ->label('Customer Tempat Penarikan')
                        ->default($this->replacement?->customer?->nama_customer ?? '-')
                        ->readOnly()
                        ->disabled(),

                    TextInput::make('technician_name')
                        ->label('Teknisi Pelaksana')
                        ->default($this->replacement?->technician?->nama_technician ?? '-')
                        ->readOnly()
                        ->disabled(),
                ])->columns(2),

            Section::make('📋 MESIN PENGGANTI (BARU)')
                ->description('Data mesin pengganti yang dipasang')
                ->collapsible(false)
                ->icon('heroicon-o-arrow-uturn-right')
                ->schema([
                    TextInput::make('new_machine_sn')
                        ->label('Serial Number Mesin Baru')
                        ->default($this->replacement?->newMachine?->serial_number ?? '-')
                        ->readOnly()
                        ->disabled()
                        ->helperText('Mesin ini sudah dipasang di customer'),

                    TextInput::make('counter_bw_awal')
                        ->label('📊 Counter BW Awal (Mesin Baru)')
                        ->numeric()
                        ->inputMode('decimal')
                        ->helperText('Counter BW saat mesin baru dipasang')
                        ->default($this->replacement?->newMachine?->counter_bw ?? 0),

                    TextInput::make('counter_color_awal')
                        ->label('📊 Counter Color Awal (Mesin Baru)')
                        ->numeric()
                        ->inputMode('decimal')
                        ->helperText('Counter Color saat mesin baru dipasang')
                        ->default($this->replacement?->newMachine?->counter_color ?? 0),
                ])->columns(2),

            Section::make('⚙️ COUNTER MESIN LAMA (EDITABLE)')
                ->description('Counter mesin saat ditarik dari customer')
                ->collapsible(false)
                ->icon('heroicon-o-pencil')
                ->schema([
                    TextInput::make('counter_bw_final')
                        ->label('📊 Counter BW Akhir')
                        ->numeric()
                        ->required()
                        ->inputMode('decimal')
                        ->helperText('Counter BW saat mesin ditarik dari customer'),

                    TextInput::make('counter_color_final')
                        ->label('📊 Counter Color Akhir')
                        ->numeric()
                        ->required()
                        ->inputMode('decimal')
                        ->helperText('Counter Color saat mesin ditarik dari customer'),
                ])->columns(2),

            Section::make('📅 TANGGAL & KETERANGAN (EDITABLE)')
                ->description('Data rolling')
                ->collapsible(false)
                ->icon('heroicon-o-calendar')
                ->schema([
                    DatePicker::make('tanggal')
                        ->label('Tanggal Rolling')
                        ->required()
                        ->native(false),

                    TextInput::make('keterangan')
                        ->label('Keterangan/Alasan Rolling')
                        ->required()
                        ->columnSpanFull(),
                ])->columns(2),

        ])->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            \Log::info('EditMachineReplacement - Data yang dikirim:', $data);
            \Log::info('EditMachineReplacement - Replacement ID:', ['id' => $this->replacement->id]);

            DB::transaction(function () use ($data) {
                if (!$this->replacement) {
                    throw new \Exception('Replacement data tidak ditemukan');
                }

                $updated_mr = $this->replacement->update([
                    'tanggal'             => $data['tanggal'],
                    'keterangan'          => $data['keterangan'],
                    'counter_bw_final'    => $data['counter_bw_final'],
                    'counter_color_final' => $data['counter_color_final'],
                ]);
                \Log::info('Machine Replacement updated:', ['result' => $updated_mr]);

                if (!$this->replacement->oldMachine) {
                    throw new \Exception('Mesin lama tidak ditemukan');
                }
                $this->replacement->oldMachine->update([
                    'counter_bw'     => $data['counter_bw_final'],
                    'counter_color'  => $data['counter_color_final'],
                    'last_rolled_at' => $data['tanggal'],
                ]);
                \Log::info('Old Machine updated');

                if (!$this->replacement->newMachine) {
                    throw new \Exception('Mesin baru tidak ditemukan');
                }
                $this->replacement->newMachine->update([
                    'counter_bw'     => $data['counter_bw_awal'] ?? 0,
                    'counter_color'  => $data['counter_color_awal'] ?? 0,
                    'last_rolled_at' => $data['tanggal'],
                ]);
                \Log::info('New Machine updated');
            });

            $this->replacement->refresh();

            Notification::make()
                ->title('✅ Data Rolling Berhasil Diperbarui!')
                ->body("Mesin Lama: {$this->replacement->oldMachine->serial_number}\nMesin Baru: {$this->replacement->newMachine->serial_number}")
                ->success()
                ->duration(5000)
                ->send();

            session()->flash('success', true);
            $this->redirect(route('filament.admin.pages.ganti-mesin'));
        } catch (\Exception $e) {
            \Log::error('EditMachineReplacement Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            Notification::make()
                ->title('❌ Gagal Memperbarui Data')
                ->body($e->getMessage())
                ->danger()
                ->duration(10000)
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(['admin', 'manager']) ?? false;
    }
}
