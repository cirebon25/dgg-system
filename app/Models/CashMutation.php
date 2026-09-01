<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CashMutation extends Model
{
    protected $fillable = [
        'no_voucher',
        'no_urut',
        'tanggal',
        'jenis_pembayaran',
        'total_jumlah',
        'terbilang',
        'pembuat',
        'pemeriksa',
        'penerima',
    ];

    protected $casts = [
        'tanggal'      => 'date:Y-m-d',
        'total_jumlah' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(CashMutationItem::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->no_voucher)) {
                $bulanRomawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

                $tgl = $model->tanggal
                    ? Carbon::parse($model->tanggal)
                    : now();

                $bln = $bulanRomawi[$tgl->month - 1];
                $thn = $tgl->format('y');

                $urutan = self::whereYear('tanggal', $tgl->year)
                    ->whereMonth('tanggal', $tgl->month)
                    ->count() + 1;

                $model->no_urut    = $urutan;
                $model->no_voucher = $urutan . ' / ' . $bln . ' / ' . $thn;
            }
        });

        // Catatan: validasi saldo real-time (validateSaldo) sengaja DICABUT.
        // SPM sekarang murni dokumen input + cetak, tidak lagi memblokir
        // berdasarkan sisa saldo Buku Kas. Pencatatan saldo resmi dilakukan
        // manual oleh admin di CashLedgerResource.
    }

    /* -------------------------------------------------------
     * Konversi angka → teks terbilang (Bahasa Indonesia)
     * ------------------------------------------------------- */
    public static function konversiTerbilang(float $angka): string
    {
        $angka  = (int) round(abs($angka));
        $prefix = $angka < 0 ? 'minus ' : '';

        $satuan = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
        ];

        if ($angka < 12)        return $prefix . $satuan[$angka];
        if ($angka < 20)        return $prefix . self::konversiTerbilang($angka - 10) . ' belas';
        if ($angka < 100)       return $prefix . self::konversiTerbilang((int)($angka / 10)) . ' puluh'
            . ($angka % 10 ? ' ' . self::konversiTerbilang($angka % 10) : '');
        if ($angka < 200)       return $prefix . 'seratus'
            . ($angka - 100 ? ' ' . self::konversiTerbilang($angka - 100) : '');
        if ($angka < 1_000)     return $prefix . self::konversiTerbilang((int)($angka / 100)) . ' ratus'
            . ($angka % 100 ? ' ' . self::konversiTerbilang($angka % 100) : '');
        if ($angka < 2_000)     return $prefix . 'seribu'
            . ($angka - 1_000 ? ' ' . self::konversiTerbilang($angka - 1_000) : '');
        if ($angka < 1_000_000)   return $prefix . self::konversiTerbilang((int)($angka / 1_000)) . ' ribu'
            . ($angka % 1_000 ? ' ' . self::konversiTerbilang($angka % 1_000) : '');
        if ($angka < 1_000_000_000) return $prefix . self::konversiTerbilang((int)($angka / 1_000_000)) . ' juta'
            . ($angka % 1_000_000 ? ' ' . self::konversiTerbilang($angka % 1_000_000) : '');

        return $prefix . self::konversiTerbilang((int)($angka / 1_000_000_000)) . ' miliar'
            . ($angka % 1_000_000_000 ? ' ' . self::konversiTerbilang($angka % 1_000_000_000) : '');
    }

    public function getNoSuratFormatted(): string
    {
        if (empty($this->no_voucher)) return '';
        $parts = array_map('trim', explode('/', $this->no_voucher));
        return str_pad($parts[0], 2, '0', STR_PAD_LEFT) . '/' . ($parts[1] ?? '') . '/' . ($parts[2] ?? '');
    }
}