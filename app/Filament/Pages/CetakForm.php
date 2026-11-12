<?php

namespace App\Filament\Pages;

use App\Models\PrintForm;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class CetakForm extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Cetak Form';
    protected static ?string $navigationGroup = 'Pusat Cetak';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.cetak-form';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'teknisi', 'keuangan', 'manager']);
    }

    protected function getViewData(): array
    {
        $disk = Storage::disk('public');

        $forms = PrintForm::latest()->get()->map(function (PrintForm $form) use ($disk) {
            $ada = $form->file_path && $disk->exists($form->file_path);

            $url = null;
            if ($ada) {
                $encoded = implode('/', array_map('rawurlencode', explode('/', $form->file_path)));
                $url = asset('storage/' . $encoded);
            }

            return [
                'nama'       => $form->judul,
                'keterangan' => (string) $form->deskripsi,
                'url'        => $url,
                'search'     => mb_strtolower($form->judul . ' ' . $form->deskripsi),
            ];
        })->all();

        return ['forms' => $forms];
    }
}
