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
    protected static ?int $navigationSort = 15;
    protected static string $view = 'filament.pages.archive-all-data';

    public int $countServiceLog  = 0;
    public int $countMachine     = 0;
    public int $countDeployment  = 0;
    public int $activeServiceLog = 0;
    public int $activeMachine    = 0;
    public int $activeDeployment = 0;
    public int $activeCustomer   = 0;
    public $archivedCustomers;

    public function mount(): void
    {
        $this->countServiceLog  = ServiceLog::onlyTrashed()->count();
        $this->countMachine     = Machine::onlyTrashed()->count();
        $this->countDeployment  = Deployment::onlyTrashed()->count();
        $this->activeServiceLog = ServiceLog::count();
        $this->activeMachine    = Machine::count();
        $this->activeDeployment = Deployment::count();
        $this->activeCustomer   = Customer::count();

        $this->archivedCustomers = Customer::onlyTrashed()
            ->with([
                'technician',
                'rayon',
                'machines' => fn($q) => $q->withTrashed()
            ])
            ->get();
    }

    public function downloadActiveExcel()
    {
        $filename = 'DGG_DataAktif_' . now()->format('d-m-Y_His') . '.xlsx';
        return Excel::download(new FullDatabaseExport, $filename);
    }

    public function downloadExcel()
    {
        $filename = 'DGG_DataArsip_' . now()->format('d-m-Y_His') . '.xlsx';
        return Excel::download(new FullDatabaseExport, $filename);
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