<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    // Proteksi kolom database agar aman saat insert/update
    protected $fillable = [
        'nama_sparepart',
        'code_part',
        'no_part',
        'saldo_masuk',
        'saldo_keluar',
        'stok',
        'harga_beli',
        'keterangan',
    ];

    // Relasi ke Service Log Sparepart
    public function serviceLogSpareparts()
    {
        return $this->hasMany(ServiceLogSparepart::class);
    }

    // --- LOGIKA KALKULATOR GUDANG OTOMATIS (ANTI-TUMPANG TINDID) ---
    protected static function booted()
    {
        // 1. KETIKA BARANG BARU PERTAMA KALI DIBUAT (CREATE)
        static::creating(function ($sparepart) {
            $masuk = (int)($sparepart->saldo_masuk ?? 0);
            $keluar = (int)($sparepart->saldo_keluar ?? 0);
            
            // Stok awal langsung dikalkulasi
            $sparepart->stok = $masuk - $keluar;
        });

        // 2. KETIKA STOK LAMA MAU DITAMBAH KULAKAN BARU (EDIT / UPDATE)
        static::updating(function ($sparepart) {
            // Kita cek, apakah admin mengetik angka baru di kolom saldo_masuk?
            if ($sparepart->isDirty('saldo_masuk')) {
                // Ambil nilai stok terakhir sebelum di-save
                $stokLama = (int)$sparepart->getOriginal('stok');
                
                // Ambil angka tambahan kulakan yang baru diketik admin di form
                $tambahanStok = (int)$sparepart->saldo_masuk;

                // KALKULASI: Stok Akhir Gudang = Stok Lama + Tambahan Baru
                $sparepart->stok = $stokLama + $tambahanStok;

                // Agar data total barang masuk sepanjang masa akurat, kita akumulasikan di DB
                $totalSaldoMasukLama = (int)$sparepart->getOriginal('saldo_masuk');
                $sparepart->saldo_masuk = $totalSaldoMasukLama + $tambahanStok;
            }
        });
    }
}