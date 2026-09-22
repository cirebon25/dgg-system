<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoicePrintController extends Controller
{
    public function print(Invoice $invoice)
    {
        // Load relasi customer jika diperlukan di view
        $invoice->load('customer');

        // Mengarahkan ke file Blade view untuk cetak (misal: resources/views/invoices/print.blade.php)
        return view('invoices.print', compact('invoice'));
    }
}
