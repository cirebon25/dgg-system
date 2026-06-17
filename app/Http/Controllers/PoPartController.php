<?php

namespace App\Http\Controllers;

use App\Models\PoPart;

class PoPartController extends Controller
{
    public function print($id)
    {
        $po = PoPart::with('items.sparepart')->findOrFail($id);
        return view('reports.po-part-print', compact('po'));
    }
}