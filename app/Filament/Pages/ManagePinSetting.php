<?php

namespace App\Filament\Resources\PartBorrowingHeaderResource\Pages; // Sesuaikan namespace jika perlu, atau buat page murni

// Letakkan di namespace Pages atau Pages/Settings
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManagePinSetting extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'Ubah PIN Modul Pinjam';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static string $view = 'filament.pages.manage-pin-setting';

    public ?array $data = [];

    public function mount(): void
    {
        // Ambil PIN yang tersimpan di database, default '123456' jika belum diset
        $currentPin = DB::table('settings')->where('key', 'modul_pinjam_pin')->value('value') ?? '123456';

        $this->form->fill([
            // Kita tampilkan form kosong atau petunjuk
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Keamanan Modul Pinjam Part')
                    ->description('Ubah PIN rahasia yang digunakan untuk mengonfirmasi transaksi peminjaman part.')
                    ->schema([
                        TextInput::make('pin_baru')
                            ->label('PIN Baru')
                            ->password()
                            ->revealable()
                            ->required()
                            ->numeric()
                            ->minLength(4)
                            ->maxLength(10),

                        TextInput::make('konfirmasi_pin')
                            ->label('Ulangi PIN Baru')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('pin_baru'),
                    ])
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan PIN Baru')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Simpan PIN baru ke database (dalam bentuk teks biasa atau di-hash)
        DB::table('settings')->updateOrInsert(
            ['key' => 'modul_pinjam_pin'],
            ['value' => $data['pin_baru'], 'updated_at' => now()]
        );

        Notification::make()
            ->title('PIN Berhasil Diperbarui! ✅')
            ->success()
            ->send();
    }
}