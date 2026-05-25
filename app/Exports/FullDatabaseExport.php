<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Deployment;
use App\Models\Machine;
use App\Models\MachineReplacement;
use App\Models\PartBorrowing;
use App\Models\PartReturn;
use App\Models\ServiceLog;
use App\Models\Sparepart;
use App\Models\SparepartEntry;
use App\Models\Technician;
use App\Models\TechnicianStock;
use App\Models\TechnicianStockHistory;
use App\Models\Rayon;
use App\Models\TypeModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FullDatabaseExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        return [
            'Customer'          => new Sheets\CustomerSheet(),
            'Machine'           => new Sheets\MachineSheet(),
            'Deployment'        => new Sheets\DeploymentSheet(),
            'Service Log'       => new Sheets\ServiceLogSheet(),
            'Technician'        => new Sheets\TechnicianSheet(),
            'Rayon'             => new Sheets\RayonSheet(),
            'Sparepart'         => new Sheets\SparepartSheet(),
            'Sparepart Entry'   => new Sheets\SparepartEntrySheet(),
            'Part Borrowing'    => new Sheets\PartBorrowingSheet(),
            'Part Return'       => new Sheets\PartReturnSheet(),
            'Technician Stock'  => new Sheets\TechnicianStockSheet(),
            'Stock History'     => new Sheets\TechnicianStockHistorySheet(),
            'Machine Replace'   => new Sheets\MachineReplacementSheet(),
            'Type Model'        => new Sheets\TypeModelSheet(),
        ];
    }
}
