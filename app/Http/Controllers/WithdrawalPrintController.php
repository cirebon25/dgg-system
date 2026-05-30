<?php

// app/Http/Controllers/WithdrawalPrintController.php

namespace App\Http\Controllers;

use App\Models\MachineWithdrawal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class WithdrawalPrintController extends Controller
{
    public function __invoke(int $id): Response
    {
        $record = MachineWithdrawal::with(['machine', 'customer'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('print.withdrawal', compact('record'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('penarikan-' . $record->id . '.pdf');
    }
}
