<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UppercaseAttributes;

class AccommodationClaim extends Model
{
    use HasFactory, SoftDeletes, UppercaseAttributes;

    protected $fillable = [
        'technician_id',
        'wilayah',
        'dari_tanggal',
        'sampai_tanggal',
        'biaya_transportasi',
        'konsumsi_karyawan',
        'pengeluaran_lain_1',
        'keterangan_lain_1',
        'pengeluaran_lain_2',
        'keterangan_lain_2',
        'total_biaya',
        'status',
        'catatan_penolakan',
    ];

    protected $casts = [
        'dari_tanggal'      => 'date',
        'sampai_tanggal'    => 'date',
        'biaya_transportasi'=> 'decimal:2',
        'konsumsi_karyawan' => 'decimal:2',
        'pengeluaran_lain_1'=> 'decimal:2',
        'pengeluaran_lain_2'=> 'decimal:2',
        'total_biaya'       => 'decimal:2',
    ];

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(AccommodationClaimVisit::class)->orderBy('no_urut');
    }

    // Auto hitung total sebelum save
    protected static function booted(): void
    {
        static::saving(function (self $claim) {
            $claim->total_biaya =
                $claim->biaya_transportasi +
                $claim->konsumsi_karyawan +
                $claim->pengeluaran_lain_1 +
                $claim->pengeluaran_lain_2;
        });
    }
}
