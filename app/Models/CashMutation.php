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

    /* -------------------------------------------------------
     * Generator No. Voucher (dipakai form & model)
     * Format: "urutan / bulan romawi / tahun 2 digit"
     * ------------------------------------------------------- */
    public static function generateNoVoucher($tanggal = null): array
    {
        $bulanRomawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        $tgl = $tanggal ? Carbon::parse($tanggal) : now();

        // pakai MAX (bukan COUNT) supaya aman kalau ada data yang dihapus
        $urutan = (int) self::whereYear('tanggal', $tgl->year)
            ->whereMonth('tanggal', $tgl->month)
            ->max('no_urut') + 1;

        return [
            'no_urut'    => $urutan,
            'no_voucher' => $urutan . ' / ' . $bulanRomawi[$tgl->month - 1] . ' / ' . $tgl->format('y'),
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->no_voucher)) {
                // Dikosongkan -> otomatis penuh
                $gen = self::generateNoVoucher($model->tanggal);
                $model->no_urut    = $gen['no_urut'];
                $model->no_voucher = $gen['no_voucher'];
            } else {
                // Diisi manual -> ambil no urut dari angka paling depan
                $model->no_urut = (int) trim(explode('/', $model->no_voucher)[0]);
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
        $prefix = $angka < 0 ? 'minus ' : '';
        $angka  = (int) round(abs($angka));

        if ($angka === 0) {
            return 'nol';
        }

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

        if ($angka < 12) {
            return $prefix . $satuan[$angka];
        }
        if ($angka < 20) {
            return $prefix . self::konversiTerbilang($angka - 10) . ' belas';
        }
        if ($angka < 100) {
            return $prefix . self::konversiTerbilang((int) ($angka / 10)) . ' puluh'
                . ($angka % 10 ? ' ' . self::konversiTerbilang($angka % 10) : '');
        }
        if ($angka < 200) {
            return $prefix . 'seratus'
                . ($angka - 100 ? ' ' . self::konversiTerbilang($angka - 100) : '');
        }
        if ($angka < 1_000) {
            return $prefix . self::konversiTerbilang((int) ($angka / 100)) . ' ratus'
                . ($angka % 100 ? ' ' . self::konversiTerbilang($angka % 100) : '');
        }
        if ($angka < 2_000) {
            return $prefix . 'seribu'
                . ($angka - 1_000 ? ' ' . self::konversiTerbilang($angka - 1_000) : '');
        }
        if ($angka < 1_000_000) {
            return $prefix . self::konversiTerbilang((int) ($angka / 1_000)) . ' ribu'
                . ($angka % 1_000 ? ' ' . self::konversiTerbilang($angka % 1_000) : '');
        }
        if ($angka < 1_000_000_000) {
            return $prefix . self::konversiTerbilang((int) ($angka / 1_000_000)) . ' juta'
                . ($angka % 1_000_000 ? ' ' . self::konversiTerbilang($angka % 1_000_000) : '');
        }

        return $prefix . self::konversiTerbilang((int) ($angka / 1_000_000_000)) . ' miliar'
            . ($angka % 1_000_000_000 ? ' ' . self::konversiTerbilang($angka % 1_000_000_000) : '');
    }

    public function getNoSuratFormatted(): string
    {
        if (empty($this->no_voucher)) {
            return '';
        }

        $parts = array_map('trim', explode('/', $this->no_voucher));

        return str_pad($parts[0], 2, '0', STR_PAD_LEFT) . '/' . ($parts[1] ?? '') . '/' . ($parts[2] ?? '');
    }
}