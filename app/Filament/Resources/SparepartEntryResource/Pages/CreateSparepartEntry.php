<?php

namespace App\Filament\Resources\SparepartEntryResource\Pages;

use App\Filament\Resources\SparepartEntryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateSparepartEntry extends CreateRecord
{
    protected static string $resource = SparepartEntryResource::class;

    /**
     * 🌟 MANTRA DEWA BATCH ENTRY: Pecah isi repeater menjadi record mandiri di DB
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $items = $data['items_masuk'] ?? [];
        
        $firstRecord = null;
        $affectedPartIds = [];

        // Loop dan pecah semua barang inputan menjadi baris data log masing-masing
        foreach ($items as $index => $item) {
            $recordData = [
                'sparepart_id' => $item['sparepart_id'],
                'jumlah' => $item['jumlah'],
                'supplier' => $data['supplier'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Masukkan data ke tabel log sparepart_entries
            $newRecord = static::getModel()::create($recordData);
            
            // Filament membutuhkan 1 model master untuk dikembalikan sebagai tanda sukses redirect
            if ($index === 0) {
                $firstRecord = $newRecord;
            }
            
            $affectedPartIds[] = $item['sparepart_id'];
        }

        // 🌟 AUTOMATIC GUDANG SYNC: Hitung ulang & update kolom 'stok' fisik di master spareparts
        foreach (array_unique($affectedPartIds) as $spId) {
            $masuk = (int) DB::table('sparepart_entries')->where('sparepart_id', $spId)->sum('jumlah');
            $pinjam = (int) DB::table('part_borrowings')->where('sparepart_id', $spId)->sum('jumlah');
            $pasang = (int) DB::table('deployment_sparepart')->where('sparepart_id', $spId)->sum('jumlah');
            
            // Kalkulasi real stok gudang pusat saat ini
            $stokAkhirReal = $masuk - ($pinjam + $pasang);

            // Suntik langsung angkanya ke tabel fisik master barang
            DB::table('spareparts')
                ->where('id', $spId)
                ->update(['stok' => $stokAkhirReal]);
        }

        // Jika user tidak mengisi item sama sekali, kembalikan ke sistem bawaan agar ditolak
        return $firstRecord ?? static::getModel()::create($data);
    }
}