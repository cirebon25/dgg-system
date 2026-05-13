<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ServiceLog;
use App\Models\Deployment;
use App\Models\Machine;
use App\Models\Customer;
use App\Exports\ArchivedCustomerExport;
use App\Exports\ActiveCustomerExport; // <--- PASTIKAN INI DIPANGGIL
use Maatwebsite\Excel\Facades\Excel;

class ArchiveAllData extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Arsip All Data';
    protected static ?string $title = 'Pusat Monitoring & Arsip';
    protected static ?string $navigationGroup = 'SISTEM ARSIP';

    protected static string $view = 'filament.pages.archive-all-data';

    /**
     * Ambil data Aktif & Arsip
     */
    public function getViewData(): array
    {
        return [
            // DATA ARSIP
            'countServiceLog' => ServiceLog::onlyTrashed()->count(),
            'countMachine'    => Machine::onlyTrashed()->count(),
            'countDeployment' => Deployment::onlyTrashed()->count(),

            // DATA AKTIF
            'activeServiceLog' => ServiceLog::count(),
            'activeMachine'    => Machine::count(),
            'activeDeployment' => Deployment::count(),
            'activeCustomer'   => Customer::count(),

            'archivedCustomers' => Customer::onlyTrashed()
                ->with(['machines' => function($q) {
                    $q->withTrashed();
                }])
                ->get(),
        ];
    }

    /**
     * 1. DOWNLOAD EXCEL ARSIP (Tombol Hijau)
     */
    public function downloadExcel()
    {
        return Excel::download(
            new ArchivedCustomerExport, 
            'Arsip_Customer_DGG_' . now()->format('d-m-Y') . '.xlsx'
        );
    }

    /**
     * 2. DOWNLOAD EXCEL AKTIF (Tombol Biru)
     * FUNGSI YANG TADI HILANG ADA DI SINI BOSS!
     */
    public function downloadActiveExcel()
    {
        return Excel::download(
            new ActiveCustomerExport, 
            'Data_Customer_Aktif_DGG_' . now()->format('d-m-Y') . '.xlsx'
        );
    }
}