<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CashLedger extends Model
{
    protected $fillable = [
        'cash_mutation_id',
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
    // Auto-generate no_surat & no_urut saat creating
    // Format: 01/VI/26
    // -------------------------------------------------------
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $bulanRomawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

            $tgl = $model->tanggal
                ? Carbon::parse($model->tanggal)
                : now();

            $bln    = $bulanRomawi[$tgl->month - 1];
            $thn    = $tgl->format('y');
            $urutan = self::whereYear('tanggal', $tgl->year)
                ->whereMonth('tanggal', $tgl->month)
                ->count() + 1;

            $model->no_urut  = $urutan;
            $model->no_surat = str_pad($urutan, 2, '0', STR_PAD_LEFT) . '/' . $bln . '/' . $thn;
        });
    }

    // -------------------------------------------------------
    // Saldo awal untuk bulan tertentu
    // = total masuk - total keluar SEBELUM bulan tersebut
    // -------------------------------------------------------
    public static function saldoAwalBulan(int $year, int $month): int
    {
        $batas = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();

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
}
