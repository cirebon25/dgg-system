<?php

namespace App\Filament\Pages;

use App\Exports\FullDatabaseExport;
use App\Models\Customer;
use App\Models\Deployment;
use App\Models\Machine;
use App\Models\ServiceLog;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;

class ArchiveAllData extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Arsip All Data';
    protected static ?string $title = 'Pusat Monitoring & Arsip';
    protected static ?string $navigationGroup = 'Sistem Arsip';
    protected static string $view = 'filament.pages.archive-all-data';

    public function getViewData(): array
    {
        return [
            'countServiceLog'   => ServiceLog::onlyTrashed()->count(),
            'countMachine'      => Machine::onlyTrashed()->count(),
            'countDeployment'   => Deployment::onlyTrashed()->count(),
            'activeServiceLog'  => ServiceLog::count(),
            'activeMachine'     => Machine::count(),
            'activeDeployment'  => Deployment::count(),
            'activeCustomer'    => Customer::count(),

            /* |--------------------------------------------------------------------------
               | 🌟 FIX: Eager loading dialihkan ke relasi customer yang valid (technician & rayon)
               |--------------------------------------------------------------------------
            */
            'archivedCustomers' => Customer::onlyTrashed()
                ->with([
                    'technician', // Panggil teknisi lewat customer (Aman Jaya)
                    'rayon',      // Panggil rayon lewat customer
                    'machines' => fn($q) => $q->withTrashed() // Panggil mesin milik customer
                ])
                ->get(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadFullDB')
                ->label('Download Semua Data (Excel)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Download Seluruh Database')
                ->modalDescription('File Excel akan berisi semua tabel dalam sheet terpisah. Proses ini mungkin memakan waktu beberapa detik.')
                ->modalSubmitActionLabel('Ya, Download Sekarang')
                ->action(function () {
                    $filename = 'DGG_FullBackup_' . now()->format('d-m-Y_His') . '.xlsx';
                    return Excel::download(new FullDatabaseExport, $filename);
                }),
        ];
    }
}
