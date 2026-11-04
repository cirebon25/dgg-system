<?php

namespace App\Http\Controllers;

use App\Models\BukuKontrak;
use App\Models\Customer;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BukuKontrakController extends Controller
{
    public function index(Request $request)
    {
        $data = BukuKontrak::with(['customer', 'technician'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->q, fn($q, $s) => $q->where('no_kontrak', 'like', "%{$s}%"))
            ->latest('tgl_dtg')
            ->paginate(20)
            ->withQueryString();

        return view('buku-kontrak.index', compact('data'));
    }

    public function create()
    {
        return view('buku-kontrak.form', [
            'row'         => new BukuKontrak(['status' => 'depo_cirebon', 'tgl_status' => now()]),
            'customers'   => Customer::orderBy('nama_customer')->get(),
            'technicians' => Technician::orderBy('nama_technician')->get(),
        ]);
    }

    public function store(Request $request)
    {
        BukuKontrak::create($this->validated($request));

        return redirect()->route('buku-kontrak.index')->with('success', 'Data tersimpan');
    }

    public function edit(BukuKontrak $bukuKontrak)
    {
        return view('buku-kontrak.form', [
            'row'         => $bukuKontrak,
            'customers'   => Customer::orderBy('nama_customer')->get(),
            'technicians' => Technician::orderBy('nama_technician')->get(),
        ]);
    }

    public function update(Request $request, BukuKontrak $bukuKontrak)
    {
        $bukuKontrak->update($this->validated($request));

        return redirect()->route('buku-kontrak.index')->with('success', 'Data diperbarui');
    }

    public function destroy(BukuKontrak $bukuKontrak)
    {
        $bukuKontrak->delete();

        return back()->with('success', 'Data dihapus');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'no_kontrak'    => 'required|string|max:100',
            'customer_id'   => 'nullable|exists:customers,id',
            'tgl_dtg'       => 'required|date',
            'technician_id' => 'nullable|exists:technicians,id',
            'tgl_dibawa'    => 'nullable|date',
            'status'        => ['required', Rule::in(array_keys(BukuKontrak::statusLabels()))],
            'tgl_status'    => 'required|date',
            'keterangan'    => 'nullable|string',
        ]);
    }
}
