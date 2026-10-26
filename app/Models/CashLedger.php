<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CashLedger extends Model
{
    protected $fillable = [
        'cash_mutation_id',
        'cash_receipt_id',
        'no_urut',
        'no_surat',
        'tanggal',
        'keterangan',
        'uang_masuk',
        'uang_keluar',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal'     => 'date:Y-m-d',
        'uang_masuk'  => 'integer',
        'uang_keluar' => 'integer',
    ];

    // -------------------------------------------------------
    // Semi otomatis:
    // - Kas masuk  : no_surat SELALU kosong
    // - Kas keluar : no_surat boleh diisi manual, kalau kosong
    //                digenerate otomatis (format 01/X/26)
    // -------------------------------------------------------
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $tgl = $model->tanggal ? Carbon::parse($model->tanggal) : now();

            // No. urut baris (kas masuk + keluar) untuk kolom "No."
            $model->no_urut = ((int) self::whereYear('tanggal', $tgl->year)
                ->whereMonth('tanggal', $tgl->month)
                ->max('no_urut')) + 1;
        });

        static::saving(function (self $model) {
            // Kas masuk: tidak boleh ada no surat
            if ((int) $model->uang_masuk > 0) {
                $model->no_surat = null;
                return;
            }

            // Kas keluar: isi otomatis kalau kosong
            if (blank($model->no_surat)) {
                $tgl = $model->tanggal ? Carbon::parse($model->tanggal) : now();
                $model->no_surat = self::nomorSuratBerikutnya($tgl);
            }
        });
    }

    // -------------------------------------------------------
    // Nomor surat berikutnya untuk bulan dari $tanggal
    // Ambil angka terbesar yang sudah dipakai di bulan itu + 1
    // (aman dari duplikat kalau ada data dihapus)
    // -------------------------------------------------------
    public static function nomorSuratBerikutnya($tanggal = null): string
    {
        $bulanRomawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        $tgl = $tanggal ? Carbon::parse($tanggal) : now();

        $maks = (int) self::whereYear('tanggal', $tgl->year)
            ->whereMonth('tanggal', $tgl->month)
            ->whereNotNull('no_surat')
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(no_surat, '/', 1) AS UNSIGNED)) as maks")
            ->value('maks');

        return str_pad($maks + 1, 2, '0', STR_PAD_LEFT)
            . '/' . $bulanRomawi[$tgl->month - 1]
            . '/' . $tgl->format('y');
    }

    // -------------------------------------------------------
    // Saldo awal untuk bulan tertentu
    // = total masuk - total keluar SEBELUM bulan tersebut
    // -------------------------------------------------------
    public static function saldoAwalBulan(int $year, int $month): int
    {
        $batas = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        return (int) (
            self::where('tanggal', '<', $batas)->sum('uang_masuk') -
            self::where('tanggal', '<', $batas)->sum('uang_keluar')
        );
    }

    // -------------------------------------------------------
    // Scope: filter per bulan
    // -------------------------------------------------------
    public function scopeBulan(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal')
            ->orderBy('id');
    }

    public function cashMutation()
    {
        return $this->belongsTo(CashMutation::class, 'cash_mutation_id');
    }

    public function cashReceipt()
    {
        return $this->belongsTo(CashReceipt::class, 'cash_receipt_id');
    }
}
