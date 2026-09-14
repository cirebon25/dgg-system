<?php

namespace App\Filament\Resources\PartBorrowingHeaderResource\Pages;

use App\Filament\Resources\PartBorrowingHeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartBorrowingHeaders extends ListRecords
{
    protected static string $resource = PartBorrowingHeaderResource::class;

    public function mount(): void
    {
        parent::mount();

        // Cek konfirmasi password (berlaku 30 menit)
        if (
            ! session()->has('auth.password_confirmed_at') ||
            (time() - session('auth.password_confirmed_at') > 1800)
        ) {

            session(['url.intended' => request()->url()]);

            $this->redirect(route('password.confirm'));
            return;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Pinjam Part'),
        ];
    }
}